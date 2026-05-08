<?php

namespace App\Http\Controllers;

use App\Enums\ApplicationStatus;
use App\Enums\HackatonStatus;
use App\Http\Requests\StoreHackatonCaseSubmissionRequest;
use App\Models\Hackaton;
use App\Models\HackatonCase;
use App\Models\HackatonCaseField;
use App\Models\HackatonCaseSubmission;
use App\Models\Team;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class HackatonCaseSubmissionController extends Controller
{
    public function store(StoreHackatonCaseSubmissionRequest $request, Hackaton $hackaton, HackatonCase $case): RedirectResponse
    {
        abort_unless($case->hackaton_id === $hackaton->id, 404);
        Gate::authorize('create', [HackatonCaseSubmission::class, $case]);

        if (! $case->isOpenForSubmission()) {
            abort(422, 'Отправка недоступна: кейс не опубликован или дедлайн уже прошел.');
        }

        $validated = $request->validated();
        $scope = $validated['scope'];
        $teamId = $scope === 'team' ? (int) ($validated['team_id'] ?? 0) : null;
        $userId = $scope === 'user' ? (int) $request->user()->id : null;
        $answers = $validated['answers'] ?? [];
        $uploadedFiles = $request->file('files', []);

        if ($scope === 'team') {
            $team = Team::find($teamId);
            if (! $team || ! $this->isValidSubmitterTeam($hackaton, $teamId, (int) $request->user()->id)) {
                abort(422, 'Команда не найдена или не одобрена для участия в этом хакатоне.');
            }

            if ($team->hackaton_case_id !== $case->id) {
                abort(422, 'Команда должна сначала присоединиться к этому кейсу.');
            }
        } else {
            // For personal scope, check if the user belongs to ANY approved team in this hackathon
            // And if that team is joined to THIS case
            $approvedJoinedTeamExists = $hackaton->teams()
                ->where('hackaton_case_id', $case->id)
                ->where(function (Builder $query) use ($request): void {
                    $query
                        ->where('teams.user_id', $request->user()->id)
                        ->orWhereHas('roles', function (Builder $rolesQuery) use ($request): void {
                            $rolesQuery->where('team_roles.user_id', $request->user()->id);
                        });
                })
                ->whereHas('hackatonApplications', function (Builder $query) use ($hackaton): void {
                    $query->where('hackaton_id', $hackaton->id)
                        ->where('status', ApplicationStatus::ACCEPTED);
                })
                ->exists();

            if (! $approvedJoinedTeamExists) {
                abort(422, 'Вы должны быть участником одобренной команды, присоединившейся к этому кейсу.');
            }
        }

        // Check hackathon status for link submissions
        if ($hackaton->status !== HackatonStatus::IN_PROGRESS) {
            foreach ($case->fields as $field) {
                if ($field->type === HackatonCaseField::TYPE_URL && filled($answers[$field->id] ?? '')) {
                    return back()->with('error', 'Загрузка ссылок (репозиторий, Figma и т.д.) доступна только после перехода хакатона в статус активной разработки.')->withInput();
                }
            }
        }

        $case->loadMissing('fields');
        $requiredErrors = $this->validateCaseAnswers($case, $answers, $uploadedFiles);

        if ($requiredErrors !== []) {
            return back()->withErrors($requiredErrors)->withInput();
        }

        DB::transaction(function () use ($case, $request, $teamId, $userId, $answers, $uploadedFiles): void {
            $submission = $case->submissions()
                ->where('team_id', $teamId)
                ->where('user_id', $userId)
                ->first();

            if (! $submission) {
                $submission = $case->submissions()->create([
                    'team_id' => $teamId,
                    'user_id' => $userId,
                    'submitted_by_user_id' => $request->user()->id,
                    'submitted_at' => now(),
                ]);
            } else {
                $submission->update([
                    'submitted_by_user_id' => $request->user()->id,
                    'submitted_at' => now(),
                ]);
            }

            foreach ($case->fields as $field) {
                $rawAnswer = (string) ($answers[$field->id] ?? '');
                $newFilePath = isset($uploadedFiles[$field->id])
                    ? $uploadedFiles[$field->id]->store('hackaton_case_answers', 'local')
                    : null;

                $payload = [
                    'value_text' => $field->type === HackatonCaseField::TYPE_FILE ? null : ($rawAnswer !== '' ? $rawAnswer : null),
                ];

                if ($newFilePath !== null) {
                    $payload['file_path'] = $newFilePath;
                }

                $submission->answers()->updateOrCreate(
                    ['hackaton_case_field_id' => $field->id],
                    $payload,
                );
            }
        });

        return back()->with('success', 'Ответы по кейсу успешно отправлены.');
    }

    private function isValidSubmitterTeam(Hackaton $hackaton, int $teamId, int $userId): bool
    {
        return $hackaton->teams()
            ->where('teams.id', $teamId)
            ->where(function (Builder $query) use ($userId): void {
                $query
                    ->where('teams.user_id', $userId)
                    ->orWhereHas('roles', function (Builder $rolesQuery) use ($userId): void {
                        $rolesQuery->where('team_roles.user_id', $userId);
                    });
            })
            ->whereHas('hackatonApplications', function (Builder $query) use ($hackaton): void {
                $query->where('hackaton_id', $hackaton->id)
                    ->where('status', ApplicationStatus::ACCEPTED);
            })
            ->exists();
    }

    private function isUserInApprovedTeam(Hackaton $hackaton, int $userId): bool
    {
        return $hackaton->teams()
            ->where(function (Builder $query) use ($userId): void {
                $query
                    ->where('teams.user_id', $userId)
                    ->orWhereHas('roles', function (Builder $rolesQuery) use ($userId): void {
                        $rolesQuery->where('team_roles.user_id', $userId);
                    });
            })
            ->whereHas('hackatonApplications', function (Builder $query) use ($hackaton): void {
                $query->where('hackaton_id', $hackaton->id)
                    ->where('status', ApplicationStatus::ACCEPTED);
            })
            ->exists();
    }

    private function validateCaseAnswers(HackatonCase $case, array $answers, array $uploadedFiles): array
    {
        $errors = [];

        foreach ($case->fields as $field) {
            $rawAnswer = (string) ($answers[$field->id] ?? '');
            $hasTextAnswer = filled(trim($rawAnswer));
            $hasFileAnswer = isset($uploadedFiles[$field->id]);

            if ($field->is_required && ! $hasTextAnswer && ! $hasFileAnswer) {
                $errors["answers.{$field->id}"] = "Поле \"{$field->label}\" обязательно для заполнения.";
            }

            if ($field->type === HackatonCaseField::TYPE_URL && $hasTextAnswer && ! filter_var($rawAnswer, FILTER_VALIDATE_URL)) {
                $errors["answers.{$field->id}"] = "Поле \"{$field->label}\" должно содержать корректную ссылку.";
            }
        }

        return $errors;
    }
}
