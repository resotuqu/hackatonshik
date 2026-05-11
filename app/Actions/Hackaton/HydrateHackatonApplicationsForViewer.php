<?php

declare(strict_types=1);

namespace App\Actions\Hackaton;

use App\Models\Hackaton;
use App\Models\HackatonApplication;
use App\Models\User;
use Illuminate\Support\Collection;

final class HydrateHackatonApplicationsForViewer
{
    /**
     * @param  Collection<int, HackatonApplication>|null  $organizerApplications
     */
    public function handle(Hackaton $hackaton, ?User $user, bool $isOrganizer, ?Collection $organizerApplications = null): void
    {
        if ($isOrganizer) {
            $hackaton->setRelation(
                'applications',
                $organizerApplications ?? $hackaton->applications()->with(['team', 'reviewer'])->latest()->get(),
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
}
