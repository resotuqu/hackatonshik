<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
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
        $providerId = (string) $oauthUser->getId();
        $email = $oauthUser->getEmail() ?: "{$driver}_{$providerId}@oauth.local";
        $name = $oauthUser->getName() ?: $oauthUser->getNickname() ?: 'OAuth пользователь';

        /** @var Authenticatable $user */
        $user = User::query()->firstOrCreate(
            ['email' => $email],
            [
                'fio' => $name,
                'nickname' => "{$driver}_".Str::lower(Str::random(10)),
                'date_of_birth' => now()->subYears(18)->toDateString(),
                'phone' => '+70000000000',
                'password' => Hash::make(Str::random(40)),
                'phone_verified_at' => null,
                'email_verified_at' => now(),
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
}
