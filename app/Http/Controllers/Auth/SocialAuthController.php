<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Throwable;

class SocialAuthController extends Controller
{
    /**
     * @var array<string, string>
     */
    private const PROVIDERS = [
        'yandex' => 'yandex',
        'vk' => 'vkontakte',
    ];

    public function redirect(string $provider): RedirectResponse
    {
        return Socialite::driver($this->resolveDriver($provider))->redirect();
    }

    public function callback(string $provider): RedirectResponse
    {
        try {
            $oauthUser = Socialite::driver($this->resolveDriver($provider))->user();
        } catch (Throwable) {
            return redirect()->route('login')->with('error', 'Не удалось выполнить вход через OAuth.');
        }

        $driver = $this->resolveDriver($provider);
        $email = mb_strtolower((string) $oauthUser->getEmail());
        if ($email === '') {
            return redirect()
                ->route('login')
                ->with('error', 'Провайдер не передал email. Выберите другой способ входа или укажите email у провайдера.');
        }

        $name = $oauthUser->getName() ?: $oauthUser->getNickname() ?: 'OAuth пользователь';

        /** @var Authenticatable $user */
        $user = User::query()->firstOrCreate(
            ['email' => $email],
            [
                'fio' => $name,
                'nickname' => "{$driver}_".Str::lower(Str::random(10)),
                'date_of_birth' => now()->subYears(18)->toDateString(),
                'phone' => $this->uniquePlaceholderPhone(),
                'password' => Hash::make(Str::random(40)),
                'phone_verified_at' => null,
                'email_verified_at' => now(),
            ],
        );

        Auth::login($user);

        return redirect()->route('phone.verify.notice');
    }

    public function yandexTokenPage(): View
    {
        return view('auth.yandex-token-page');
    }

    public function yandexToken(Request $request): RedirectResponse
    {
        $accessToken = $request->string('access_token')->toString();

        if (blank($accessToken)) {
            return redirect()->route('login')->with('error', 'Не удалось получить токен Яндекс ID.');
        }

        $response = Http::withToken($accessToken)->get('https://login.yandex.ru/info', [
            'format' => 'json',
        ]);

        if ($response->failed()) {
            return redirect()->route('login')->with('error', 'Не удалось получить данные пользователя Яндекс ID.');
        }

        $yaUser = $response->json();
        $email = mb_strtolower((string) ($yaUser['default_email'] ?? ''));

        if ($email === '') {
            return redirect()->route('login')
                ->with('error', 'Яндекс ID не передал email. Укажите email в настройках Яндекса.');
        }

        $name = ($yaUser['real_name'] ?? '') ?: ($yaUser['display_name'] ?? '') ?: 'Яндекс пользователь';

        /** @var Authenticatable $user */
        $user = User::query()->firstOrCreate(
            ['email' => $email],
            [
                'fio' => $name,
                'nickname' => 'ya_'.Str::lower(Str::random(10)),
                'oauth_provider' => 'yandex',
                'oauth_provider_id' => (string) ($yaUser['id'] ?? ''),
                'email_verified_at' => now(),
                'pd_consent_accepted_at' => now(),
            ],
        );

        Auth::login($user);

        return redirect()->route('phone.verify.notice');
    }

    public function vkRedirect(): RedirectResponse
    {
        $state = Str::random(40);
        session(['vk_oauth_state' => $state]);

        $query = http_build_query([
            'response_type' => 'code',
            'client_id' => config('services.vkontakte.client_id'),
            'redirect_uri' => route('auth.vk.callback'),
            'scope' => 'email',
            'state' => $state,
        ]);

        return redirect('https://id.vk.com/oauth2/auth?'.$query);
    }

    public function vkCallback(Request $request): RedirectResponse
    {
        if ($request->query('state') !== session('vk_oauth_state')) {
            return redirect()->route('login')->with('error', 'Не удалось выполнить вход через VK ID.');
        }

        $code = (string) $request->query('code', '');
        $deviceId = (string) $request->query('device_id', '');

        if (blank($code)) {
            return redirect()->route('login')->with('error', 'Не удалось выполнить вход через VK ID.');
        }

        $tokenResponse = Http::asForm()->post('https://id.vk.com/oauth2/auth', [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'device_id' => $deviceId,
            'redirect_uri' => route('auth.vk.callback'),
            'client_id' => config('services.vkontakte.client_id'),
            'client_secret' => config('services.vkontakte.client_secret'),
        ]);

        if ($tokenResponse->failed() || blank($tokenResponse->json('access_token'))) {
            return redirect()->route('login')->with('error', 'Не удалось получить токен VK ID.');
        }

        return $this->loginVkUser($tokenResponse->json('access_token'));
    }

    public function vkToken(Request $request): RedirectResponse
    {
        $accessToken = $request->string('access_token')->toString();

        if (blank($accessToken)) {
            return redirect()->route('login')->with('error', 'Не удалось получить токен VK ID.');
        }

        return $this->loginVkUser($accessToken);
    }

    private function loginVkUser(string $accessToken): RedirectResponse
    {
        $response = Http::asForm()->post('https://id.vk.com/oauth2/user_info', [
            'access_token' => $accessToken,
            'client_id' => config('services.vkontakte.client_id'),
        ]);

        if ($response->failed()) {
            return redirect()->route('login')->with('error', 'Не удалось получить данные пользователя VK ID.');
        }

        $vkUser = $response->json('user');
        $email = mb_strtolower((string) ($vkUser['email'] ?? ''));

        if ($email === '') {
            return redirect()->route('login')
                ->with('error', 'VK ID не передал email. Разрешите доступ к email в настройках VK.');
        }

        $name = trim(($vkUser['first_name'] ?? '').' '.($vkUser['last_name'] ?? '')) ?: 'VK пользователь';

        /** @var Authenticatable $user */
        $user = User::query()->firstOrCreate(
            ['email' => $email],
            [
                'fio' => $name,
                'nickname' => 'vk_'.Str::lower(Str::random(10)),
                'oauth_provider' => 'vk',
                'oauth_provider_id' => (string) ($vkUser['user_id'] ?? ''),
                'email_verified_at' => now(),
                'pd_consent_accepted_at' => now(),
            ],
        );

        Auth::login($user);

        return redirect()->route('phone.verify.notice');
    }

    private function resolveDriver(string $provider): string
    {
        abort_unless(isset(self::PROVIDERS[$provider]), 404);

        return self::PROVIDERS[$provider];
    }

    private function uniquePlaceholderPhone(): string
    {
        do {
            $phone = '+79'.str_pad((string) random_int(0, 999_999_999), 9, '0', STR_PAD_LEFT);
        } while (User::query()->where('phone', $phone)->exists());

        return $phone;
    }
}
