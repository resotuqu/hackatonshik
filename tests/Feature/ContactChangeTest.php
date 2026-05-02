<?php

use App\Models\User;
use App\Services\ContactChangeService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

test('email change completes after old and new codes', function () {
    Notification::fake();

    $user = User::factory()->create([
        'email' => 'old@example.com',
        'phone' => '+79991112233',
        'email_verified_at' => now(),
    ]);

    $service = app(ContactChangeService::class);

    $service->startEmailChange($user, 'new@example.com');

    $payload = Cache::get('contact-email-change:'.$user->id);
    expect($payload)->not->toBeNull()
        ->and((int) $payload['step'])->toBe(1);

    $oldCode = $payload['code'];

    $service->verifyEmailChangeOldAndSendToNew($user, $oldCode);

    $payload = Cache::get('contact-email-change:'.$user->id);
    expect((int) $payload['step'])->toBe(2);
    $newCode = $payload['code'];

    $service->completeEmailChange($user, $newCode);

    expect($user->fresh()->email)->toBe('new@example.com')
        ->and($user->fresh()->email_verified_at)->not->toBeNull()
        ->and(Cache::get('contact-email-change:'.$user->id))->toBeNull();
});

test('email change rejects duplicate email', function () {
    User::factory()->create([
        'email' => 'taken@example.com',
        'phone' => '+79991112244',
    ]);

    $user = User::factory()->create([
        'email' => 'owner@example.com',
        'phone' => '+79991112233',
    ]);

    expect(fn () => app(ContactChangeService::class)->startEmailChange($user, 'taken@example.com'))
        ->toThrow(ValidationException::class);
});

test('phone change completes after email and sms codes', function () {
    $user = User::factory()->create([
        'email' => 'u@example.com',
        'phone' => '+79991112233',
        'phone_verified_at' => now(),
    ]);

    $service = app(ContactChangeService::class);

    $service->startPhoneChange($user, '+79991112255');

    $payload = Cache::get('contact-phone-change:'.$user->id);
    $emailCode = $payload['code'];

    $service->verifyPhoneChangeEmailAndSendSms($user, $emailCode);

    $payload = Cache::get('contact-phone-change:'.$user->id);
    expect((int) $payload['step'])->toBe(2);
    $smsCode = $payload['code'];

    $service->completePhoneChange($user, $smsCode);

    expect($user->fresh()->phone)->toBe('+79991112255')
        ->and($user->fresh()->phone_verified_at)->not->toBeNull()
        ->and(Cache::get('contact-phone-change:'.$user->id))->toBeNull();
});

test('phone change rejects wrong email step code', function () {
    $user = User::factory()->create([
        'email' => 'u2@example.com',
        'phone' => '+79991112233',
    ]);

    app(ContactChangeService::class)->startPhoneChange($user, '+79991112266');

    expect(fn () => app(ContactChangeService::class)->verifyPhoneChangeEmailAndSendSms($user, '000000'))
        ->toThrow(ValidationException::class);
});

test('email change send is rate limited', function () {
    $user = User::factory()->create([
        'email' => 'rate@example.com',
        'phone' => '+79991112277',
    ]);

    $key = 'email-change-send-old:'.$user->id;
    RateLimiter::clear($key);
    RateLimiter::hit($key, 60);
    RateLimiter::hit($key, 60);
    RateLimiter::hit($key, 60);

    expect(fn () => app(ContactChangeService::class)->startEmailChange($user, 'fresh@example.com'))
        ->toThrow(ValidationException::class);
});

test('cached email change expires when completing with wrong code', function () {
    $user = User::factory()->create([
        'email' => 'old2@example.com',
        'phone' => '+79991112288',
    ]);

    $service = app(ContactChangeService::class);
    $service->startEmailChange($user, 'final@example.com');

    $payload = Cache::get('contact-email-change:'.$user->id);
    $service->verifyEmailChangeOldAndSendToNew($user, $payload['code']);

    expect(fn () => $service->completeEmailChange($user, '000000'))
        ->toThrow(ValidationException::class);

    expect($user->fresh()->email)->toBe('old2@example.com');
});
