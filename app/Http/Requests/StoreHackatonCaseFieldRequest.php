<?php

namespace App\Http\Requests;

use App\Models\HackatonCaseField;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreHackatonCaseFieldRequest extends FormRequest
{
    public function authorize(): bool
    {
        $case = $this->route('case');

        return $this->user()?->can('update', $case) ?? false;
    }

    public function rules(): array
    {
        return [
            'label' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', Rule::in(HackatonCaseField::allowedTypes())],
            'is_required' => ['nullable', 'boolean'],
        ];
    }
}
