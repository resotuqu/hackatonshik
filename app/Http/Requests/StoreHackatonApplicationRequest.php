<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\ApplicationStatus;
use App\Models\HackatonApplication;
use App\Models\Team;
use App\Models\TeamRole;
use Illuminate\Foundation\Http\FormRequest;

class StoreHackatonApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', HackatonApplication::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'hackaton_id' => ['required', 'exists:hackatons,id'],
            'team_id' => ['required', 'exists:teams,id'],
            'message' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function after(): array
    {
        return [
            function ($validator): void {
                $user = $this->user();
                $teamId = (int) $this->input('team_id');
                $hackatonId = (int) $this->input('hackaton_id');

                if (! $user || $teamId === 0 || $hackatonId === 0) {
                    return;
                }

                if (! $user->canParticipate()) {
                    $validator->errors()->add('team_id', 'Только участники могут подавать заявки на хакатоны.');

                    return;
                }

                $team = Team::query()->find($teamId);

                if (! $team) {
                    return;
                }

                $isOwner = $team->user_id === $user->id;
                $isCaptain = TeamRole::query()
                    ->where('team_id', $team->id)
                    ->where('user_id', $user->id)
                    ->whereHas('role', fn ($query) => $query->where('name', 'капитан'))
                    ->exists();

                if (! $isOwner && ! $isCaptain) {
                    $validator->errors()->add('team_id', 'Только владелец или капитан команды может подать заявку.');
                }

                $hasActiveApplication = HackatonApplication::query()
                    ->where('team_id', $team->id)
                    ->where('hackaton_id', $hackatonId)
                    ->where('status', ApplicationStatus::PENDING)
                    ->exists();

                if ($hasActiveApplication) {
                    $validator->errors()->add('team_id', 'У этой команды уже есть активная заявка в этот хакатон.');
                }
            },
        ];
    }
}
