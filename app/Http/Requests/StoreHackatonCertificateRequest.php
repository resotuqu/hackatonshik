<?php

namespace App\Http\Requests;

use App\Models\Hackaton;
use App\Models\HackatonCertificate;
use Illuminate\Foundation\Http\FormRequest;

class StoreHackatonCertificateRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Hackaton $hackaton */
        $hackaton = $this->route('hackaton');

        return $this->user()?->can('create', [HackatonCertificate::class, $hackaton]) ?? false;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'user_ids' => ['nullable', 'array'],
            'user_ids.*' => ['integer', 'exists:users,id'],
            'title' => ['required', 'string', 'max:255'],
            'file' => ['required', 'file', 'mimes:pdf,png,jpg,jpeg', 'max:10240'],
            'issued_at' => ['nullable', 'date'],
        ];
    }
}
