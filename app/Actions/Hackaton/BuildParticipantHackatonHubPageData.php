<?php

declare(strict_types=1);

namespace App\Actions\Hackaton;

use App\Models\Hackaton;
use App\Models\HackatonCaseSubmission;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

final class BuildParticipantHackatonHubPageData
{
    /**
     * @return array<string, mixed>|null
     */
    public function build(Hackaton $hackaton, User $user): ?array
    {
        $teams = Team::query()
            ->where('hackaton_id', $hackaton->id)
            ->where(function (Builder $query) use ($user): void {
                $query
                    ->where('user_id', $user->id)
                    ->orWhereHas('roles', fn (Builder $rolesQuery) => $rolesQuery->where('user_id', $user->id));
            })
            ->with(['roles.user:id,fio'])
            ->get();

        if ($teams->isEmpty()) {
            return null;
        }

        $teamIds = $teams->pluck('id');
        $applications = $hackaton->applications()
            ->whereIn('team_id', $teamIds)
            ->with('team:id,title')
            ->latest()
            ->get();

        $submissions = HackatonCaseSubmission::query()
            ->whereIn('team_id', $teamIds)
            ->whereHas('case', fn (Builder $query) => $query->where('hackaton_id', $hackaton->id))
            ->with(['case:id,hackaton_id,title,deadline_at', 'score'])
            ->latest('submitted_at')
            ->get();

        $requiredDocuments = $hackaton->documents()
            ->select(['id', 'name'])
            ->withCount(['usersFiles as uploaded_count' => fn (Builder $query) => $query->where('user_id', $user->id)])
            ->get();

        $upcomingCases = $hackaton->cases()
            ->whereNotNull('deadline_at')
            ->where('deadline_at', '>', now())
            ->where('is_published', true)
            ->where(function (Builder $scheduleQuery): void {
                $scheduleQuery
                    ->whereNull('publish_at')
                    ->orWhere('publish_at', '<=', now());
            })
            ->orderBy('deadline_at')
            ->limit(5)
            ->get(['id', 'title', 'deadline_at']);

        $myCertificates = $hackaton->certificates()
            ->where('user_id', $user->id)
            ->orderByDesc('issued_at')
            ->get(['id', 'title', 'issued_at']);

        return [
            'teams' => $teams,
            'applications' => $applications,
            'submissions' => $submissions,
            'requiredDocuments' => $requiredDocuments,
            'upcomingCases' => $upcomingCases,
            'myCertificates' => $myCertificates,
        ];
    }
}
