<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\ApplicationStatus;
use App\Http\Requests\BulkUpdateHackatonApplicationsRequest;
use App\Http\Requests\StoreHackatonApplicationRequest;
use App\Http\Requests\UpdateApplicationStatusRequest;
use App\Models\Hackaton;
use App\Models\HackatonApplication;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class HackatonApplicationController extends Controller
{
    public function store(StoreHackatonApplicationRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $hackaton = Hackaton::query()->findOrFail($validated['hackaton_id']);

        $application = HackatonApplication::query()->firstOrNew([
            'team_id' => $validated['team_id'],
            'hackaton_id' => $hackaton->id,
        ]);

        $application->fill([
            'message' => $validated['message'] ?? null,
            'status' => ApplicationStatus::PENDING,
            'reviewed_at' => null,
            'reviewed_by' => null,
        ]);
        $application->save();

        return back()->with('success', 'Заявка команды на хакатон подана.');
    }

    public function update(UpdateApplicationStatusRequest $request, HackatonApplication $application): RedirectResponse
    {
        Gate::authorize('update', $application);
        $status = $request->validated('status');
        /** @var User $reviewer */
        $reviewer = $request->user();

        DB::transaction(function () use ($application, $status, $reviewer): void {
            $lockedApplication = HackatonApplication::query()
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
            ? 'Заявка команды принята.'
            : 'Заявка команды отклонена.';

        return back()->with('success', $flashMessage);
    }

    private function acceptApplication(HackatonApplication $lockedApplication, User $reviewer): void
    {
        $team = Team::query()
            ->lockForUpdate()
            ->findOrFail($lockedApplication->team_id);

        $team->update([
            'hackaton_id' => $lockedApplication->hackaton_id,
        ]);

        $lockedApplication->markAsAccepted($reviewer);
    }

    public function destroy(HackatonApplication $application): RedirectResponse
    {
        Gate::authorize('delete', $application);
        $application->delete();

        return back()->with('success', 'Заявка удалена.');
    }

    public function bulkUpdate(BulkUpdateHackatonApplicationsRequest $request, Hackaton $hackaton): RedirectResponse
    {
        if ((int) $hackaton->user_id !== (int) $request->user()->id) {
            abort(403);
        }

        $validated = $request->validated();
        $applicationIds = $validated['application_ids'];
        $status = $validated['status'];
        /** @var User $reviewer */
        $reviewer = $request->user();

        $applications = HackatonApplication::query()
            ->where('hackaton_id', $hackaton->id)
            ->whereIn('id', $applicationIds)
            ->where('status', ApplicationStatus::PENDING)
            ->get();

        foreach ($applications as $application) {
            if ($status === ApplicationStatus::ACCEPTED->value) {
                $this->acceptApplication($application, $reviewer);

                continue;
            }

            $application->markAsRejected($reviewer);
        }

        return back()->with('success', 'Групповая модерация выполнена.');
    }
}
