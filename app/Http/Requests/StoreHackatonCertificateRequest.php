<?php

namespace App\Http\Requests;

use App\Actions\Hackaton\ResolveParticipantUsersForHackatonCertificates;
use App\Models\Hackaton;
use App\Models\HackatonCertificate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

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

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            /** @var Hackaton $hackaton */
            $hackaton = $this->route('hackaton');
            $participantIds = app(ResolveParticipantUsersForHackatonCertificates::class)
                ->handle($hackaton)
                ->pluck('id')
                ->all();

            $recipientIds = collect($this->input('user_ids', []))
                ->filter()
                ->whenEmpty(fn () => collect([(int) $this->input('user_id')]))
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->values();

            $invalidIds = $recipientIds->diff($participantIds);

            if ($invalidIds->isNotEmpty()) {
                $validator->errors()->add(
                    'user_ids',
                    'Сертификаты можно выдавать только участникам хакатона.',
                );
            }
        });
    }
}
