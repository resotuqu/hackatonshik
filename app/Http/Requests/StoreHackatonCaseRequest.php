<?php

namespace App\Http\Requests;

use App\Models\Hackaton;
use App\Models\HackatonCase;
use Illuminate\Foundation\Http\FormRequest;

class StoreHackatonCaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Hackaton $hackaton */
        $hackaton = $this->route('hackaton');

        return $this->user()?->can('create', [HackatonCase::class, $hackaton]) ?? false;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'is_published' => ['nullable', 'boolean'],
            'publish_at' => ['nullable', 'date'],
            'deadline_at' => ['nullable', 'date', 'after:publish_at'],
        ];
    }
}
