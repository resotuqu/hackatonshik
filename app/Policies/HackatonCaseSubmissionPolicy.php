<?php

namespace App\Policies;

use App\Enums\ApplicationStatus;
use App\Models\HackatonCase;
use App\Models\HackatonCaseSubmission;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class HackatonCaseSubmissionPolicy
{
    public function create(User $user, HackatonCase $hackatonCase): bool
    {
        if ($hackatonCase->hackaton->user_id === $user->id) {
            return true;
        }

        $hackatonId = $hackatonCase->hackaton_id;

        return $hackatonCase->hackaton->teams()
            ->where(function (Builder $query) use ($user): void {
                $query
                    ->where('teams.user_id', $user->id)
                    ->orWhereHas('roles', function (Builder $rolesQuery) use ($user): void {
                        $rolesQuery->where('team_roles.user_id', $user->id);
                    });
            })
            ->where('teams.hackaton_id', $hackatonId)
            ->whereHas('hackatonApplications', function (Builder $query) use ($hackatonId): void {
                $query->where('hackaton_id', $hackatonId)
                    ->where('status', ApplicationStatus::ACCEPTED);
            })
            ->exists();
    }

    public function view(User $user, HackatonCaseSubmission $hackatonCaseSubmission): bool
    {
        if ($hackatonCaseSubmission->case->hackaton->user_id === $user->id) {
            return true;
        }

        if ($hackatonCaseSubmission->user_id === $user->id) {
            return true;
        }

        if ($hackatonCaseSubmission->team_id === null) {
            return false;
        }

        return $hackatonCaseSubmission->team
            ->roles()
            ->where('user_id', $user->id)
            ->exists() || $hackatonCaseSubmission->team->user_id === $user->id;
    }

    public function delete(User $user, HackatonCaseSubmission $hackatonCaseSubmission): bool
    {
        return $hackatonCaseSubmission->case->hackaton->user_id === $user->id;
    }
}
