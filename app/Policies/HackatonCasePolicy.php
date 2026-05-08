<?php

namespace App\Policies;

use App\Models\Hackaton;
use App\Models\HackatonCase;
use App\Models\User;

class HackatonCasePolicy
{
    public function view(?User $user, HackatonCase $hackatonCase): bool
    {
        if ($hackatonCase->is_published) {
            return true;
        }

        if (! $user) {
            return false;
        }

        $hackaton = Hackaton::query()->find($hackatonCase->hackaton_id);

        return $hackaton instanceof Hackaton && $hackaton->user_id === $user->id;
    }

    public function create(User $user, Hackaton $hackaton): bool
    {
        return $hackaton->user_id === $user->id;
    }

    public function update(User $user, HackatonCase $hackatonCase): bool
    {
        $hackaton = Hackaton::query()->find($hackatonCase->hackaton_id);

        return $hackaton instanceof Hackaton && $hackaton->user_id === $user->id;
    }

    public function delete(User $user, HackatonCase $hackatonCase): bool
    {
        $hackaton = Hackaton::query()->find($hackatonCase->hackaton_id);

        return $hackaton instanceof Hackaton && $hackaton->user_id === $user->id;
    }
}
