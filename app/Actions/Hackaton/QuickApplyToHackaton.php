<?php

declare(strict_types=1);

namespace App\Actions\Hackaton;

use App\Enums\ApplicationStatus;
use App\Models\Hackaton;
use App\Models\HackatonApplication;
use App\Models\Team;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

final class QuickApplyToHackaton
{
    public function handle(User $user, int $hackatonId): HackatonApplication
    {
        Gate::forUser($user)->authorize('create', HackatonApplication::class);

        $rateLimitKey = 'applications:'.$user->id;

        if (RateLimiter::tooManyAttempts($rateLimitKey, 10)) {
            throw ValidationException::withMessages([
                'hackaton_id' => ['Слишком много заявок. Попробуйте позже.'],
            ]);
        }

        RateLimiter::hit($rateLimitKey, 60);

        $hackaton = Hackaton::query()->findOrFail($hackatonId);

        $team = Team::query()
            ->where('user_id', $user->id)
            ->where('hackaton_id', $hackatonId)
            ->first();

        if ($team === null) {
            throw ValidationException::withMessages([
                'team_id' => ['Нет доступной команды для быстрой заявки.'],
            ]);
        }

        $this->assertCanSubmit($user, $team, $hackaton);

        try {
            return DB::transaction(function () use ($user, $hackaton, $team): HackatonApplication {
                $application = HackatonApplication::query()->firstOrNew([
                    'hackaton_id' => $hackaton->id,
                    'team_id' => $team->id,
                ]);

                $application->fill([
                    'status' => ApplicationStatus::PENDING,
                    'reviewed_at' => null,
                    'reviewed_by' => null,
                    'message' => null,
                    'hackaton_cases_count_when_applied' => $hackaton->cases()->count(),
                ]);
                $application->save();

                return $application;
            });
        } catch (UniqueConstraintViolationException) {
            throw ValidationException::withMessages([
                'team_id' => ['У этой команды уже есть активная заявка в этот хакатон.'],
            ]);
        }
    }

    private function assertCanSubmit(User $user, Team $team, Hackaton $hackaton): void
    {
        if (! $user->canParticipate()) {
            throw new AuthorizationException('Только участники могут подавать заявки на хакатоны.');
        }

        $isOwner = $team->user_id === $user->id;
        $isCaptain = $team->roles()
            ->where('user_id', $user->id)
            ->whereHas('role', fn ($query) => $query->where('name', 'капитан'))
            ->exists();

        if (! $isOwner && ! $isCaptain) {
            throw ValidationException::withMessages([
                'team_id' => ['Только владелец или капитан команды может подать заявку.'],
            ]);
        }

        $hasActiveApplication = HackatonApplication::query()
            ->where('team_id', $team->id)
            ->where('hackaton_id', $hackaton->id)
            ->where('status', ApplicationStatus::PENDING)
            ->exists();

        if ($hasActiveApplication) {
            throw ValidationException::withMessages([
                'team_id' => ['У этой команды уже есть активная заявка в этот хакатон.'],
            ]);
        }
    }
}
