<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\ApplicationStatus;
use App\Http\Requests\StoreTeamApplicationRequest;
use App\Http\Requests\UpdateApplicationStatusRequest;
use App\Models\TeamApplication;
use App\Models\TeamRole;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class TeamApplicationController extends Controller
{
    public function store(StoreTeamApplicationRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $roleId = (int) $validated['team_role_id'];
        TeamRole::query()->findOrFail($roleId);

        $application = TeamApplication::query()->firstOrNew([
            'user_id' => Auth::id(),
            'team_role_id' => $roleId,
        ]);

        $application->fill([
            'message' => $validated['message'] ?? null,
            'status' => ApplicationStatus::PENDING,
            'reviewed_at' => null,
            'reviewed_by' => null,
        ]);
        $application->save();

        return back()->with('success', 'Заявка подана. Ожидайте решения создателя команды.');
    }

    public function update(UpdateApplicationStatusRequest $request, TeamApplication $application): RedirectResponse
    {
        Gate::authorize('update', $application);
        $status = $request->validated('status');
        /** @var User $reviewer */
        $reviewer = $request->user();

        DB::transaction(function () use ($application, $status, $reviewer): void {
            $lockedApplication = TeamApplication::query()
                ->with('teamRole')
                ->lockForUpdate()
                ->findOrFail($application->id);

            if (! $lockedApplication->status->isPending()) {
                abort(422, 'Заявка уже рассмотрена.');
            }

            if ($status === ApplicationStatus::ACCEPTED->value) {
                $this->acceptApplication($lockedApplication, $reviewer);

                return;
            }

            $lockedApplication->markAsRejected($reviewer);
        });

        $flashMessage = $status === ApplicationStatus::ACCEPTED->value
            ? 'Заявка принята. Участник добавлен в команду.'
            : 'Заявка отклонена.';

        return back()->with('success', $flashMessage);
    }

    private function acceptApplication(TeamApplication $lockedApplication, User $reviewer): void
    {
        $lockedRole = TeamRole::query()
            ->lockForUpdate()
            ->findOrFail($lockedApplication->team_role_id);

        if ($lockedRole->user_id !== null) {
            abort(422, 'Роль уже занята.');
        }

        $lockedRole->update([
            'user_id' => $lockedApplication->user_id,
        ]);

        $lockedApplication->markAsAccepted($reviewer);

        TeamApplication::query()
            ->where('team_role_id', $lockedApplication->team_role_id)
            ->where('id', '!=', $lockedApplication->id)
            ->where('status', ApplicationStatus::PENDING)
            ->update([
                'status' => ApplicationStatus::REJECTED,
                'reviewed_at' => now(),
                'reviewed_by' => $reviewer->id,
            ]);
    }

    public function destroy(TeamApplication $application): RedirectResponse
    {
        Gate::authorize('delete', $application);
        $application->delete();

        return back()->with('success', 'Заявка удалена.');
    }
}
