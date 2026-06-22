<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\OrganizerApplication;
use App\Models\User;

class OrganizerApplicationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, OrganizerApplication $organizerApplication): bool
    {
        return $user->isAdmin();
    }

    public function approve(User $user, OrganizerApplication $organizerApplication): bool
    {
        return $user->isAdmin() && $organizerApplication->isPending();
    }

    public function reject(User $user, OrganizerApplication $organizerApplication): bool
    {
        return $user->isAdmin() && $organizerApplication->isPending();
    }
}
