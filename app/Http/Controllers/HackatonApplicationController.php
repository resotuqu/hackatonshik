<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Hackaton\RecordHackatonAnalyticsEvent;
use App\Enums\ApplicationStatus;
use App\Events\HackatonApplicationChanged;
use App\Http\Requests\BulkUpdateHackatonApplicationsRequest;
use App\Http\Requests\StoreHackatonApplicationRequest;
use App\Http\Requests\UpdateApplicationStatusRequest;
use App\Models\Hackaton;
use App\Models\HackatonApplication;
use App\Models\HackatonCase;
use App\Models\Team;
use App\Models\User;
use App\Notifications\ApplicationStatusUpdated;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;

class HackatonApplicationController extends Controller
{
    public function store(StoreHackatonApplicationRequest $request, RecordHackatonAnalyticsEvent $recordEvent): RedirectResponse
    {
        Gate::authorize('create', HackatonApplication::class);

        $validated = $request->validated();
        $hackaton = Hackaton::query()->findOrFail($validated['hackaton_id']);

        $application = HackatonApplication::query()->firstOrNew([
            'team_id' => $validated['team_id'],
            'hackaton_id' => $hackaton->id,
        ]);

        $casesCountWhenApplied = $hackaton->cases()->count();

        $application->fill([
            'message' => $validated['message'] ?? null,
            'hackaton_cases_count_when_applied' => $casesCountWhenApplied,
            'status' => ApplicationStatus::PENDING,
            'reviewed_at' => null,
            'reviewed_by' => null,
        ]);
        $application->save();
        $recordEvent->handle($hackaton, 'application_submitted', $request->user(), [
            'team_id' => $application->team_id,
            'application_id' => $application->id,
        ]);
        event(new HackatonApplicationChanged(
            teamId: (int) $application->team_id,
            hackatonId: (int) $hackaton->id,
            organizerId: (int) $hackaton->user_id,
        ));

        return back()->with('success', 'Заявка команды на хакатон подана.');
    }

    public function update(UpdateApplicationStatusRequest $request, HackatonApplication $application): RedirectResponse
    {
        Gate::authorize('update', $application);
        $status = ApplicationStatus::from($request->validated('status'));
        /** @var User $reviewer */
        $reviewer = $request->user();

        DB::transaction(function () use ($application, $status, $reviewer): void {
            $lockedApplication = HackatonApplication::query()
                ->lockForUpdate()
                ->findOrFail($application->id);

            if ($this->resolveApplicationStatus($lockedApplication) !== ApplicationStatus::PENDING) {
                abort(422, 'Заявка уже рассмотрена.');
            }

            if ($status === ApplicationStatus::ACCEPTED) {
                $this->acceptApplication($lockedApplication, $reviewer);

                return;
            }

            $lockedApplication->markAsRejected($reviewer);
        });

        $flashMessage = $status === ApplicationStatus::ACCEPTED
            ? 'Заявка команды принята.'
            : 'Заявка команды отклонена.';

        $updatedApplication = HackatonApplication::query()
            ->with(['hackaton', 'team'])
            ->findOrFail($application->id);
        $organizerId = Hackaton::query()
            ->whereKey($updatedApplication->hackaton_id)
            ->value('user_id');
        $this->notifyTeamAboutStatus($updatedApplication);
        event(new HackatonApplicationChanged(
            teamId: (int) $application->team_id,
            hackatonId: (int) $application->hackaton_id,
            organizerId: $organizerId !== null ? (int) $organizerId : null,
            invalidateHomeFeatured: $status === ApplicationStatus::ACCEPTED,
        ));

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

        $this->maybeAssignTeamToSingleCase($team, $lockedApplication);

        $lockedApplication->markAsAccepted($reviewer);
    }

    /**
     * If the hackathon had at least one case when the team applied and now has exactly one case,
     * attach the team to that case.
     */
    private function maybeAssignTeamToSingleCase(Team $team, HackatonApplication $lockedApplication): void
    {
        $whenApplied = $lockedApplication->hackaton_cases_count_when_applied;
        if ($whenApplied === null || $whenApplied < 1) {
            return;
        }

        $hackatonId = $lockedApplication->hackaton_id;

        $caseIds = HackatonCase::query()
            ->where('hackaton_id', $hackatonId)
            ->lockForUpdate()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->pluck('id');

        if ($caseIds->count() !== 1) {
            return;
        }

        $team->update([
            'hackaton_case_id' => $caseIds->first(),
        ]);
    }

    public function destroy(HackatonApplication $application): RedirectResponse
    {
        Gate::authorize('delete', $application);
        $teamId = (int) $application->team_id;
        $hackaton = Hackaton::query()->find($application->hackaton_id, ['id', 'user_id']);
        $application->delete();
        event(new HackatonApplicationChanged(
            teamId: $teamId,
            hackatonId: $hackaton?->id !== null ? (int) $hackaton->id : null,
            organizerId: $hackaton?->user_id !== null ? (int) $hackaton->user_id : null,
        ));

        return back()->with('success', 'Заявка удалена.');
    }

    public function bulkUpdate(BulkUpdateHackatonApplicationsRequest $request, Hackaton $hackaton): RedirectResponse
    {
        if ((int) $hackaton->user_id !== (int) $request->user()->id) {
            abort(403);
        }

        $validated = $request->validated();
        $applicationIds = $validated['application_ids'];
        $status = ApplicationStatus::from($validated['status']);
        /** @var User $reviewer */
        $reviewer = $request->user();

        DB::transaction(function () use ($hackaton, $applicationIds, $status, $reviewer): void {
            $applications = HackatonApplication::query()
                ->where('hackaton_id', $hackaton->id)
                ->whereIn('id', $applicationIds)
                ->where('status', ApplicationStatus::PENDING)
                ->lockForUpdate()
                ->get();

            foreach ($applications as $application) {
                if ($status === ApplicationStatus::ACCEPTED) {
                    $this->acceptApplication($application, $reviewer);

                    $this->notifyTeamAboutStatus(
                        HackatonApplication::query()->with(['hackaton', 'team'])->findOrFail($application->id)
                    );
                    event(new HackatonApplicationChanged(
                        teamId: (int) $application->team_id,
                        hackatonId: (int) $application->hackaton_id,
                        organizerId: (int) $hackaton->user_id,
                        invalidateHomeFeatured: true,
                    ));

                    continue;
                }

                $application->markAsRejected($reviewer);
                $this->notifyTeamAboutStatus(
                    HackatonApplication::query()->with(['hackaton', 'team'])->findOrFail($application->id)
                );
                event(new HackatonApplicationChanged(
                    teamId: (int) $application->team_id,
                    hackatonId: (int) $application->hackaton_id,
                    organizerId: (int) $hackaton->user_id,
                ));
            }
        });

        return back()->with('success', 'Групповая модерация выполнена.');
    }

    private function notifyTeamAboutStatus(HackatonApplication $application): void
    {
        $team = Team::query()
            ->with(['roles:id,team_id,user_id', 'roles.user:id,email'])
            ->find($application->team_id);
        $hackaton = Hackaton::query()->find($application->hackaton_id);

        if (! $team || ! $hackaton) {
            return;
        }

        $participantIds = $team->roles
            ->pluck('user_id')
            ->filter()
            ->push($team->user_id)
            ->unique()
            ->values();

        if ($participantIds->isEmpty()) {
            return;
        }

        $users = User::query()->whereIn('id', $participantIds)->get();

        if ($users->isEmpty()) {
            return;
        }

        Notification::send(
            $users,
            new ApplicationStatusUpdated($hackaton, $team, $this->resolveApplicationStatus($application))
        );
    }

    private function resolveApplicationStatus(HackatonApplication $application): ApplicationStatus
    {
        return ApplicationStatus::from((string) $application->getRawOriginal('status'));
    }
}
