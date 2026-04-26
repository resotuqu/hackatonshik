<?php

namespace App\Http\Controllers;

use App\Services\Sms\PlusofonSmsSender;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class PhoneVerificationController extends Controller
{
    public function __construct(private readonly PlusofonSmsSender $smsSender) {}

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

        $code = (string) random_int(100000, 999999);
        $cacheKey = $this->codeCacheKey($user->id);
        Cache::put($cacheKey, $code, now()->addMinutes(10));

        if (! $this->smsSender->sendVerificationCode($user->phone, $code)) {
            throw ValidationException::withMessages([
                'code' => 'Не удалось отправить SMS. Повторите попытку.',
            ]);
        }

        return back()->with('success', 'Код отправлен на ваш номер телефона.');
    }

    public function verify(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'digits:6'],
        ], [
            'code.required' => 'Введите код из SMS.',
            'code.digits' => 'Код должен содержать 6 цифр.',
        ]);

        $user = $request->user();
        abort_if(! $user, 401);

        $cacheKey = $this->codeCacheKey($user->id);
        $expectedCode = Cache::get($cacheKey);

        if (! $expectedCode || ! hash_equals((string) $expectedCode, (string) $validated['code'])) {
            throw ValidationException::withMessages([
                'code' => 'Неверный или просроченный код подтверждения.',
            ]);
        }

        $user->update(['phone_verified_at' => now()]);
        Cache::forget($cacheKey);

        return redirect()->route('home')->with('success', 'Номер телефона подтвержден.');
    }

    private function codeCacheKey(int $userId): string
    {
        return "phone-verification-code:{$userId}";
    }
}
