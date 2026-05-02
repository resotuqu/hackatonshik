<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    /**
     * Validate and update the given user's profile information.
     *
     * Email смена выполняется только через профиль (двухэтапное подтверждение).
     *
     * @param  array<string, string>  $input
     *
     * @throws ValidationException
     */
    public function update(User $user, array $input): void
    {
        Validator::make($input, [
            'fio' => ['required', 'string', 'max:255'],
        ])->validateWithBag('updateProfileInformation');

        $user->forceFill([
            'fio' => $input['fio'],
        ])->save();
    }
}
