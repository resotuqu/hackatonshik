<?php

declare(strict_types=1);

use App\Livewire\Pages\Auth\Login;
use App\Livewire\Pages\Profile\TwoFactor;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\Fortify;
use Livewire\Livewire;
use PragmaRX\Google2FA\Google2FA;

test('livewire login redirects to challenge when two factor is enabled', function () {
    $google2fa = new Google2FA;
    $secret = $google2fa->generateSecretKey();

    $user = User::factory()->create([
        'email' => 'lw-2fa@example.com',
        'password' => Hash::make('Str0ng!Passw0rd'),
        'email_verified_at' => now(),
        'phone_verified_at' => now(),
        'two_factor_secret' => Fortify::currentEncrypter()->encrypt($secret),
        'two_factor_confirmed_at' => now(),
    ]);

    Livewire::test(Login::class)
        ->set('email', $user->email)
        ->set('password', 'Str0ng!Passw0rd')
        ->call('save')
        ->assertRedirect(route('two-factor.login'));

    expect(session('login.id'))->toBe($user->getKey());
    $this->assertGuest();
});

test('livewire login signs in directly without two factor', function () {
    $user = User::factory()->create([
        'email' => 'lw-no2fa@example.com',
        'password' => Hash::make('Str0ng!Passw0rd'),
        'email_verified_at' => now(),
        'phone_verified_at' => now(),
    ]);

    Livewire::test(Login::class)
        ->set('email', $user->email)
        ->set('password', 'Str0ng!Passw0rd')
        ->call('save')
        ->assertRedirect('/');

    $this->assertAuthenticatedAs($user);
});

test('two factor challenge view renders for a challenged user', function () {
    $user = User::factory()->create([
        'two_factor_secret' => Fortify::currentEncrypter()->encrypt('SECRET'),
        'two_factor_confirmed_at' => now(),
    ]);

    $this->withSession(['login.id' => $user->getKey()])
        ->get('/two-factor-challenge')
        ->assertOk()
        ->assertSee('Подтверждение входа');
});

test('user enables and confirms two factor from profile component', function () {
    $user = User::factory()->create([
        'password' => Hash::make('Str0ng!Passw0rd'),
        'email_verified_at' => now(),
        'phone_verified_at' => now(),
    ]);

    $component = Livewire::actingAs($user)->test(TwoFactor::class)
        ->set('password', 'Str0ng!Passw0rd')
        ->call('enableTwoFactor')
        ->assertHasNoErrors()
        ->assertSet('confirming', true);

    $secret = Fortify::currentEncrypter()->decrypt($user->fresh()->two_factor_secret);
    $code = (new Google2FA)->getCurrentOtp($secret);

    $component->set('code', $code)
        ->call('confirmTwoFactor')
        ->assertHasNoErrors()
        ->assertSet('enabled', true)
        ->assertSet('showingRecoveryCodes', true);

    expect($user->fresh()->two_factor_confirmed_at)->not->toBeNull();
});

test('enable is rejected with wrong password', function () {
    $user = User::factory()->create([
        'password' => Hash::make('Str0ng!Passw0rd'),
    ]);

    Livewire::actingAs($user)->test(TwoFactor::class)
        ->set('password', 'wrong-password')
        ->call('enableTwoFactor')
        ->assertHasErrors('password');

    expect($user->fresh()->two_factor_secret)->toBeNull();
});

test('user disables two factor with correct password', function () {
    $user = User::factory()->create([
        'password' => Hash::make('Str0ng!Passw0rd'),
        'two_factor_secret' => Fortify::currentEncrypter()->encrypt('SECRET'),
        'two_factor_confirmed_at' => now(),
    ]);

    Livewire::actingAs($user)->test(TwoFactor::class)
        ->assertSet('enabled', true)
        ->set('password', 'Str0ng!Passw0rd')
        ->call('disableTwoFactor')
        ->assertHasNoErrors()
        ->assertSet('enabled', false);

    expect($user->fresh()->two_factor_secret)->toBeNull();
});
