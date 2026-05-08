<?php

namespace App\Http\Requests;

use App\Models\Hackaton;
use Illuminate\Foundation\Http\FormRequest;

class StoreHackatonCaseScoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        $hackaton = $this->route('hackaton');

        if ($user === null || ! $hackaton instanceof Hackaton) {
            return false;
        }

        if ((int) $hackaton->user_id === (int) $user->id) {
            return true;
        }

        return $hackaton->judges()->where('users.id', $user->id)->exists();
    }

    public function rules(): array
    {
        return [
            'hackaton_case_submission_id' => ['required', 'integer', 'exists:hackaton_case_submissions,id'],
            'score' => ['required', 'integer', 'min:0', 'max:100'],
            'max_score' => ['nullable', 'integer', 'min:1', 'max:100'],
            'comment' => ['nullable', 'string', 'max:3000'],
        ];
    }
}
