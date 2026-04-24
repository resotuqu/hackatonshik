<?php

namespace App\Http\Requests;

use App\Models\Hackaton;
use App\Models\HackatonAnnouncement;
use Illuminate\Foundation\Http\FormRequest;

class StoreHackatonAnnouncementRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Hackaton $hackaton */
        $hackaton = $this->route('hackaton');

        return $this->user()?->can('create', [HackatonAnnouncement::class, $hackaton]) ?? false;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:10000'],
            'is_draft' => ['nullable', 'boolean'],
            'published_at' => ['nullable', 'date'],
            'template_key' => ['nullable', 'string', 'in:deadline,start,results'],
        ];
    }
}
