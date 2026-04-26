<?php

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;

test('authenticated user can request sms verification code', function () {
    config()->set('services.plusofon.api_url', 'https://sms.test/send');
    config()->set('services.plusofon.token', 'token');
    config()->set('services.plusofon.sender', 'Hackatonshik');

    Http::fake([
        'https://sms.test/send' => Http::response(['ok' => true], 200),
    ]);

    $user = User::factory()->create([
        'phone_verified_at' => null,
    ]);

    $response = $this
        ->actingAs($user)
        ->post(route('phone.verify.send'));

    $response->assertSessionHas('success');
    expect(Cache::get("phone-verification-code:{$user->id}"))->not->toBeNull();
});

test('authenticated user can verify phone with sms code', function () {
    $user = User::factory()->create([
        'phone_verified_at' => null,
    ]);

    Cache::put("phone-verification-code:{$user->id}", '123456', now()->addMinutes(10));

    $response = $this
        ->actingAs($user)
        ->post(route('phone.verify'), [
            'code' => '123456',
        ]);

    $response->assertRedirect(route('home'));
    expect($user->fresh()->phone_verified_at)->not->toBeNull();
});

test('send sms code is rate limited after too many attempts', function () {
    $user = User::factory()->create([
        'phone_verified_at' => null,
    ]);

    $key = "phone-verification-send:{$user->id}";
    RateLimiter::clear($key);
    RateLimiter::hit($key, 60);
    RateLimiter::hit($key, 60);
    RateLimiter::hit($key, 60);

    $response = $this
        ->actingAs($user)
        ->post(route('phone.verify.send'));

    $response->assertSessionHas('error', 'Слишком много запросов. Попробуйте позже.');
});

test('phone verification fails with expired sms code', function () {
    $user = User::factory()->create([
        'phone_verified_at' => null,
    ]);

    Cache::forget("phone-verification-code:{$user->id}");

    $response = $this
        ->actingAs($user)
        ->from(route('phone.verify.notice'))
        ->post(route('phone.verify'), [
            'code' => '123456',
        ]);

    $response->assertRedirect(route('phone.verify.notice'));
    $response->assertSessionHasErrors('code');
    expect($user->fresh()->phone_verified_at)->toBeNull();
});
