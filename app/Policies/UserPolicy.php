<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewActivityHistory(User $user, User $model): bool
    {
        return $user->isAdmin();
    }
}
