<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\HackatonApplication;
use App\Models\User;

class HackatonApplicationPolicy
{
    public function create(User $user): bool
    {
        return $user->canParticipate();
    }

    public function update(User $user, HackatonApplication $application): bool
    {
        return (int) $application->hackaton?->user_id === (int) $user->id;
    }

    public function delete(User $user, HackatonApplication $application): bool
    {
        return (int) $application->hackaton?->user_id === (int) $user->id
            || (int) $application->team?->user_id === (int) $user->id;
    }
}
