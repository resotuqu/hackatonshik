<?php

declare(strict_types=1);

use App\Actions\AnonymizeUserAccount;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

it('anonymizes user account completely', function (): void {
    $user = User::factory()->create([
        'fio' => 'Иван Петров',
        'nickname' => 'ivan_petrov',
        'email' => 'ivan@example.com',
        'phone' => '+79999999999',
        'description' => 'About me',
        'is_profile_public' => true,
        'show_email_on_profile' => true,
        'show_phone_on_profile' => true,
    ]);

    $originalEmail = $user->email;

    AnonymizeUserAccount::run($user);

    $user->refresh();

    expect($user->isAccountDeleted())->toBeTrue();
    expect($user->account_deleted_at)->not->toBeNull();
    expect($user->fio)->toBe('');
    expect($user->phone)->toBeNull();
    expect($user->description)->toBe('');
    expect($user->nickname)->toStartWith('deleted_');
    expect($user->email)->not->toEqual($originalEmail);
    expect($user->is_profile_public)->toBeFalse();
    expect($user->show_email_on_profile)->toBeFalse();
    expect($user->show_phone_on_profile)->toBeFalse();
    expect($user->suspended_at)->not->toBeNull();
});

it('invalidates password and sessions', function (): void {
    $user = User::factory()->create([
        'password' => Hash::make('password123'),
    ]);

    $oldPassword = $user->password;

    DB::table('sessions')->insert([
        'user_id' => $user->id,
        'ip_address' => '127.0.0.1',
        'user_agent' => 'Test',
        'payload' => 'test',
        'last_activity' => time(),
    ]);

    AnonymizeUserAccount::run($user);

    $user->refresh();

    expect($user->password)->not->toEqual($oldPassword);
    expect(Hash::check('password123', $user->password))->toBeFalse();

    $sessionCount = DB::table('sessions')
        ->where('user_id', $user->id)
        ->count();

    expect($sessionCount)->toBe(0);
});

it('clears 2FA secrets', function (): void {
    $user = User::factory()->create([
        'two_factor_secret' => 'secret123',
        'two_factor_recovery_codes' => 'code1,code2,code3',
    ]);

    AnonymizeUserAccount::run($user);

    $user->refresh();

    expect($user->two_factor_secret)->toBeNull();
    expect($user->two_factor_recovery_codes)->toBeNull();
});

it('returns deleted display name with skull emoji', function (): void {
    $user = User::factory()->create([
        'nickname' => 'ivan_petrov',
    ]);

    AnonymizeUserAccount::run($user);

    $user->refresh();

    $displayName = $user->getDeletedDisplayName();

    expect($displayName)->toContain('☠️');
    expect($displayName)->toContain("deleted_{$user->id}");
});

it('preserves user id for foreign keys', function (): void {
    $user = User::factory()->create();
    $userId = $user->id;

    AnonymizeUserAccount::run($user);

    $user->refresh();

    expect($user->id)->toBe($userId);
});

it('makes email reusable after deletion', function (): void {
    $user = User::factory()->create([
        'email' => 'test@example.com',
    ]);

    AnonymizeUserAccount::run($user);

    $user->refresh();

    // Old email should not be used
    expect($user->email)->not->toEqual('test@example.com');

    // New user should be able to use the old email
    $newUser = User::factory()->create([
        'email' => 'test@example.com',
    ]);

    expect($newUser->email)->toBe('test@example.com');
});
