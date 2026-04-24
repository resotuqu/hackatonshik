<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\HackatonApplication;
use App\Models\User;

class HackatonApplicationPolicy
{
    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, HackatonApplication $application): bool
    {
        return $application->hackaton->user_id === $user->id; // только создатель хакатона
    }

    public function delete(User $user, HackatonApplication $application): bool
    {
        return $application->hackaton->user_id === $user->id || $application->team->user_id === $user->id;
    }
}
