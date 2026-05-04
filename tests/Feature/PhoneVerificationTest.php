<?php

use App\Models\User;
use App\Services\Sms\PlusofonFlashCallSender;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;

test('authenticated user can request flash call verification code', function () {
    config()->set('services.plusofon_flash_call.enabled', true);
    config()->set('services.plusofon_flash_call.base_url', 'https://restapi.plusofon.ru/api/v1');
    config()->set('services.plusofon_flash_call.token', 'test-token');
    config()->set('services.plusofon_flash_call.client_id', '10553');

    Http::fake([
        'https://restapi.plusofon.ru/api/v1/flash-call/send' => Http::response(['call_id' => 'xyz'], 200),
    ]);

    $user = User::factory()->create([
        'phone_verified_at' => null,
        'phone' => '+79991234567',
    ]);

    $response = $this
        ->actingAs($user)
        ->post(route('phone.verify.send'));

    $response->assertSessionHas('success');

    $cachedCode = Cache::get("phone-verification-code:{$user->id}");
    expect($cachedCode)->not->toBeNull()
        ->and(strlen((string) $cachedCode))->toBe(4)
        ->and(ctype_digit((string) $cachedCode))->toBeTrue();

    expect(Cache::get(PlusofonFlashCallSender::callIdCacheKeyForPhone('+79991234567')))->toBe('xyz');

    Http::assertSent(function ($request): bool {
        if ($request->url() !== 'https://restapi.plusofon.ru/api/v1/flash-call/send') {
            return false;
        }

        $authorization = $request->header('Authorization')[0] ?? '';
        if (! str_starts_with($authorization, 'Bearer ')) {
            return false;
        }

        $clientHeader = $request->header('Client')[0] ?? '';
        if ($clientHeader !== '10553') {
            return false;
        }

        $data = $request->data();
        $pin = $data['pin'] ?? '';
        $phone = $data['phone'] ?? '';

        return $phone === '+79991234567'
            && is_string($pin)
            && strlen($pin) === 4
            && ctype_digit($pin);
    });
});

test('authenticated user can verify phone with flash call code', function () {
    $user = User::factory()->create([
        'phone_verified_at' => null,
    ]);

    Cache::put("phone-verification-code:{$user->id}", '1234', now()->addMinutes(10));

    $response = $this
        ->actingAs($user)
        ->post(route('phone.verify'), [
            'code' => '1234',
        ]);

    $response->assertRedirect(route('home'));
    expect($user->fresh()->phone_verified_at)->not->toBeNull();
});

test('flash call code request is rate limited after too many attempts', function () {
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

test('flash call send fails when api returns http 200 with success false', function () {
    config()->set('services.plusofon_flash_call.enabled', true);
    config()->set('services.plusofon_flash_call.base_url', 'https://restapi.plusofon.ru/api/v1');
    config()->set('services.plusofon_flash_call.token', 'test-token');
    config()->set('services.plusofon_flash_call.client_id', '10553');

    Http::fake([
        'https://restapi.plusofon.ru/api/v1/flash-call/send' => Http::response([
            'message' => 'error find flashcall account: Flashcall account not found',
            'code' => 500,
            'success' => false,
        ], 200),
    ]);

    $user = User::factory()->create([
        'phone_verified_at' => null,
        'phone' => '+79991234567',
    ]);

    RateLimiter::clear("phone-verification-send:{$user->id}");

    $response = $this
        ->actingAs($user)
        ->from(route('phone.verify.notice'))
        ->post(route('phone.verify.send'));

    $response->assertRedirect(route('phone.verify.notice'));
    $response->assertSessionHasErrors('code');
    expect(Cache::get(PlusofonFlashCallSender::callIdCacheKeyForPhone('+79991234567')))->toBeNull();
});

test('phone verification fails with expired flash call code', function () {
    $user = User::factory()->create([
        'phone_verified_at' => null,
    ]);

    Cache::forget("phone-verification-code:{$user->id}");

    $response = $this
        ->actingAs($user)
        ->from(route('phone.verify.notice'))
        ->post(route('phone.verify'), [
            'code' => '1234',
        ]);

    $response->assertRedirect(route('phone.verify.notice'));
    $response->assertSessionHasErrors('code');
    expect($user->fresh()->phone_verified_at)->toBeNull();
});
