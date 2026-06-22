<?php

use Illuminate\Support\Facades\Http;
use Laravel\Socialite\Contracts\User as SocialiteUserContract;
use Laravel\Socialite\Facades\Socialite;

test('yandex callback creates local user and logs in', function () {
    $socialiteUser = mock(SocialiteUserContract::class);
    $socialiteUser->shouldReceive('getEmail')->andReturn('oauth-user@example.com');
    $socialiteUser->shouldReceive('getName')->andReturn('OAuth Tester');
    $socialiteUser->shouldReceive('getNickname')->andReturn('oauth_tester');
    $socialiteUser->shouldReceive('getId')->andReturn('321');

    $driver = mock();
    $driver->shouldReceive('user')->once()->andReturn($socialiteUser);

    Socialite::shouldReceive('driver')
        ->once()
        ->with('yandex')
        ->andReturn($driver);

    $response = $this->get('/auth/yandex/callback');

    $response->assertRedirect(route('phone.verify.notice'));
    $this->assertAuthenticated();
    $this->assertDatabaseHas('users', [
        'email' => 'oauth-user@example.com',
        'fio' => 'OAuth Tester',
    ]);
});

test('yandex callback redirects to login when provider has no email', function () {
    $socialiteUser = mock(SocialiteUserContract::class);
    $socialiteUser->shouldReceive('getEmail')->andReturnNull();
    $socialiteUser->shouldReceive('getName')->andReturnNull();
    $socialiteUser->shouldReceive('getNickname')->andReturn('oauth_no_email');
    $socialiteUser->shouldReceive('getId')->andReturn('12345');

    $driver = mock();
    $driver->shouldReceive('user')->once()->andReturn($socialiteUser);

    Socialite::shouldReceive('driver')
        ->once()
        ->with('yandex')
        ->andReturn($driver);

    $response = $this->get('/auth/yandex/callback');

    $response->assertRedirect(route('login'));
    $response->assertSessionHas('error');
    $this->assertGuest();
    $this->assertDatabaseCount('users', 0);
});

test('yandex oauth callback redirects to login when provider throws', function () {
    $providerDriver = mock();
    $providerDriver->shouldReceive('user')->once()->andThrow(new RuntimeException('OAuth provider error'));

    Socialite::shouldReceive('driver')
        ->once()
        ->with('yandex')
        ->andReturn($providerDriver);

    $response = $this->get('/auth/yandex/callback');

    $response->assertRedirect(route('login'));
    $response->assertSessionHas('error', 'Не удалось выполнить вход через OAuth.');
    $this->assertGuest();
});

test('vk redirect initiates oauth flow', function () {
    $response = $this->get(route('auth.vk.redirect'));

    $response->assertRedirect();
    expect($response->headers->get('Location'))->toContain('id.vk.com/oauth2/auth');
    $this->assertGuest();
});

test('vk callback creates local user and logs in', function () {
    session(['vk_oauth_state' => 'test-state-123']);

    Http::fake([
        'id.vk.com/oauth2/auth' => Http::response(['access_token' => 'fake-access-token'], 200),
        'id.vk.com/oauth2/user_info' => Http::response([
            'user' => [
                'user_id' => '999',
                'first_name' => 'VK',
                'last_name' => 'Tester',
                'email' => 'vk-user@example.com',
            ],
        ], 200),
    ]);

    $response = $this->get(route('auth.vk.callback', [
        'code' => 'fake-code',
        'device_id' => 'fake-device-id',
        'state' => 'test-state-123',
    ]));

    $response->assertRedirect(route('phone.verify.notice'));
    $this->assertAuthenticated();
    $this->assertDatabaseHas('users', [
        'email' => 'vk-user@example.com',
        'fio' => 'VK Tester',
    ]);
});

test('vk callback redirects to login on invalid state', function () {
    session(['vk_oauth_state' => 'correct-state']);

    $response = $this->get(route('auth.vk.callback', [
        'code' => 'fake-code',
        'state' => 'wrong-state',
    ]));

    $response->assertRedirect(route('login'));
    $response->assertSessionHas('error');
    $this->assertGuest();
});

test('vk token endpoint creates local user and logs in', function () {
    Http::fake([
        'id.vk.com/oauth2/user_info' => Http::response([
            'user' => [
                'user_id' => '999',
                'first_name' => 'VK',
                'last_name' => 'Tester',
                'email' => 'vk-user@example.com',
            ],
        ], 200),
    ]);

    $response = $this->post(route('auth.vk.token'), [
        'access_token' => 'fake-vk-access-token',
    ]);

    $response->assertRedirect(route('phone.verify.notice'));
    $this->assertAuthenticated();
    $this->assertDatabaseHas('users', [
        'email' => 'vk-user@example.com',
        'fio' => 'VK Tester',
    ]);
});

test('vk token endpoint redirects to login when access_token is missing', function () {
    $response = $this->post(route('auth.vk.token'), []);

    $response->assertRedirect(route('login'));
    $response->assertSessionHas('error');
    $this->assertGuest();
});

test('vk token endpoint redirects to login when vk api fails', function () {
    Http::fake([
        'id.vk.com/oauth2/user_info' => Http::response([], 500),
    ]);

    $response = $this->post(route('auth.vk.token'), [
        'access_token' => 'fake-token',
    ]);

    $response->assertRedirect(route('login'));
    $response->assertSessionHas('error');
    $this->assertGuest();
});

test('vk token endpoint redirects to login when email is missing', function () {
    Http::fake([
        'id.vk.com/oauth2/user_info' => Http::response([
            'user' => [
                'user_id' => '999',
                'first_name' => 'No',
                'last_name' => 'Email',
                'email' => '',
            ],
        ], 200),
    ]);

    $response = $this->post(route('auth.vk.token'), [
        'access_token' => 'fake-token',
    ]);

    $response->assertRedirect(route('login'));
    $response->assertSessionHas('error');
    $this->assertGuest();
});
