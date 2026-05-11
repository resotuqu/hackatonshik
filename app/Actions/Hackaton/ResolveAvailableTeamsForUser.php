<?php

declare(strict_types=1);

namespace App\Actions\Hackaton;

use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Collection;

final class ResolveAvailableTeamsForUser
{
    /**
     * @return Collection<int, Team>
     */
    public function handle(?User $user): Collection
    {
        if ($user === null) {
            return collect();
        }

        return $user->teams()->select(['id', 'title'])->orderBy('title')->get();
    }
}
