<?php

use App\Models\User;
use App\Services\OAuth\OAuthPhoneResolver;
use App\Services\OAuth\OAuthPhoneResult;

test('normalize converts russian mobile numbers to e164', function () {
    $resolver = app(OAuthPhoneResolver::class);

    expect($resolver->normalize('+79037659418'))->toBe('+79037659418')
        ->and($resolver->normalize('79037659418'))->toBe('+79037659418')
        ->and($resolver->normalize('89037659418'))->toBe('+79037659418')
        ->and($resolver->normalize('9037659418'))->toBe('+79037659418');
});

test('extract phone reads yandex default_phone number', function () {
    $resolver = app(OAuthPhoneResolver::class);

    $phone = $resolver->extractPhone('yandex', [
        'default_phone' => ['number' => '+79037659418'],
    ]);

    expect($phone)->toBe('+79037659418');
});

test('extract phone reads vk phone field', function () {
    $resolver = app(OAuthPhoneResolver::class);

    $phone = $resolver->extractPhone('vk', [
        'phone' => '+79991234567',
    ]);

    expect($phone)->toBe('+79991234567');
});

test('apply to user verifies unique oauth phone', function () {
    $resolver = app(OAuthPhoneResolver::class);
    $user = User::factory()->create([
        'phone' => null,
        'phone_verified_at' => null,
        'oauth_provider' => 'yandex',
    ]);

    $result = $resolver->applyToUser($user, '+79991234567');

    expect($result)->toBe(OAuthPhoneResult::Verified)
        ->and($user->fresh()->phone)->toBe('+79991234567')
        ->and($user->fresh()->phone_verified_at)->not->toBeNull();
});

test('apply to user returns needs manual entry when phone is missing', function () {
    $resolver = app(OAuthPhoneResolver::class);
    $user = User::factory()->create([
        'phone' => null,
        'phone_verified_at' => null,
        'oauth_provider' => 'vk',
    ]);

    $result = $resolver->applyToUser($user, null);

    expect($result)->toBe(OAuthPhoneResult::NeedsManualEntry)
        ->and($user->fresh()->phone)->toBeNull();
});

test('apply to user clears unverified oauth placeholder phone before manual entry', function () {
    $resolver = app(OAuthPhoneResolver::class);
    $user = User::factory()->create([
        'phone' => '+79998887766',
        'phone_verified_at' => null,
        'oauth_provider' => 'yandex',
    ]);

    $result = $resolver->applyToUser($user, null);

    expect($result)->toBe(OAuthPhoneResult::NeedsManualEntry)
        ->and($user->fresh()->phone)->toBeNull();
});

test('apply to user returns already verified without overwriting phone', function () {
    $resolver = app(OAuthPhoneResolver::class);
    $user = User::factory()->create([
        'phone' => '+79991112233',
        'phone_verified_at' => now(),
        'oauth_provider' => 'yandex',
    ]);

    $result = $resolver->applyToUser($user, '+79994445566');

    expect($result)->toBe(OAuthPhoneResult::AlreadyVerified)
        ->and($user->fresh()->phone)->toBe('+79991112233');
});

test('apply to user returns needs manual entry when phone is already taken', function () {
    User::factory()->create(['phone' => '+79990001122']);

    $resolver = app(OAuthPhoneResolver::class);
    $user = User::factory()->create([
        'phone' => null,
        'phone_verified_at' => null,
        'oauth_provider' => 'yandex',
    ]);

    $result = $resolver->applyToUser($user, '+79990001122');

    expect($result)->toBe(OAuthPhoneResult::NeedsManualEntry)
        ->and($user->fresh()->phone)->toBeNull();
});
