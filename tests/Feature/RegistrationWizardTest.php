<?php

use App\Livewire\Pages\Auth\Register as RegisterPage;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;

test('registration wizard completes all steps and redirects to phone verification', function () {
    $suffix = uniqid('', true);
    $email = "wizard_{$suffix}@example.com";
    $nickname = "nick_{$suffix}";
    $phone = '79'.str_pad((string) random_int(0, 999999999), 9, '0', STR_PAD_LEFT);

    Livewire::test(RegisterPage::class)
        ->set('fio', 'Тестов Пользователь Иванович')
        ->set('date_of_birth', '1995-06-15')
        ->call('nextStep')
        ->assertSet('step', 2)
        ->set('email', $email)
        ->set('nickname', $nickname)
        ->call('nextStep')
        ->assertSet('step', 3)
        ->set('password', 'Password1!')
        ->set('password_confirmation', 'Password1!')
        ->call('nextStep')
        ->assertSet('step', 4)
        ->set('phone', $phone)
        ->call('save')
        ->assertRedirect(route('verification.notice'));

    $user = User::query()->where('email', $email)->first();

    expect($user)->not->toBeNull()
        ->and($user->nickname)->toBe($nickname)
        ->and($user->phone)->toBe($phone)
        ->and($user->hasVerifiedEmail())->toBeFalse();

    expect(Auth::check())->toBeTrue()
        ->and(Auth::id())->toBe($user->id);
});

test('registration wizard stays on step one when personal data is invalid', function () {
    Livewire::test(RegisterPage::class)
        ->set('fio', '')
        ->set('date_of_birth', '')
        ->call('nextStep')
        ->assertHasErrors(['fio', 'date_of_birth'])
        ->assertSet('step', 1);
});
