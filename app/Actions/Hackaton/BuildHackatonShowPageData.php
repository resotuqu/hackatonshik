<?php

declare(strict_types=1);

namespace App\Actions\Hackaton;

use App\Enums\ApplicationStatus;
use App\Models\Hackaton;
use App\Models\HackatonCase;
use App\Models\HackatonCaseScore;
use App\Models\HackatonCaseSubmission;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

final class BuildHackatonShowPageData
{
    /**
     * @return array<string, mixed>
     */
    public function build(Hackaton $hackaton, Request $request): array
    {
        $user = Auth::user();

        $isOrganizer = $user !== null && Gate::forUser($user)->allows('update', $hackaton);
        $isAssignedJudge = $user !== null && $hackaton->isJudge($user);
        $needsOrganizationInsights = $isOrganizer || $isAssignedJudge;

        $hackaton->loadShowRelations();
        $hackaton->setRelation('announcements', $hackaton->announcements()
            ->with('images')
            ->when(! $isOrganizer, fn (Builder $query) => $query
                ->where('is_draft', false)
                ->whereNotNull('published_at')
                ->where('published_at', '<=', now()))
            ->get());
        $hackaton->setRelation('cases', $hackaton->cases()
            ->with(['fields', 'submissions.answers', 'submissions.score'])
            ->when(! $isOrganizer, fn (Builder $query) => $query
                ->where('is_published', true)
                ->where(function (Builder $scheduleQuery): void {
                    $scheduleQuery
                        ->whereNull('publish_at')
                        ->orWhere('publish_at', '<=', now());
                }))
            ->get());
        if ($isOrganizer) {
            $hackaton->setRelation('certificates', $hackaton->certificates()->with('user')->get());
        } else {
            $hackaton->setRelation('certificates', collect());
        }

        $this->hydrateHackatonApplicationsRelation($hackaton, $user, $isOrganizer);

        $availableTeams = $this->resolveAvailableTeams();
        $submitterTeams = $this->resolveSubmitterTeams($hackaton);
        $participantUsers = $isOrganizer
            ? $this->resolveParticipantUsers($hackaton)
            : collect();
        $applicationStatusFilter = $request->string('applications_status')->toString();
        $applications = $isOrganizer
            ? $hackaton->applications()
                ->with(['team', 'reviewer'])
                ->when($applicationStatusFilter !== '', fn (Builder $query) => $query->where('status', $applicationStatusFilter))
                ->latest()
                ->get()
            : collect();

        $metrics = $needsOrganizationInsights
            ? $this->buildMetrics($hackaton)
            : $this->emptyMetrics();
        $leaderboard = $needsOrganizationInsights
            ? $this->buildLeaderboard($hackaton)
            : collect();

        $judgeCandidates = $isOrganizer
            ? User::query()
                ->where('role', 'judge')
                ->orderBy('fio')
                ->get(['id', 'fio', 'email', 'nickname'])
            : collect();

        $pendingJudgeInvitations = $isOrganizer
            ? $hackaton->judgeInvitations()
                ->where('status', 'pending')
                ->latest()
                ->get()
            : collect();

        return [
            'isOrganizer' => $isOrganizer,
            'isAssignedJudge' => $isAssignedJudge,
            'availableTeams' => $availableTeams,
            'submitterTeams' => $submitterTeams,
            'participantUsers' => $participantUsers,
            'applications' => $applications,
            'applicationStatusFilter' => $applicationStatusFilter,
            'metrics' => $metrics,
            'leaderboard' => $leaderboard,
            'judgeCandidates' => $judgeCandidates,
            'pendingJudgeInvitations' => $pendingJudgeInvitations,
        ];
    }

    /**
     * @return array<string, int|float>
     */
    private function buildMetrics(Hackaton $hackaton): array
    {
        $statusCounts = $hackaton->applications()
            ->selectRaw('status, COUNT(*) as c')
            ->groupBy('status')
            ->pluck('c', 'status');

        $submissionTable = (new HackatonCaseSubmission)->getTable();
        $caseTable = (new HackatonCase)->getTable();
        $scoreTable = (new HackatonCaseScore)->getTable();

        $submissionStats = DB::table($submissionTable)
            ->join($caseTable, "{$caseTable}.id", '=', "{$submissionTable}.hackaton_case_id")
            ->leftJoin($scoreTable, "{$scoreTable}.hackaton_case_submission_id", '=', "{$submissionTable}.id")
            ->where("{$caseTable}.hackaton_id", $hackaton->id)
            ->selectRaw('COUNT(*) as submissions_total, SUM(CASE WHEN '.$scoreTable.'.id IS NOT NULL THEN 1 ELSE 0 END) as submissions_scored')
            ->first();

        $submissionsTotal = (int) ($submissionStats?->submissions_total ?? 0);
        $submissionsScored = (int) ($submissionStats?->submissions_scored ?? 0);

        $metrics = [
            'applications_total' => (int) $statusCounts->sum(),
            'applications_pending' => (int) ($statusCounts[ApplicationStatus::PENDING->value] ?? 0),
            'applications_accepted' => (int) ($statusCounts[ApplicationStatus::ACCEPTED->value] ?? 0),
            'applications_rejected' => (int) ($statusCounts[ApplicationStatus::REJECTED->value] ?? 0),
            'submissions_total' => $submissionsTotal,
            'submissions_scored' => $submissionsScored,
        ];
        $metrics['submissions_scored_percent'] = $metrics['submissions_total'] > 0
            ? (int) round(($metrics['submissions_scored'] / $metrics['submissions_total']) * 100)
            : 0;

        return $metrics;
    }

    /**
     * @return Collection<int, array{team: ?Team, total_score: int, max_score: int, progress_percent: int}>
     */
    private function buildLeaderboard(Hackaton $hackaton): Collection
    {
        $submissionTable = (new HackatonCaseSubmission)->getTable();
        $caseTable = (new HackatonCase)->getTable();
        $scoreTable = (new HackatonCaseScore)->getTable();

        $rows = DB::table($submissionTable)
            ->join($caseTable, "{$caseTable}.id", '=', "{$submissionTable}.hackaton_case_id")
            ->join($scoreTable, "{$scoreTable}.hackaton_case_submission_id", '=', "{$submissionTable}.id")
            ->where("{$caseTable}.hackaton_id", $hackaton->id)
            ->whereNotNull("{$submissionTable}.team_id")
            ->groupBy("{$submissionTable}.team_id")
            ->orderByDesc(DB::raw('SUM('.$scoreTable.'.score)'))
            ->selectRaw(
                "{$submissionTable}.team_id as team_id, SUM({$scoreTable}.score) as total_score, SUM({$scoreTable}.max_score) as max_score"
            )
            ->get();

        if ($rows->isEmpty()) {
            return collect();
        }

        $teams = Team::query()
            ->whereIn('id', $rows->pluck('team_id'))
            ->get()
            ->keyBy('id');

        return $rows->map(function ($row) use ($teams): array {
            $team = $teams->get($row->team_id);
            $totalScore = (int) $row->total_score;
            $maxScore = (int) $row->max_score;

            return [
                'team' => $team,
                'total_score' => $totalScore,
                'max_score' => $maxScore,
                'progress_percent' => $maxScore > 0 ? (int) round(($totalScore / $maxScore) * 100) : 0,
            ];
        })->values();
    }

    /**
     * @return Collection<int, Team>
     */
    private function resolveAvailableTeams(): Collection
    {
        if (! Auth::check()) {
            return collect();
        }

        return Auth::user()->teams()->select(['id', 'title'])->orderBy('title')->get();
    }

    /**
     * @return Collection<int, Team>
     */
    private function resolveSubmitterTeams(Hackaton $hackaton): Collection
    {
        if (! Auth::check()) {
            return collect();
        }

        return $hackaton->teams()
            ->where(function (Builder $query): void {
                $query
                    ->where('teams.user_id', Auth::id())
                    ->orWhereHas('roles', function (Builder $rolesQuery): void {
                        $rolesQuery->where('team_roles.user_id', Auth::id());
                    });
            })
            ->orderBy('title')
            ->get(['teams.id', 'teams.title']);
    }

    /**
     * @return Collection<int, User>
     */
    private function resolveParticipantUsers(Hackaton $hackaton): Collection
    {
        /** @var Collection<int, User> $participants */
        $participants = $hackaton->teams()
            ->with(['user:id,email,fio,nickname', 'roles.user:id,email,fio,nickname'])
            ->get()
            ->flatMap(function ($team) {
                $users = collect([$team->user])->merge($team->roles->pluck('user'));

                return $users->filter();
            })
            ->unique('id')
            ->values();

        return $participants;
    }

    private function hydrateHackatonApplicationsRelation(Hackaton $hackaton, ?User $user, bool $isOrganizer): void
    {
        if ($isOrganizer) {
            $hackaton->setRelation(
                'applications',
                $hackaton->applications()->with(['team', 'reviewer'])->latest()->get(),
            );

            return;
        }

        if ($user === null) {
            $hackaton->setRelation('applications', collect());

            return;
        }

        $myTeamIds = $user->teams()->pluck('id');
        $hackaton->setRelation(
            'applications',
            $hackaton->applications()
                ->whereIn('team_id', $myTeamIds)
                ->with(['team', 'reviewer'])
                ->latest()
                ->get(),
        );
    }

    /**
     * @return array<string, int|float>
     */
    private function emptyMetrics(): array
    {
        return [
            'applications_total' => 0,
            'applications_pending' => 0,
            'applications_accepted' => 0,
            'applications_rejected' => 0,
            'submissions_total' => 0,
            'submissions_scored' => 0,
            'submissions_scored_percent' => 0,
        ];
    }
}
