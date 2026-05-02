<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     *
     * @throws ValidationException
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'fio' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['required', 'date', 'before:now'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],
            'nickname' => ['required', 'string', 'max:255', Rule::unique(User::class)],
            'password' => $this->passwordRules(),
            'phone' => ['required', 'string', 'min:11', 'max:12', Rule::unique(User::class)],
        ])->validate();

        $user = User::create([
            'fio' => $input['fio'],
            'date_of_birth' => $input['date_of_birth'],
            'email' => $input['email'],
            'nickname' => $input['nickname'],
            'password' => Hash::make($input['password']),
            'phone' => $input['phone'],
        ]);

        $user->sendEmailVerificationNotification();

        return $user;
    }
}
