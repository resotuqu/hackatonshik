<?php

namespace App\Http\Controllers;

use App\Models\Hackaton;
use App\Models\HackatonCaseSubmission;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class HackatonController extends Controller
{
    public function show(Hackaton $hackaton): View
    {
        $isOrganizer = Auth::check() && (int) $hackaton->user_id === (int) Auth::id();
        $isAssignedJudge = Auth::check() && $hackaton->judges()->where('users.id', Auth::id())->exists();
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
        $hackaton->setRelation('certificates', $hackaton->certificates()->with('user')->get());

        $availableTeams = $this->resolveAvailableTeams();
        $submitterTeams = $this->resolveSubmitterTeams($hackaton);
        $participantUsers = $this->resolveParticipantUsers($hackaton);
        $applicationStatusFilter = request()->string('applications_status')->toString();
        $applications = $hackaton->applications()
            ->with(['team', 'reviewer'])
            ->when($applicationStatusFilter !== '', fn (Builder $query) => $query->where('status', $applicationStatusFilter))
            ->latest()
            ->get();

        $submissions = HackatonCaseSubmission::query()
            ->whereHas('case', fn (Builder $query) => $query->where('hackaton_id', $hackaton->id))
            ->with(['case', 'team', 'user', 'score'])
            ->get();

        $metrics = [
            'applications_total' => $hackaton->applications()->count(),
            'applications_pending' => $hackaton->applications()->where('status', 'pending')->count(),
            'applications_accepted' => $hackaton->applications()->where('status', 'accepted')->count(),
            'applications_rejected' => $hackaton->applications()->where('status', 'rejected')->count(),
            'submissions_total' => $submissions->count(),
            'submissions_scored' => $submissions->whereNotNull('score')->count(),
        ];
        $metrics['submissions_scored_percent'] = $metrics['submissions_total'] > 0
            ? (int) round(($metrics['submissions_scored'] / $metrics['submissions_total']) * 100)
            : 0;

        $leaderboard = $submissions
            ->filter(fn (HackatonCaseSubmission $submission) => $submission->team !== null && $submission->score !== null)
            ->groupBy('team_id')
            ->map(function ($teamSubmissions) {
                /** @var HackatonCaseSubmission $firstSubmission */
                $firstSubmission = $teamSubmissions->first();
                $totalScore = $teamSubmissions->sum(fn (HackatonCaseSubmission $submission) => (int) $submission->score?->score);
                $maxScore = $teamSubmissions->sum(fn (HackatonCaseSubmission $submission) => (int) $submission->score?->max_score);

                return [
                    'team' => $firstSubmission->team,
                    'total_score' => $totalScore,
                    'max_score' => $maxScore,
                    'progress_percent' => $maxScore > 0 ? (int) round(($totalScore / $maxScore) * 100) : 0,
                ];
            })
            ->sortByDesc('total_score')
            ->values();

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

        return view('pages.hackatons.show', compact(
            'hackaton',
            'isAssignedJudge',
            'availableTeams',
            'submitterTeams',
            'participantUsers',
            'applications',
            'applicationStatusFilter',
            'submissions',
            'metrics',
            'leaderboard',
            'judgeCandidates',
            'pendingJudgeInvitations',
        ));
    }

    private function resolveAvailableTeams(): Collection
    {
        if (! Auth::check()) {
            return collect();
        }

        return Auth::user()->teams()->select(['id', 'title'])->orderBy('title')->get();
    }

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
}
