<?php

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

test('oauth callback redirects to login when provider throws', function (string $path, string $driver) {
    $providerDriver = mock();
    $providerDriver->shouldReceive('user')->once()->andThrow(new RuntimeException('OAuth provider error'));

    Socialite::shouldReceive('driver')
        ->once()
        ->with($driver)
        ->andReturn($providerDriver);

    $response = $this->get($path);

    $response->assertRedirect(route('login'));
    $response->assertSessionHas('error', 'Не удалось выполнить вход через OAuth.');
    $this->assertGuest();
})->with([
    'yandex' => ['/auth/yandex/callback', 'yandex'],
    'vk' => ['/auth/vk/callback', 'vkontakte'],
]);

test('vk callback creates local user and logs in', function () {
    $socialiteUser = mock(SocialiteUserContract::class);
    $socialiteUser->shouldReceive('getEmail')->andReturn('vk-user@example.com');
    $socialiteUser->shouldReceive('getName')->andReturn('VK Tester');
    $socialiteUser->shouldReceive('getNickname')->andReturn('vk_tester');
    $socialiteUser->shouldReceive('getId')->andReturn('999');

    $driver = mock();
    $driver->shouldReceive('user')->once()->andReturn($socialiteUser);

    Socialite::shouldReceive('driver')
        ->once()
        ->with('vkontakte')
        ->andReturn($driver);

    $response = $this->get('/auth/vk/callback');

    $response->assertRedirect(route('phone.verify.notice'));
    $this->assertAuthenticated();
    $this->assertDatabaseHas('users', [
        'email' => 'vk-user@example.com',
        'fio' => 'VK Tester',
    ]);
});
