<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\ApplicationStatus;
use App\Models\TeamApplication;
use App\Models\TeamRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreTeamApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'team_role_id' => ['required', 'exists:team_roles,id'],
            'message' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function after(): array
    {
        return [
            function ($validator): void {
                $user = $this->user();
                $teamRoleId = (int) $this->input('team_role_id');

                if (! $user || $teamRoleId === 0) {
                    return;
                }

                if ($user->isOrganizer()) {
                    $validator->errors()->add('team_role_id', 'Организатор не может подавать заявки в команды.');

                    return;
                }

                $teamRole = TeamRole::query()
                    ->with('team')
                    ->find($teamRoleId);

                if (! $teamRole) {
                    return;
                }

                if ($teamRole->user_id !== null) {
                    $validator->errors()->add('team_role_id', 'Эта роль уже занята.');
                }

                if ($teamRole->team->user_id === $user->id) {
                    $validator->errors()->add('team_role_id', 'Владелец команды не может подать заявку в свою команду.');
                }

                $alreadyInTeam = TeamRole::query()
                    ->where('team_id', $teamRole->team_id)
                    ->where('user_id', $user->id)
                    ->exists();

                if ($alreadyInTeam) {
                    $validator->errors()->add('team_role_id', 'Вы уже состоите в этой команде.');
                }

                $hasActiveApplication = TeamApplication::query()
                    ->where('user_id', $user->id)
                    ->where('team_role_id', $teamRole->id)
                    ->where('status', ApplicationStatus::PENDING)
                    ->exists();

                if ($hasActiveApplication) {
                    $validator->errors()->add('team_role_id', 'У вас уже есть активная заявка на эту роль.');
                }
            },
        ];
    }
}
