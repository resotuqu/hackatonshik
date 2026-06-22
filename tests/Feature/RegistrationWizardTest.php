<?php

use App\Enums\OrganizerApplicationStatus;
use App\Enums\OrganizerEntityType;
use App\Enums\UserRole;
use App\Livewire\Pages\Auth\Register as RegisterPage;
use App\Models\OrganizerApplication;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;

test('partner registration creates pending organizer application without partner role', function () {
    $suffix = uniqid('p', true);
    $email = "partner_{$suffix}@example.com";
    $nickname = "org_{$suffix}";
    $phone = '79'.str_pad((string) random_int(0, 999999999), 9, '0', STR_PAD_LEFT);

    Livewire::test(RegisterPage::class)
        ->set('accountType', 'partner')
        ->set('organizerEntityType', 'company')
        ->set('organizerCompanyName', 'ООО Тест Организатор')
        ->set('organizerNote', 'Мы проводим корпоративные хакатоны для IT-компаний в регионе.')
        ->set('fio', 'Организаторов Пётр Иванович')
        ->set('date_of_birth', '1990-01-01')
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
        ->set('pd_consent', true)
        ->call('save')
        ->assertRedirect(route('verification.notice'));

    $user = User::query()->where('email', $email)->firstOrFail();

    expect($user->role)->toBe(UserRole::USER)
        ->and($user->isOrganizer())->toBeFalse();

    $application = OrganizerApplication::query()->where('user_id', $user->id)->first();

    expect($application)->not->toBeNull()
        ->and($application->status)->toBe(OrganizerApplicationStatus::Pending)
        ->and($application->entity_type)->toBe(OrganizerEntityType::Company)
        ->and($application->company_name)->toBe('ООО Тест Организатор');
});

test('partner registration validates organizer application fields on step one', function () {
    Livewire::test(RegisterPage::class)
        ->set('accountType', 'partner')
        ->set('organizerEntityType', 'company')
        ->set('organizerCompanyName', '')
        ->set('organizerNote', 'коротко')
        ->set('fio', 'Организаторов Пётр Иванович')
        ->set('date_of_birth', '1990-01-01')
        ->call('nextStep')
        ->assertHasErrors(['organizerCompanyName', 'organizerNote'])
        ->assertSet('step', 1);
});

test('user registration does not create organizer application', function () {
    $suffix = uniqid('u', true);
    $email = "user_{$suffix}@example.com";
    $nickname = "usr_{$suffix}";
    $phone = '79'.str_pad((string) random_int(0, 999999999), 9, '0', STR_PAD_LEFT);

    Livewire::test(RegisterPage::class)
        ->set('accountType', 'user')
        ->set('fio', 'Участников Иван Петрович')
        ->set('date_of_birth', '1999-03-20')
        ->call('nextStep')
        ->set('email', $email)
        ->set('nickname', $nickname)
        ->call('nextStep')
        ->set('password', 'Password1!')
        ->set('password_confirmation', 'Password1!')
        ->call('nextStep')
        ->set('phone', $phone)
        ->set('pd_consent', true)
        ->call('save');

    $user = User::query()->where('email', $email)->firstOrFail();

    expect($user->role)->toBe(UserRole::USER)
        ->and(OrganizerApplication::query()->where('user_id', $user->id)->exists())->toBeFalse();
});

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
        ->set('pd_consent', true)
        ->call('save')
        ->assertRedirect(route('verification.notice'));

    $user = User::query()->where('email', $email)->first();

    expect($user)->not->toBeNull()
        ->and($user->nickname)->toBe($nickname)
        ->and($user->phone)->toBe($phone)
        ->and($user->hasVerifiedEmail())->toBeFalse()
        ->and($user->pd_consent_accepted_at)->not->toBeNull();

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

test('registration is rejected without pd consent', function () {
    $suffix = uniqid('nc', true);
    $email = "nc_{$suffix}@example.com";
    $nickname = "nc_{$suffix}";
    $phone = '79'.str_pad((string) random_int(0, 999999999), 9, '0', STR_PAD_LEFT);

    Livewire::test(RegisterPage::class)
        ->set('fio', 'Тестов Тест Тестович')
        ->set('date_of_birth', '1995-01-01')
        ->call('nextStep')
        ->set('email', $email)
        ->set('nickname', $nickname)
        ->call('nextStep')
        ->set('password', 'Password1!')
        ->set('password_confirmation', 'Password1!')
        ->call('nextStep')
        ->set('phone', $phone)
        ->set('pd_consent', false)
        ->call('save')
        ->assertHasErrors(['pd_consent']);

    expect(User::query()->where('email', $email)->exists())->toBeFalse();
});

test('invalid account type is rejected on step 1', function () {
    Livewire::test(RegisterPage::class)
        ->set('accountType', 'admin')
        ->set('fio', 'Тестов Тест Тестович')
        ->set('date_of_birth', '1995-01-01')
        ->call('nextStep')
        ->assertHasErrors(['accountType'])
        ->assertSet('step', 1);
});
