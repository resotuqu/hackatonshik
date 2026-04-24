<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Hackaton;
use App\Models\JudgeInvitation;
use Illuminate\Foundation\Http\FormRequest;

class StoreJudgeInvitationRequest extends FormRequest
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
            'email' => ['required', 'email:rfc,dns', 'max:255'],
        ];
    }

    public function after(): array
    {
        return [
            function ($validator): void {
                /** @var Hackaton|null $hackaton */
                $hackaton = $this->route('hackaton');
                $email = mb_strtolower((string) $this->input('email'));

                if ($hackaton === null || $email === '') {
                    return;
                }

                $hasPendingInvite = JudgeInvitation::query()
                    ->where('hackaton_id', $hackaton->id)
                    ->where('invited_email', $email)
                    ->where('status', JudgeInvitation::STATUS_PENDING)
                    ->exists();

                if ($hasPendingInvite) {
                    $validator->errors()->add('email', 'Для этого email уже есть активное приглашение.');
                }
            },
        ];
    }
}
