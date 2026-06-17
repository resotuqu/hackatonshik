<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\Features;
use Laravel\Fortify\Fortify;
use PragmaRX\Google2FA\Google2FA;

use function Pest\Laravel\post;

test('user can log in with valid credentials', function () {
    $user = User::factory()->create([
        'email' => 'login-probe@example.com',
        'password' => Hash::make('Str0ng!Passw0rd'),
        'email_verified_at' => now(),
        'phone_verified_at' => now(),
    ]);

    post('/login', [
        'email' => $user->email,
        'password' => 'Str0ng!Passw0rd',
    ])->assertRedirect('/');

    $this->assertAuthenticatedAs($user);
});

test('login fails with invalid credentials', function () {
    $user = User::factory()->create([
        'email' => 'bad-login@example.com',
        'password' => Hash::make('Str0ng!Passw0rd'),
    ]);

    post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ])->assertSessionHasErrors();

    $this->assertGuest();
});

test('password reset request accepts known email', function () {
    if (! Features::enabled(Features::resetPasswords())) {
        $this->markTestSkipped('Password reset feature is disabled.');
    }

    $user = User::factory()->create([
        'email' => 'reset-probe@example.com',
    ]);

    post('/forgot-password', [
        'email' => $user->email,
    ])->assertSessionHasNoErrors();
});

test('two factor authentication can be enabled for user', function () {
    if (! Features::enabled(Features::twoFactorAuthentication())) {
        $this->markTestSkipped('Two factor authentication feature is disabled.');
    }

    $user = User::factory()->create([
        'email_verified_at' => now(),
        'phone_verified_at' => now(),
    ]);

    $this->actingAs($user);

    post('/user/confirm-password', [
        'password' => 'password',
    ])->assertRedirect();

    post('/user/two-factor-authentication')->assertSessionHasNoErrors();

    expect($user->fresh()->two_factor_secret)->not->toBeNull();
});

test('user with two factor enabled completes login challenge', function () {
    if (! Features::enabled(Features::twoFactorAuthentication())) {
        $this->markTestSkipped('Two factor authentication feature is disabled.');
    }

    $google2fa = new Google2FA;
    $secret = $google2fa->generateSecretKey();

    $user = User::factory()->create([
        'email' => 'twofa-login@example.com',
        'password' => Hash::make('Str0ng!Passw0rd'),
        'email_verified_at' => now(),
        'phone_verified_at' => now(),
        'two_factor_secret' => Fortify::currentEncrypter()->encrypt($secret),
        'two_factor_confirmed_at' => now(),
    ]);

    $code = $google2fa->getCurrentOtp($secret);

    post('/login', [
        'email' => $user->email,
        'password' => 'Str0ng!Passw0rd',
    ])->assertRedirect('/two-factor-challenge');

    post('/two-factor-challenge', [
        'code' => $code,
    ])->assertRedirect('/');

    $this->assertAuthenticatedAs($user);
});
