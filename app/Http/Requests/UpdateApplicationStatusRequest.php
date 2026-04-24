<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\ApplicationStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateApplicationStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'status' => [
                'required',
                Rule::in([
                    ApplicationStatus::ACCEPTED->value,
                    ApplicationStatus::REJECTED->value,
                ]),
            ],
        ];
    }
}
