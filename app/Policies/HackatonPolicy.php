<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\HackatonStatus;
use App\Models\Hackaton;
use App\Models\User;

class HackatonPolicy
{
    public function create(User $user): bool
    {
        return $user->isOrganizer();
    }

    public function view(?User $user, Hackaton $hackaton): bool
    {
        if ($hackaton->is_public) {
            return true;
        }

        if ($user === null) {
            return false;
        }

        if ((int) $hackaton->user_id === (int) $user->id) {
            return true;
        }

        if ($hackaton->isJudge($user)) {
            return true;
        }

        return $hackaton->isJudge($user) || $user->participatesInHackaton($hackaton);
    }

    public function update(User $user, Hackaton $hackaton): bool
    {
        return (int) $hackaton->user_id === (int) $user->id;
    }

    public function delete(User $user, Hackaton $hackaton): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $this->update($user, $hackaton);
    }

    /**
     * Организатор или назначенный судья (вкладки кейсов / оценки).
     */
    public function viewOrganizationDashboard(User $user, Hackaton $hackaton): bool
    {
        return $this->update($user, $hackaton) || $hackaton->isJudge($user);
    }

    public function viewActivityHistory(User $user, Hackaton $hackaton): bool
    {
        return $this->update($user, $hackaton);
    }

    public function viewPublicResults(?User $user, Hackaton $hackaton): bool
    {
        if (! $hackaton->is_results_public) {
            return false;
        }

        if (in_array($hackaton->status, [
            HackatonStatus::JUDGING,
            HackatonStatus::FINISHED,
            HackatonStatus::ARCHIVED,
        ], true)) {
            return true;
        }

        return $user !== null && $this->viewOrganizationDashboard($user, $hackaton);
    }
}
