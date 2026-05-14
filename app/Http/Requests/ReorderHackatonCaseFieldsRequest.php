<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReorderHackatonCaseFieldsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'field_ids' => ['required', 'array', 'min:1'],
            'field_ids.*' => ['integer', 'distinct'],
        ];
    }
}
