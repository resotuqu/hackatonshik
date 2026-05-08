<?php

namespace App\Policies;

use App\Enums\ApplicationStatus;
use App\Models\Hackaton;
use App\Models\HackatonCase;
use App\Models\HackatonCaseSubmission;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class HackatonCaseSubmissionPolicy
{
    public function create(User $user, HackatonCase $hackatonCase): bool
    {
        $hackaton = Hackaton::query()->find($hackatonCase->hackaton_id);
        if (! $hackaton instanceof Hackaton) {
            return false;
        }

        if ($hackaton->user_id === $user->id) {
            return true;
        }

        $hackatonId = $hackatonCase->hackaton_id;

        return $hackaton->teams()
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
        $submissionCase = HackatonCase::query()->find($hackatonCaseSubmission->hackaton_case_id);
        $hackaton = $submissionCase instanceof HackatonCase
            ? Hackaton::query()->find($submissionCase->hackaton_id)
            : null;

        if ($hackaton instanceof Hackaton && $hackaton->user_id === $user->id) {
            return true;
        }

        if ($hackatonCaseSubmission->user_id === $user->id) {
            return true;
        }

        if ($hackatonCaseSubmission->team_id === null) {
            return false;
        }

        $team = Team::query()->find($hackatonCaseSubmission->team_id);
        if (! $team instanceof Team) {
            return false;
        }

        return $team->roles()
            ->where('user_id', $user->id)
            ->exists() || $team->user_id === $user->id;
    }

    public function delete(User $user, HackatonCaseSubmission $hackatonCaseSubmission): bool
    {
        $submissionCase = HackatonCase::query()->find($hackatonCaseSubmission->hackaton_case_id);
        $hackaton = $submissionCase instanceof HackatonCase
            ? Hackaton::query()->find($submissionCase->hackaton_id)
            : null;

        return $hackaton instanceof Hackaton && $hackaton->user_id === $user->id;
    }
}
