<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OAuth\OAuthPhoneResolver;
use App\Support\PostLoginRedirect;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Laravel\Fortify\Events\TwoFactorAuthenticationChallenged;
use Laravel\Fortify\Fortify;
use Laravel\Socialite\Contracts\User as SocialiteUserContract;
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

    public function __construct(private readonly OAuthPhoneResolver $oauthPhoneResolver) {}

    public function redirect(string $provider): RedirectResponse
    {
        $driver = Socialite::driver($this->resolveDriver($provider));

        if ($provider === 'yandex') {
            $driver->scopes(['login:default_phone']);
        }

        return $driver->redirect();
    }

    public function callback(string $provider, Request $request): RedirectResponse
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
        $providerId = (string) $oauthUser->getId();
        $rawPayload = $this->socialiteRawPayload($oauthUser);

        $linkCheck = $this->ensureOAuthLinkAllowed($email, $provider, $providerId);
        if ($linkCheck !== null) {
            return $linkCheck;
        }

        $user = $this->upsertOAuthUser(
            email: $email,
            name: $name,
            provider: $provider,
            providerId: $providerId,
            rawPayload: $rawPayload,
        );

        return $this->loginUserOrChallengeTwoFactor($user, $request);
    }

    public function yandexTokenPage(): View
    {
        return view('auth.yandex-token-page', [
            'oauthTokenNonce' => $this->ensureOAuthTokenNonceInSession(),
            'suggestOrigin' => rtrim((string) config('app.url'), '/'),
        ]);
    }

    public function yandexToken(Request $request): RedirectResponse
    {
        if (! $this->validateOAuthTokenNonce($request)) {
            return redirect()->route('login')->with('error', 'Сессия OAuth истекла. Повторите вход.');
        }

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
        $providerId = (string) ($yaUser['id'] ?? '');

        $linkCheck = $this->ensureOAuthLinkAllowed($email, 'yandex', $providerId);
        if ($linkCheck !== null) {
            return $linkCheck;
        }

        $user = $this->upsertOAuthUser(
            email: $email,
            name: $name,
            provider: 'yandex',
            providerId: $providerId,
            rawPayload: is_array($yaUser) ? $yaUser : [],
        );

        return $this->loginUserOrChallengeTwoFactor($user, $request);
    }

    public function vkRedirect(): RedirectResponse
    {
        $state = Str::random(40);
        $codeVerifier = $this->generatePkceCodeVerifier();

        session([
            'vk_oauth_state' => $state,
            'vk_oauth_code_verifier' => $codeVerifier,
        ]);

        $query = http_build_query([
            'response_type' => 'code',
            'client_id' => config('services.vkontakte.client_id'),
            'redirect_uri' => route('auth.vk.callback'),
            'scope' => 'email phone',
            'state' => $state,
            'code_challenge' => $this->generatePkceCodeChallenge($codeVerifier),
            'code_challenge_method' => 'S256',
        ]);

        return redirect('https://id.vk.ru/authorize?'.$query);
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

        $codeVerifier = (string) session()->pull('vk_oauth_code_verifier', '');

        if (blank($codeVerifier)) {
            return redirect()->route('login')->with('error', 'Не удалось выполнить вход через VK ID.');
        }

        $tokenResponse = Http::asForm()->post('https://id.vk.ru/oauth2/auth', [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'device_id' => $deviceId,
            'redirect_uri' => route('auth.vk.callback'),
            'client_id' => config('services.vkontakte.client_id'),
            'code_verifier' => $codeVerifier,
            'state' => (string) $request->query('state', ''),
        ]);

        if ($tokenResponse->failed() || blank($tokenResponse->json('access_token'))) {
            return redirect()->route('login')->with('error', 'Не удалось получить токен VK ID.');
        }

        return $this->loginVkUser($tokenResponse->json('access_token'), $request);
    }

    public function vkToken(Request $request): RedirectResponse
    {
        if (! $this->validateOAuthTokenNonce($request)) {
            return redirect()->route('login')->with('error', 'Сессия OAuth истекла. Повторите вход.');
        }

        $accessToken = $request->string('access_token')->toString();

        if (blank($accessToken)) {
            return redirect()->route('login')->with('error', 'Не удалось получить токен VK ID.');
        }

        return $this->loginVkUser($accessToken, $request);
    }

    private function loginVkUser(string $accessToken, Request $request): RedirectResponse
    {
        $response = Http::asForm()->post('https://id.vk.ru/oauth2/user_info', [
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
        $providerId = (string) ($vkUser['user_id'] ?? '');

        $linkCheck = $this->ensureOAuthLinkAllowed($email, 'vk', $providerId);
        if ($linkCheck !== null) {
            return $linkCheck;
        }

        $user = $this->upsertOAuthUser(
            email: $email,
            name: $name,
            provider: 'vk',
            providerId: $providerId,
            rawPayload: is_array($vkUser) ? $vkUser : [],
        );

        return $this->loginUserOrChallengeTwoFactor($user, $request);
    }

    /**
     * @param  array<string, mixed>  $rawPayload
     */
    private function upsertOAuthUser(
        string $email,
        string $name,
        string $provider,
        string $providerId,
        array $rawPayload,
    ): User {
        /** @var User $user */
        $user = $this->createOAuthUser($email, $name, $provider, $providerId);

        if (! $user->wasRecentlyCreated) {
            $updates = [];

            if ($user->oauth_provider === null || $user->oauth_provider === $provider) {
                $updates['oauth_provider'] = $provider;
                $updates['oauth_provider_id'] = $providerId;
            }

            if ($updates !== []) {
                $user->forceFill($updates)->save();
            }
        }

        $oauthPhone = $this->oauthPhoneResolver->extractPhone($provider, $rawPayload);
        $this->oauthPhoneResolver->applyToUser($user->fresh(), $oauthPhone);

        return $user->fresh();
    }

    private function createOAuthUser(string $email, string $name, string $provider, string $providerId): User
    {
        for ($attempt = 0; $attempt < 3; $attempt++) {
            try {
                /** @var User $user */
                $user = User::query()->firstOrCreate(
                    ['email' => $email],
                    [
                        'fio' => $name,
                        'nickname' => "{$provider}_".Str::lower(Str::random(10)),
                        'date_of_birth' => null,
                        'password' => Hash::make(Str::random(40)),
                        'pd_consent_accepted_at' => null,
                    ],
                );

                if ($user->wasRecentlyCreated) {
                    $user->forceFill([
                        'email_verified_at' => now(),
                        'oauth_provider' => $provider,
                        'oauth_provider_id' => $providerId,
                    ])->save();
                }

                return $user;
            } catch (\Illuminate\Database\UniqueConstraintViolationException $exception) {
                if ($attempt === 2) {
                    throw $exception;
                }
            }
        }

        throw new \RuntimeException('Unable to create OAuth user.');
    }

    private function generatePkceCodeVerifier(): string
    {
        return Str::random(64);
    }

    private function generatePkceCodeChallenge(string $codeVerifier): string
    {
        return rtrim(strtr(base64_encode(hash('sha256', $codeVerifier, true)), '+/', '-_'), '=');
    }

    private function ensureOAuthTokenNonceInSession(): string
    {
        $nonce = (string) session('oauth_token_nonce', '');
        $issuedAt = (int) session('oauth_token_nonce_at', 0);
        $expired = $issuedAt <= 0 || (now()->timestamp - $issuedAt) > 300;

        if ($nonce === '' || $expired) {
            $nonce = Str::random(40);
            session([
                'oauth_token_nonce' => $nonce,
                'oauth_token_nonce_at' => now()->timestamp,
            ]);
        }

        return $nonce;
    }

    private function validateOAuthTokenNonce(Request $request): bool
    {
        $nonce = $request->string('oauth_token_nonce')->toString();
        $sessionNonce = (string) $request->session()->pull('oauth_token_nonce', '');
        $issuedAt = (int) $request->session()->pull('oauth_token_nonce_at', 0);

        if ($nonce === '' || $sessionNonce === '' || ! hash_equals($sessionNonce, $nonce)) {
            return false;
        }

        return $issuedAt > 0 && (now()->timestamp - $issuedAt) <= 300;
    }

    private function ensureOAuthLinkAllowed(string $email, string $provider, string $providerId): ?RedirectResponse
    {
        $existingUser = User::query()->where('email', $email)->first();

        if ($existingUser !== null && ! $this->isOAuthLinked($existingUser, $provider, $providerId)) {
            return redirect()->route('login')
                ->with('error', 'Аккаунт с этим email уже зарегистрирован другим способом. Войдите через email и пароль.');
        }

        return null;
    }

    private function resolveDriver(string $provider): string
    {
        abort_unless(isset(self::PROVIDERS[$provider]), 404);

        return self::PROVIDERS[$provider];
    }

    private function isOAuthLinked(User $user, string $provider, string $providerId): bool
    {
        return $user->oauth_provider === $provider && $user->oauth_provider_id === $providerId;
    }

    private function loginUserOrChallengeTwoFactor(User $user, Request $request): RedirectResponse
    {
        if ($user->two_factor_secret &&
            (! Fortify::confirmsTwoFactorAuthentication() || ! is_null($user->two_factor_confirmed_at))) {
            $request->session()->put([
                'login.id' => $user->getKey(),
                'login.remember' => false,
            ]);

            TwoFactorAuthenticationChallenged::dispatch($user);

            return redirect()->route('two-factor.login');
        }

        Auth::login($user);

        return $this->finalizeOAuthLogin($user);
    }

    private function finalizeOAuthLogin(User $user): RedirectResponse
    {
        $user->refresh();

        if ($user->pd_consent_accepted_at === null) {
            return redirect()->route('auth.oauth.consent');
        }

        if ($user->hasVerifiedContactChannels()) {
            return redirect()->to(PostLoginRedirect::intendedUrl($user));
        }

        return redirect()->route('phone.verify.notice');
    }

    /**
     * @return array<string, mixed>
     */
    private function socialiteRawPayload(SocialiteUserContract $oauthUser): array
    {
        if (is_callable([$oauthUser, 'getRaw'])) {
            $raw = $oauthUser->getRaw();

            if (is_array($raw)) {
                return $raw;
            }
        }

        if (property_exists($oauthUser, 'user') && is_array($oauthUser->user)) {
            return $oauthUser->user;
        }

        return [];
    }
}
