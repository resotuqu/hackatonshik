<?php

namespace App\Policies;

use App\Models\Hackaton;
use App\Models\HackatonCertificate;
use App\Models\User;

class HackatonCertificatePolicy
{
    public function create(User $user, Hackaton $hackaton): bool
    {
        return $hackaton->user_id === $user->id;
    }

    public function view(User $user, HackatonCertificate $hackatonCertificate): bool
    {
        $hackaton = Hackaton::query()->find($hackatonCertificate->hackaton_id);

        return $hackatonCertificate->user_id === $user->id
            || ($hackaton instanceof Hackaton && $hackaton->user_id === $user->id);
    }

    public function download(User $user, HackatonCertificate $hackatonCertificate): bool
    {
        $hackaton = Hackaton::query()->find($hackatonCertificate->hackaton_id);

        return $hackatonCertificate->user_id === $user->id
            || ($hackaton instanceof Hackaton && $hackaton->user_id === $user->id);
    }

    public function delete(User $user, HackatonCertificate $hackatonCertificate): bool
    {
        $hackaton = Hackaton::query()->find($hackatonCertificate->hackaton_id);

        return $hackaton instanceof Hackaton && $hackaton->user_id === $user->id;
    }
}
