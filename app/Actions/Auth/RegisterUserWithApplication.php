<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\Enums\OrganizerEntityType;
use App\Models\OrganizerApplication;
use App\Models\User;
use App\Services\OAuth\OAuthPhoneResolver;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class RegisterUserWithApplication
{
    public function __construct(private readonly OAuthPhoneResolver $phoneResolver) {}

    /**
     * @param  array{
     *     fio: string,
     *     date_of_birth: string,
     *     email: string,
     *     nickname: string,
     *     password: string,
     *     phone: string,
     *     account_type?: string,
     *     organizer_entity_type?: string,
     *     organizer_company_name?: string|null,
     *     organizer_note?: string|null,
     * }  $input
     */
    public function create(array $input): User
    {
        $normalizedPhone = $this->phoneResolver->normalize($input['phone']);

        if ($normalizedPhone === null) {
            throw ValidationException::withMessages([
                'phone' => [__('ui.auth.register.phone_invalid')],
            ]);
        }

        $user = User::create([
            'fio' => $input['fio'],
            'email' => $input['email'],
            'nickname' => $input['nickname'],
            'phone' => $normalizedPhone,
            'date_of_birth' => $input['date_of_birth'],
            'password' => Hash::make($input['password']),
            'pd_consent_accepted_at' => now(),
        ]);

        if (($input['account_type'] ?? 'user') === 'partner') {
            OrganizerApplication::createPendingForUser(
                $user,
                OrganizerEntityType::from((string) $input['organizer_entity_type']),
                ($input['organizer_entity_type'] ?? '') === OrganizerEntityType::Company->value
                    ? ($input['organizer_company_name'] ?? null)
                    : null,
                (string) ($input['organizer_note'] ?? ''),
            );
        }

        $user->sendEmailVerificationNotification();

        return $user;
    }
}
