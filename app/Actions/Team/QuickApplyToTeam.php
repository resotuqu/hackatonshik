<?php

declare(strict_types=1);

namespace App\Actions\Team;

use App\Enums\ApplicationStatus;
use App\Models\Team;
use App\Models\TeamApplication;
use App\Models\TeamRole;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

final class QuickApplyToTeam
{
    public function handle(User $user, int $teamId): TeamApplication
    {
        Gate::forUser($user)->authorize('create', TeamApplication::class);

        $rateLimitKey = 'applications:'.$user->id;

        if (RateLimiter::tooManyAttempts($rateLimitKey, 10)) {
            throw ValidationException::withMessages([
                'team_id' => ['Слишком много заявок. Попробуйте позже.'],
            ]);
        }

        RateLimiter::hit($rateLimitKey, 60);

        if (! $user->canParticipate()) {
            throw new AuthorizationException('Организатор не может подавать заявки в команды.');
        }

        $team = Team::query()->with('roles')->find($teamId);

        if ($team === null || $team->user_id === $user->id) {
            throw ValidationException::withMessages([
                'team_id' => ['Команда недоступна для быстрого отклика.'],
            ]);
        }

        $role = $team->roles->first(fn (TeamRole $candidate): bool => $candidate->user_id === null);

        if ($role === null) {
            throw ValidationException::withMessages([
                'team_role_id' => ['В команде нет свободных ролей.'],
            ]);
        }

        $this->assertCanApply($user, $role, $team);

        try {
            return DB::transaction(function () use ($user, $role): TeamApplication {
                $application = TeamApplication::query()->firstOrNew([
                    'user_id' => $user->id,
                    'team_role_id' => $role->id,
                ]);

                $application->fill([
                    'status' => ApplicationStatus::PENDING,
                    'message' => null,
                    'reviewed_at' => null,
                    'reviewed_by' => null,
                ]);
                $application->save();

                return $application;
            });
        } catch (UniqueConstraintViolationException) {
            throw ValidationException::withMessages([
                'team_role_id' => ['У вас уже есть активная заявка на эту роль.'],
            ]);
        }
    }

    private function assertCanApply(User $user, TeamRole $teamRole, Team $team): void
    {
        if ($teamRole->user_id !== null) {
            throw ValidationException::withMessages([
                'team_role_id' => ['Эта роль уже занята.'],
            ]);
        }

        $alreadyInTeam = TeamRole::query()
            ->where('team_id', $teamRole->team_id)
            ->where('user_id', $user->id)
            ->exists();

        if ($alreadyInTeam) {
            throw ValidationException::withMessages([
                'team_role_id' => ['Вы уже состоите в этой команде.'],
            ]);
        }

        $hasActiveApplication = TeamApplication::query()
            ->where('user_id', $user->id)
            ->where('team_role_id', $teamRole->id)
            ->where('status', ApplicationStatus::PENDING)
            ->exists();

        if ($hasActiveApplication) {
            throw ValidationException::withMessages([
                'team_role_id' => ['У вас уже есть активная заявка на эту роль.'],
            ]);
        }
    }
}
