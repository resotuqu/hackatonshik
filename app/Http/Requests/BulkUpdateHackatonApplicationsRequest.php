<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\ApplicationStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BulkUpdateHackatonApplicationsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'application_ids' => ['required', 'array', 'min:1'],
            'application_ids.*' => ['integer', 'exists:hackaton_applications,id'],
            'status' => ['required', 'string', Rule::in([
                ApplicationStatus::ACCEPTED->value,
                ApplicationStatus::REJECTED->value,
            ])],
        ];
    }
}
