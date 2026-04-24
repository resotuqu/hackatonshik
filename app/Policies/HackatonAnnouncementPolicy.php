<?php

namespace App\Policies;

use App\Models\Hackaton;
use App\Models\HackatonAnnouncement;
use App\Models\User;

class HackatonAnnouncementPolicy
{
    public function create(User $user, Hackaton $hackaton): bool
    {
        return $hackaton->user_id === $user->id;
    }

    public function view(?User $user, HackatonAnnouncement $hackatonAnnouncement): bool
    {
        return true;
    }

    public function update(User $user, HackatonAnnouncement $hackatonAnnouncement): bool
    {
        return $hackatonAnnouncement->hackaton->user_id === $user->id;
    }

    public function delete(User $user, HackatonAnnouncement $hackatonAnnouncement): bool
    {
        return $hackatonAnnouncement->hackaton->user_id === $user->id;
    }
}
