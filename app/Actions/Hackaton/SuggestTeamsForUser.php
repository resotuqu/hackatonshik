<?php

declare(strict_types=1);

namespace App\Actions\Hackaton;

use App\Enums\HackatonStatus;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

final class SuggestTeamsForUser
{
    /**
     * @return Collection<int, array{team: Team, match_score: int, matched_skills: list<string>}>
     */
    public function handle(User $user, ?int $limit = 6, ?int $hackatonId = null): Collection
    {
        if (! $user->open_to_teams) {
            return collect();
        }

        $userSkillIds = $user->skills()->pluck('skills.id');

        if ($userSkillIds->isEmpty()) {
            return collect();
        }

        $joinedTeamIds = Team::query()
            ->where(function (Builder $query) use ($user): void {
                $query
                    ->where('user_id', $user->id)
                    ->orWhereHas('roles', fn (Builder $rolesQuery) => $rolesQuery->where('user_id', $user->id));
            })
            ->pluck('id');

        $teams = Team::query()
            ->where('is_public', true)
            ->whereNotIn('id', $joinedTeamIds)
            ->whereHas('hackaton', function (Builder $query): void {
                $query
                    ->where('is_public', true)
                    ->where('status', HackatonStatus::REGISTRATION_OPEN);
            })
            ->whereHas('roles', function (Builder $query): void {
                $query
                    ->whereNull('user_id')
                    ->whereHas('skills');
            })
            ->when($hackatonId !== null, fn (Builder $query) => $query->where('hackaton_id', $hackatonId))
            ->with([
                'hackaton:id,title,start_at,status',
                'roles' => fn ($query) => $query->whereNull('user_id')->with('skills:id,name'),
            ])
            ->limit(50)
            ->get();

        return $teams
            ->map(function (Team $team) use ($userSkillIds): ?array {
                $openRoleSkills = $team->roles
                    ->whereNull('user_id')
                    ->flatMap(fn ($role) => $role->skills);

                $matchedSkills = $openRoleSkills
                    ->whereIn('id', $userSkillIds)
                    ->unique('id');

                $matchScore = $matchedSkills->count();

                if ($matchScore === 0) {
                    return null;
                }

                return [
                    'team' => $team,
                    'match_score' => $matchScore,
                    'matched_skills' => $matchedSkills->pluck('name')->values()->all(),
                ];
            })
            ->filter()
            ->sortByDesc('match_score')
            ->take($limit ?? 6)
            ->values();
    }
}
