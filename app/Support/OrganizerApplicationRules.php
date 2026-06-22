<?php

declare(strict_types=1);

namespace App\Support;

use App\Enums\OrganizerEntityType;
use Illuminate\Validation\Rule;

final class OrganizerApplicationRules
{
    /**
     * @return array<string, list<\Illuminate\Contracts\Validation\Rule|string>>
     */
    public static function forFields(
        string $entityTypeField = 'organizerEntityType',
        string $companyNameField = 'organizerCompanyName',
        string $noteField = 'organizerNote',
        ?string $entityType = null,
    ): array {
        return [
            $entityTypeField => ['required', Rule::enum(OrganizerEntityType::class)],
            $companyNameField => [
                'nullable',
                'string',
                'max:255',
                Rule::requiredIf($entityType === OrganizerEntityType::Company->value),
            ],
            $noteField => ['required', 'string', 'min:20', 'max:2000'],
        ];
    }
}
