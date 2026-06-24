<?php

use App\Models\User;
use App\Support\OAuthRedirectUris;
use Illuminate\Support\Facades\Http;
use Laravel\Socialite\Contracts\User as SocialiteUserContract;
use Laravel\Socialite\Facades\Socialite;

test('yandex token page preserves oauth session nonce from login', function () {
    session([
        'oauth_token_nonce' => 'existing-nonce-value',
        'oauth_token_nonce_at' => now()->timestamp,
    ]);

    $this->get(route('auth.yandex.token-page'));

    expect(session('oauth_token_nonce'))->toBe('existing-nonce-value');
});

test('yandex token page submits access token to backend', function () {
    Http::fake([
        'login.yandex.ru/*' => Http::response([
            'id' => '42',
            'default_email' => 'token-page@example.com',
            'real_name' => 'Token Page User',
        ], 200),
    ]);

    $this->get(route('auth.yandex.token-page'));
    $nonce = (string) session('oauth_token_nonce');

    $response = $this->post(route('auth.yandex.token'), [
        'oauth_token_nonce' => $nonce,
        'access_token' => 'hash-flow-token',
    ]);

    $response->assertRedirect(route('auth.oauth.consent'));
    $this->assertAuthenticated();
    $this->assertDatabaseHas('users', [
        'email' => 'token-page@example.com',
        'fio' => 'Token Page User',
    ]);
});

test('yandex token page includes token handoff script', function () {
    $response = $this->get(route('auth.yandex.token-page'));

    $response->assertOk();
    $response->assertSee('YaSendSuggestToken', false);
    $response->assertSee(route('auth.yandex.token'), false);
    $response->assertSee('parseHashAccessToken', false);
});

test('oauth redirect uris are derived from app url routes', function () {
    expect(config('services.yandex.redirect'))->toBe(OAuthRedirectUris::yandexCallback())
        ->and(config('services.vkontakte.redirect'))->toBe(OAuthRedirectUris::vkCallback());
});

test('yandex callback creates local user and logs in', function () {
    $socialiteUser = mock(SocialiteUserContract::class);
    $socialiteUser->shouldReceive('getEmail')->andReturn('oauth-user@example.com');
    $socialiteUser->shouldReceive('getName')->andReturn('OAuth Tester');
    $socialiteUser->shouldReceive('getNickname')->andReturn('oauth_tester');
    $socialiteUser->shouldReceive('getId')->andReturn('321');
    $socialiteUser->shouldReceive('getRaw')->andReturn([]);

    $driver = mock();
    $driver->shouldReceive('user')->once()->andReturn($socialiteUser);

    Socialite::shouldReceive('driver')
        ->once()
        ->with('yandex')
        ->andReturn($driver);

    $response = $this->get('/auth/yandex/callback');

    $response->assertRedirect(route('auth.oauth.consent'));
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
    $socialiteUser->shouldReceive('getRaw')->andReturn([]);

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
    expect($response->headers->get('Location'))->toContain('id.vk.ru/authorize');
    expect($response->headers->get('Location'))->toContain('code_challenge=');
    $this->assertGuest();
});

test('vk callback creates local user and logs in', function () {
    session([
        'vk_oauth_state' => 'test-state-123',
        'vk_oauth_code_verifier' => 'test-code-verifier-that-is-long-enough-for-pkce-123456',
    ]);

    Http::fake([
        'id.vk.ru/oauth2/auth' => Http::response(['access_token' => 'fake-access-token'], 200),
        'id.vk.ru/oauth2/user_info' => Http::response([
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

    $response->assertRedirect(route('auth.oauth.consent'));
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
        'id.vk.ru/oauth2/user_info' => Http::response([
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

    $response->assertRedirect(route('auth.oauth.consent'));
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
        'id.vk.ru/oauth2/user_info' => Http::response([], 500),
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
        'id.vk.ru/oauth2/user_info' => Http::response([
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

// --- Account takeover prevention ---

test('yandex callback blocks login when email belongs to a password-only user', function () {
    User::factory()->create(['email' => 'existing@example.com']);

    $socialiteUser = mock(SocialiteUserContract::class);
    $socialiteUser->shouldReceive('getEmail')->andReturn('existing@example.com');
    $socialiteUser->shouldReceive('getName')->andReturn('Attacker');
    $socialiteUser->shouldReceive('getNickname')->andReturn('attacker');
    $socialiteUser->shouldReceive('getId')->andReturn('evil-id');
    $socialiteUser->shouldReceive('getRaw')->andReturn([]);

    $driver = mock();
    $driver->shouldReceive('user')->once()->andReturn($socialiteUser);

    Socialite::shouldReceive('driver')->once()->with('yandex')->andReturn($driver);

    $response = $this->get('/auth/yandex/callback');

    $response->assertRedirect(route('login'));
    $response->assertSessionHas('error');
    $this->assertGuest();
});

test('yandex token endpoint blocks login when email belongs to a password-only user', function () {
    User::factory()->create(['email' => 'existing@example.com']);

    Http::fake([
        'login.yandex.ru/*' => Http::response([
            'id' => '42',
            'default_email' => 'existing@example.com',
            'real_name' => 'Attacker',
        ], 200),
    ]);

    $response = $this->post(route('auth.yandex.token'), ['access_token' => 'fake-token']);

    $response->assertRedirect(route('login'));
    $response->assertSessionHas('error');
    $this->assertGuest();
});

test('vk token endpoint blocks login when email belongs to a password-only user', function () {
    User::factory()->create(['email' => 'existing@example.com']);

    Http::fake([
        'id.vk.ru/oauth2/user_info' => Http::response([
            'user' => [
                'user_id' => '777',
                'first_name' => 'Attacker',
                'last_name' => '',
                'email' => 'existing@example.com',
            ],
        ], 200),
    ]);

    $response = $this->post(route('auth.vk.token'), ['access_token' => 'fake-token']);

    $response->assertRedirect(route('login'));
    $response->assertSessionHas('error');
    $this->assertGuest();
});

test('yandex callback allows returning oauth user with matching provider and id', function () {
    User::factory()->create([
        'email' => 'ya@example.com',
        'oauth_provider' => 'yandex',
        'oauth_provider_id' => '321',
        'pd_consent_accepted_at' => now(),
        'date_of_birth' => '1990-01-01',
        'email_verified_at' => now(),
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

test('yandex token allows returning oauth user with matching provider and id', function () {
    User::factory()->create([
        'email' => 'ya@example.com',
        'oauth_provider' => 'yandex',
        'oauth_provider_id' => '42',
        'pd_consent_accepted_at' => now(),
        'date_of_birth' => '1990-01-01',
        'email_verified_at' => now(),
    ]);

    Http::fake([
        'login.yandex.ru/*' => Http::response([
            'id' => '42',
            'default_email' => 'ya@example.com',
            'real_name' => 'Yandex User',
        ], 200),
    ]);

    $response = $this->post(route('auth.yandex.token'), ['access_token' => 'real-token']);

    $response->assertRedirect(route('home'));
    $this->assertAuthenticated();
});

// --- 2FA challenge ---

test('yandex token redirects to 2fa challenge when user has 2fa enabled', function () {
    User::factory()->create([
        'email' => 'ya-2fa@example.com',
        'oauth_provider' => 'yandex',
        'oauth_provider_id' => '99',
        'two_factor_secret' => encrypt('totp_secret'),
        'two_factor_confirmed_at' => now(),
    ]);

    Http::fake([
        'login.yandex.ru/*' => Http::response([
            'id' => '99',
            'default_email' => 'ya-2fa@example.com',
            'real_name' => '2FA User',
        ], 200),
    ]);

    $response = $this->post(route('auth.yandex.token'), ['access_token' => 'real-token']);

    $response->assertRedirect(route('two-factor.login'));
    $this->assertGuest();
    expect(session('login.id'))->not->toBeNull();
});

test('vk token redirects to 2fa challenge when user has 2fa enabled', function () {
    User::factory()->create([
        'email' => 'vk-2fa@example.com',
        'oauth_provider' => 'vk',
        'oauth_provider_id' => '555',
        'two_factor_secret' => encrypt('totp_secret'),
        'two_factor_confirmed_at' => now(),
    ]);

    Http::fake([
        'id.vk.ru/oauth2/user_info' => Http::response([
            'user' => [
                'user_id' => '555',
                'first_name' => '2FA',
                'last_name' => 'User',
                'email' => 'vk-2fa@example.com',
            ],
        ], 200),
    ]);

    $response = $this->post(route('auth.vk.token'), ['access_token' => 'real-token']);

    $response->assertRedirect(route('two-factor.login'));
    $this->assertGuest();
    expect(session('login.id'))->not->toBeNull();
});

test('yandex token endpoint is rate limited', function () {
    Http::fake([
        'login.yandex.ru/*' => Http::response(['id' => '1', 'default_email' => 'a@example.com'], 200),
    ]);

    for ($i = 0; $i < 5; $i++) {
        $this->post(route('auth.yandex.token'), ['access_token' => "token-{$i}"]);
    }

    $response = $this->post(route('auth.yandex.token'), ['access_token' => 'token-final']);

    $response->assertStatus(429);
});

test('vk token endpoint is rate limited', function () {
    Http::fake([
        'id.vk.ru/oauth2/user_info' => Http::response([
            'user' => ['user_id' => '1', 'email' => 'a@example.com', 'first_name' => 'A', 'last_name' => 'B'],
        ], 200),
    ]);

    for ($i = 0; $i < 5; $i++) {
        $this->post(route('auth.vk.token'), ['access_token' => "token-{$i}"]);
    }

    $response = $this->post(route('auth.vk.token'), ['access_token' => 'token-final']);

    $response->assertStatus(429);
});
