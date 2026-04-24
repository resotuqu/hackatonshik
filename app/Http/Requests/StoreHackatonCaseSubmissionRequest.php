<?php

namespace App\Http\Requests;

use App\Models\HackatonCase;
use App\Models\HackatonCaseSubmission;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreHackatonCaseSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var HackatonCase $case */
        $case = $this->route('case');

        return $this->user()?->can('create', [HackatonCaseSubmission::class, $case]) ?? false;
    }

    public function rules(): array
    {
        return [
            'scope' => ['required', 'string', Rule::in(['team', 'user'])],
            'team_id' => ['nullable', 'required_if:scope,team', 'integer', 'exists:teams,id'],
            'answers' => ['nullable', 'array'],
            'answers.*' => ['nullable', 'string', 'max:10000'],
            'files' => ['nullable', 'array'],
            'files.*' => ['nullable', 'file', 'max:10240'],
        ];
    }
}
