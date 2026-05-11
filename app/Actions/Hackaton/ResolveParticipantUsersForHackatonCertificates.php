<?php

declare(strict_types=1);

namespace App\Actions\Hackaton;

use App\Models\Hackaton;
use App\Models\User;
use Illuminate\Support\Collection;

final class ResolveParticipantUsersForHackatonCertificates
{
    /**
     * @return Collection<int, User>
     */
    public function handle(Hackaton $hackaton): Collection
    {
        $participantIds = $hackaton->teams()->pluck('user_id')
            ->merge($hackaton->roles()->whereNotNull('team_roles.user_id')->pluck('team_roles.user_id'))
            ->unique()
            ->filter();

        if ($participantIds->isEmpty()) {
            return collect();
        }

        return User::query()
            ->whereIn('id', $participantIds)
            ->orderBy('fio')
            ->get(['id', 'email', 'fio', 'nickname']);
    }
}
