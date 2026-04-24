<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreHackatonCaseScoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
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
