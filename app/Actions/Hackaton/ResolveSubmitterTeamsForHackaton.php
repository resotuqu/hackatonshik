<?php

declare(strict_types=1);

namespace App\Actions\Hackaton;

use App\Enums\ApplicationStatus;
use App\Models\Hackaton;
use App\Models\Team;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

final class ResolveSubmitterTeamsForHackaton
{
    /**
     * @return Collection<int, Team>
     */
    public function handle(Hackaton $hackaton): Collection
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
            ->whereHas('hackatonApplications', function (Builder $query) use ($hackaton): void {
                $query->where('hackaton_id', $hackaton->id)
                    ->where('status', ApplicationStatus::ACCEPTED);
            })
            ->orderBy('title')
            ->get(['teams.id', 'teams.title', 'teams.hackaton_case_id']);
    }
}
