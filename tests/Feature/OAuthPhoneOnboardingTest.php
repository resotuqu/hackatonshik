<?php

use App\Livewire\Pages\Auth\OAuthConsent;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Laravel\Socialite\Contracts\User as SocialiteUserContract;
use Laravel\Socialite\Facades\Socialite;
use Livewire\Livewire;

test('authenticated user can store phone before verification', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
        'phone' => null,
        'phone_verified_at' => null,
    ]);

    $response = $this->actingAs($user)->post(route('phone.verify.phone'), [
        'phone' => '9991234567',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    expect($user->fresh()->phone)->toBe('+79991234567');
});

test('send code requires phone to be set first', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
        'phone' => null,
        'phone_verified_at' => null,
    ]);

    $response = $this->actingAs($user)->post(route('phone.verify.send'));

    $response->assertRedirect();
    $response->assertSessionHas('error', 'Сначала укажите номер телефона.');
});

test('oauth user without phone completes manual onboarding flow', function () {
    config()->set('services.plusofon_flash_call.enabled', true);
    config()->set('services.plusofon_flash_call.base_url', 'https://restapi.plusofon.ru/api/v1');
    config()->set('services.plusofon_flash_call.token', 'test-token');
    config()->set('services.plusofon_flash_call.client_id', '10553');

    Http::fake([
        'https://restapi.plusofon.ru/api/v1/flash-call/send' => Http::response(['call_id' => 'xyz'], 200),
    ]);

    $socialiteUser = mock(SocialiteUserContract::class);
    $socialiteUser->shouldReceive('getEmail')->andReturn('manual-oauth@example.com');
    $socialiteUser->shouldReceive('getName')->andReturn('Manual OAuth');
    $socialiteUser->shouldReceive('getNickname')->andReturn('manual_oauth');
    $socialiteUser->shouldReceive('getId')->andReturn('manual-1');
    $socialiteUser->shouldReceive('getRaw')->andReturn([]);

    $driver = mock();
    $driver->shouldReceive('user')->once()->andReturn($socialiteUser);

    Socialite::shouldReceive('driver')
        ->once()
        ->with('yandex')
        ->andReturn($driver);

    $this->get('/auth/yandex/callback')->assertRedirect(route('auth.oauth.consent'));

    $user = User::query()->where('email', 'manual-oauth@example.com')->first();
    expect($user)->not->toBeNull()
        ->and($user->phone)->toBeNull()
        ->and($user->phone_verified_at)->toBeNull();

    Livewire::actingAs($user)
        ->test(OAuthConsent::class)
        ->set('pd_consent', true)
        ->set('date_of_birth', '1995-01-01')
        ->call('save')
        ->assertRedirect(route('phone.verify.notice'));

    $this->actingAs($user)
        ->post(route('phone.verify.phone'), ['phone' => '9992223344'])
        ->assertRedirect();

    $this->actingAs($user->fresh())
        ->post(route('phone.verify.send'))
        ->assertSessionHas('success');

    $code = Cache::get("phone-verification-code:{$user->id}");

    $this->actingAs($user->fresh())
        ->post(route('phone.verify'), ['code' => $code])
        ->assertRedirect(route('home'));

    expect($user->fresh()->phone_verified_at)->not->toBeNull();
});

test('yandex callback auto verifies phone when provider returns default phone', function () {
    $socialiteUser = mock(SocialiteUserContract::class);
    $socialiteUser->shouldReceive('getEmail')->andReturn('verified-oauth@example.com');
    $socialiteUser->shouldReceive('getName')->andReturn('Verified OAuth');
    $socialiteUser->shouldReceive('getNickname')->andReturn('verified_oauth');
    $socialiteUser->shouldReceive('getId')->andReturn('verified-1');
    $socialiteUser->shouldReceive('getRaw')->andReturn([
        'default_phone' => ['number' => '+79991112233'],
    ]);

    $driver = mock();
    $driver->shouldReceive('user')->once()->andReturn($socialiteUser);

    Socialite::shouldReceive('driver')
        ->once()
        ->with('yandex')
        ->andReturn($driver);

    $response = $this->get('/auth/yandex/callback');

    $response->assertRedirect(route('auth.oauth.consent'));
    $this->assertAuthenticated();

    $user = User::query()->where('email', 'verified-oauth@example.com')->first();
    expect($user->phone)->toBe('+79991112233')
        ->and($user->phone_verified_at)->not->toBeNull();

    Livewire::actingAs($user)
        ->test(OAuthConsent::class)
        ->set('pd_consent', true)
        ->set('date_of_birth', '1990-01-01')
        ->call('save')
        ->assertRedirect(route('home'));
});

test('yandex token auto verifies phone when default phone is present', function () {
    Http::fake([
        'login.yandex.ru/*' => Http::response([
            'id' => '55',
            'default_email' => 'ya-phone@example.com',
            'real_name' => 'Yandex Phone User',
            'default_phone' => ['number' => '+79993334455'],
        ], 200),
    ]);

    $response = $this->post(route('auth.yandex.token'), ['access_token' => 'fake-token']);

    $response->assertRedirect(route('auth.oauth.consent'));
    $this->assertAuthenticated();

    $user = User::query()->where('email', 'ya-phone@example.com')->first();
    expect($user->phone)->toBe('+79993334455')
        ->and($user->phone_verified_at)->not->toBeNull();

    Livewire::actingAs($user)
        ->test(OAuthConsent::class)
        ->set('pd_consent', true)
        ->set('date_of_birth', '1990-01-01')
        ->call('save')
        ->assertRedirect(route('home'));
});

test('vk callback auto verifies phone when provider returns phone', function () {
    session(['vk_oauth_state' => 'test-state-456']);

    Http::fake([
        'id.vk.com/oauth2/auth' => Http::response(['access_token' => 'fake-access-token'], 200),
        'id.vk.com/oauth2/user_info' => Http::response([
            'user' => [
                'user_id' => '888',
                'first_name' => 'VK',
                'last_name' => 'Phone',
                'email' => 'vk-phone@example.com',
                'phone' => '+79995556677',
            ],
        ], 200),
    ]);

    $response = $this->get(route('auth.vk.callback', [
        'code' => 'fake-code',
        'device_id' => 'fake-device-id',
        'state' => 'test-state-456',
    ]));

    $response->assertRedirect(route('auth.oauth.consent'));
    $this->assertAuthenticated();

    $user = User::query()->where('email', 'vk-phone@example.com')->first();
    expect($user->phone)->toBe('+79995556677')
        ->and($user->phone_verified_at)->not->toBeNull();

    Livewire::actingAs($user)
        ->test(OAuthConsent::class)
        ->set('pd_consent', true)
        ->set('date_of_birth', '1990-01-01')
        ->call('save')
        ->assertRedirect(route('home'));
});

test('returning oauth user with verified phone redirects to home', function () {
    User::factory()->create([
        'email' => 'ya@example.com',
        'oauth_provider' => 'yandex',
        'oauth_provider_id' => '321',
        'phone' => '+79991112233',
        'phone_verified_at' => now(),
        'email_verified_at' => now(),
        'pd_consent_accepted_at' => now(),
        'date_of_birth' => '1990-01-01',
    ]);

    $socialiteUser = mock(SocialiteUserContract::class);
    $socialiteUser->shouldReceive('getEmail')->andReturn('ya@example.com');
    $socialiteUser->shouldReceive('getName')->andReturn('Yandex User');
    $socialiteUser->shouldReceive('getNickname')->andReturn('ya_user');
    $socialiteUser->shouldReceive('getId')->andReturn('321');
    $socialiteUser->shouldReceive('getRaw')->andReturn([]);

    $driver = mock();
    $driver->shouldReceive('user')->once()->andReturn($socialiteUser);

    Socialite::shouldReceive('driver')->once()->with('yandex')->andReturn($driver);

    $response = $this->get('/auth/yandex/callback');

    $response->assertRedirect(route('home'));
    $this->assertAuthenticated();
});

test('returning oauth user with placeholder phone clears phone and requires manual entry', function () {
    User::factory()->create([
        'email' => 'placeholder@example.com',
        'oauth_provider' => 'yandex',
        'oauth_provider_id' => '777',
        'phone' => '+79998887766',
        'phone_verified_at' => null,
        'email_verified_at' => now(),
        'pd_consent_accepted_at' => now(),
        'date_of_birth' => '1990-01-01',
    ]);

    $socialiteUser = mock(SocialiteUserContract::class);
    $socialiteUser->shouldReceive('getEmail')->andReturn('placeholder@example.com');
    $socialiteUser->shouldReceive('getName')->andReturn('Placeholder User');
    $socialiteUser->shouldReceive('getNickname')->andReturn('placeholder_user');
    $socialiteUser->shouldReceive('getId')->andReturn('777');
    $socialiteUser->shouldReceive('getRaw')->andReturn([]);

    $driver = mock();
    $driver->shouldReceive('user')->once()->andReturn($socialiteUser);

    Socialite::shouldReceive('driver')->once()->with('yandex')->andReturn($driver);

    $response = $this->get('/auth/yandex/callback');

    $response->assertRedirect(route('phone.verify.notice'));

    $user = User::query()->where('email', 'placeholder@example.com')->first();
    expect($user->phone)->toBeNull();
});
