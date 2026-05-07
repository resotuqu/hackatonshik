<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\Sms\PlusofonFlashCallSender;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class PhoneVerificationController extends Controller
{
    public function __construct(private readonly PlusofonFlashCallSender $flashCallSender) {}

    public function notice(): View
    {
        return view('pages.auth.phone-verify');
    }

    public function sendCode(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_if(! $user, 401);

        if ($user->phone_verified_at !== null) {
            return redirect()->route('home')->with('success', 'Телефон уже подтвержден.');
        }

        $key = "phone-verification-send:{$user->id}";
        if (RateLimiter::tooManyAttempts($key, 3)) {
            return back()->with('error', 'Слишком много запросов. Попробуйте позже.');
        }

        RateLimiter::hit($key, 60);

        $code = (string) random_int(1000, 9999);
        $cacheKey = $this->codeCacheKey($user->id);
        Cache::put($cacheKey, $code, now()->addMinutes(10));

        if (! $this->flashCallSender->sendVerificationCode($user->phone, $code)) {
            throw ValidationException::withMessages([
                'code' => 'Не удалось инициировать звонок. Повторите попытку.',
            ]);
        }

        return back()->with('success', 'Сейчас поступит звонок. Голосовой ассистент проговорит код.');
    }

    public function verify(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'digits:4'],
        ], [
            'code.required' => 'Введите код из звонка.',
            'code.digits' => 'Код должен содержать 4 цифры.',
        ]);

        $user = $request->user();
        abort_if(! $user, 401);

        $attemptsKey = "phone-verification-attempts:{$user->id}";
        if (RateLimiter::tooManyAttempts($attemptsKey, 5)) {
            Cache::forget($this->codeCacheKey($user->id));

            return back()->with('error', 'Слишком много неверных попыток. Запросите код заново.');
        }

        $cacheKey = $this->codeCacheKey($user->id);
        $expectedCode = Cache::get($cacheKey);

        if (! $expectedCode || ! hash_equals((string) $expectedCode, (string) $validated['code'])) {
            RateLimiter::hit($attemptsKey, 600);
            throw ValidationException::withMessages([
                'code' => 'Неверный или просроченный код подтверждения.',
            ]);
        }

        $user->update(['phone_verified_at' => now()]);
        Cache::forget($cacheKey);
        RateLimiter::clear($attemptsKey);

        return redirect()->route('home')->with('success', 'Номер телефона подтвержден.');
    }

    private function codeCacheKey(int $userId): string
    {
        return "phone-verification-code:{$userId}";
    }
}
