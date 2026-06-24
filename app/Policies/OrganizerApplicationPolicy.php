<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\OrganizerApplication;
use App\Models\User;

class OrganizerApplicationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdminOrModerator();
    }

    public function view(User $user, OrganizerApplication $organizerApplication): bool
    {
        return $user->isAdminOrModerator();
    }

    public function approve(User $user, OrganizerApplication $organizerApplication): bool
    {
        return $user->isAdminOrModerator() && $organizerApplication->isPending();
    }

    public function reject(User $user, OrganizerApplication $organizerApplication): bool
    {
        return $user->isAdminOrModerator() && $organizerApplication->isPending();
    }
}
