<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Hackaton;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class AssignHackatonJudgeRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Hackaton|null $hackaton */
        $hackaton = $this->route('hackaton');
        $user = $this->user();

        return $hackaton !== null && $user !== null && (int) $hackaton->user_id === (int) $user->id;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', 'integer', 'exists:users,id'],
        ];
    }

    public function after(): array
    {
        return [
            function ($validator): void {
                $judgeId = (int) $this->input('user_id');
                if ($judgeId === 0) {
                    return;
                }

                $candidate = User::query()->find($judgeId);
                if (! $candidate || ! $candidate->isJudge()) {
                    $validator->errors()->add('user_id', 'Назначать можно только зарегистрированных судей.');
                }
            },
        ];
    }
}
