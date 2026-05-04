<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Notifications\EmailChangeNewAddressCodeNotification;
use App\Notifications\EmailChangeOldAddressCodeNotification;
use App\Notifications\PhoneChangeEmailCodeNotification;
use App\Services\Sms\PlusofonFlashCallSender;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ContactChangeService
{
    private const CODE_TTL_MINUTES = 10;

    private const RATE_LIMIT_MAX = 3;

    private const RATE_LIMIT_DECAY_SECONDS = 60;

    public function __construct(
        private readonly PlusofonFlashCallSender $flashCallSender,
    ) {}

    /**
     * @return array{step: int, new_phone: string}|null
     */
    public function phoneChangeState(User $user): ?array
    {
        $data = Cache::get($this->phoneChangeCacheKey($user->id));
        if (! is_array($data) || ! isset($data['step'], $data['new_phone'])) {
            return null;
        }

        return [
            'step' => (int) $data['step'],
            'new_phone' => (string) $data['new_phone'],
        ];
    }

    /**
     * @return array{step: int, new_email: string}|null
     */
    public function emailChangeState(User $user): ?array
    {
        $data = Cache::get($this->emailChangeCacheKey($user->id));
        if (! is_array($data) || ! isset($data['step'], $data['new_email'])) {
            return null;
        }

        return [
            'step' => (int) $data['step'],
            'new_email' => (string) $data['new_email'],
        ];
    }

    public function startPhoneChange(User $user, string $newPhone): void
    {
        $newPhone = $this->validatedPhone($newPhone, $user->id);

        if ($newPhone === $user->phone) {
            throw ValidationException::withMessages([
                'new_phone' => 'Укажите номер, отличный от текущего.',
            ]);
        }

        $this->hitRateLimit(
            "phone-change-send-email:{$user->id}",
            'Слишком много запросов кода на почту. Попробуйте позже.',
            'new_phone'
        );

        $code = $this->generateEmailCode();

        Cache::put($this->phoneChangeCacheKey($user->id), [
            'new_phone' => $newPhone,
            'step' => 1,
            'code' => $code,
        ], now()->addMinutes(self::CODE_TTL_MINUTES));

        $user->notify(new PhoneChangeEmailCodeNotification($code));
    }

    public function resendPhoneChangeEmailCode(User $user): void
    {
        $state = Cache::get($this->phoneChangeCacheKey($user->id));
        if (! is_array($state) || (int) ($state['step'] ?? 0) !== 1) {
            throw ValidationException::withMessages([
                'code' => 'Сначала начните смену номера и укажите новый телефон.',
            ]);
        }

        $this->hitRateLimit(
            "phone-change-send-email:{$user->id}",
            'Слишком много запросов кода на почту. Попробуйте позже.',
            'phone_email_code'
        );

        $code = $this->generateEmailCode();
        $state['code'] = $code;
        Cache::put($this->phoneChangeCacheKey($user->id), $state, now()->addMinutes(self::CODE_TTL_MINUTES));

        $user->notify(new PhoneChangeEmailCodeNotification($code));
    }

    public function verifyPhoneChangeEmailAndSendCall(User $user, string $code): void
    {
        $state = Cache::get($this->phoneChangeCacheKey($user->id));
        if (! is_array($state) || (int) ($state['step'] ?? 0) !== 1) {
            throw ValidationException::withMessages([
                'code' => 'Сессия смены номера истекла или не найдена. Начните заново.',
            ]);
        }

        if (! $this->codesMatch((string) ($state['code'] ?? ''), $code)) {
            throw ValidationException::withMessages([
                'code' => 'Неверный код из письма.',
            ]);
        }

        $newPhone = (string) $state['new_phone'];
        $callPin = $this->generateCallPin();

        Cache::put($this->phoneChangeCacheKey($user->id), [
            'new_phone' => $newPhone,
            'step' => 2,
            'code' => $callPin,
        ], now()->addMinutes(self::CODE_TTL_MINUTES));

        if (! $this->flashCallSender->sendVerificationCode($newPhone, $callPin)) {
            Cache::forget($this->phoneChangeCacheKey($user->id));
            throw ValidationException::withMessages([
                'code' => 'Не удалось инициировать звонок. Попробуйте позже.',
            ]);
        }
    }

    public function resendPhoneChangeCall(User $user): void
    {
        $state = Cache::get($this->phoneChangeCacheKey($user->id));
        if (! is_array($state) || (int) ($state['step'] ?? 0) !== 2) {
            throw ValidationException::withMessages([
                'code' => 'Сначала подтвердите код из письма.',
            ]);
        }

        $this->hitRateLimit(
            "phone-change-send-call:{$user->id}",
            'Слишком много запросов звонков. Попробуйте позже.',
            'phone_call_code'
        );

        $newPhone = (string) $state['new_phone'];
        $callPin = $this->generateCallPin();

        $state['code'] = $callPin;
        Cache::put($this->phoneChangeCacheKey($user->id), $state, now()->addMinutes(self::CODE_TTL_MINUTES));

        if (! $this->flashCallSender->sendVerificationCode($newPhone, $callPin)) {
            throw ValidationException::withMessages([
                'code' => 'Не удалось инициировать звонок. Попробуйте позже.',
            ]);
        }
    }

    public function completePhoneChange(User $user, string $code): void
    {
        $state = Cache::get($this->phoneChangeCacheKey($user->id));
        if (! is_array($state) || (int) ($state['step'] ?? 0) !== 2) {
            throw ValidationException::withMessages([
                'code' => 'Сессия смены номера истекла или звонок ещё не инициирован.',
            ]);
        }

        if (! $this->codesMatch((string) ($state['code'] ?? ''), $code)) {
            throw ValidationException::withMessages([
                'code' => 'Неверный код из звонка.',
            ]);
        }

        $newPhone = (string) $state['new_phone'];

        $user->forceFill([
            'phone' => $newPhone,
            'phone_verified_at' => now(),
        ])->save();

        Cache::forget($this->phoneChangeCacheKey($user->id));
    }

    public function cancelPhoneChange(User $user): void
    {
        Cache::forget($this->phoneChangeCacheKey($user->id));
    }

    public function startEmailChange(User $user, string $newEmail): void
    {
        $newEmail = $this->validatedNewEmail($newEmail, $user->id);

        if ($newEmail === strtolower($user->email)) {
            throw ValidationException::withMessages([
                'new_email' => 'Укажите адрес, отличный от текущего.',
            ]);
        }

        $this->hitRateLimit(
            "email-change-send-old:{$user->id}",
            'Слишком много запросов кода. Попробуйте позже.',
            'new_email'
        );

        $code = $this->generateEmailCode();

        Cache::put($this->emailChangeCacheKey($user->id), [
            'new_email' => $newEmail,
            'step' => 1,
            'code' => $code,
        ], now()->addMinutes(self::CODE_TTL_MINUTES));

        $user->notify(new EmailChangeOldAddressCodeNotification($code));
    }

    public function resendEmailChangeOldCode(User $user): void
    {
        $state = Cache::get($this->emailChangeCacheKey($user->id));
        if (! is_array($state) || (int) ($state['step'] ?? 0) !== 1) {
            throw ValidationException::withMessages([
                'code' => 'Сначала укажите новый адрес почты.',
            ]);
        }

        $this->hitRateLimit(
            "email-change-send-old:{$user->id}",
            'Слишком много запросов кода. Попробуйте позже.',
            'email_old_code'
        );

        $code = $this->generateEmailCode();
        $state['code'] = $code;
        Cache::put($this->emailChangeCacheKey($user->id), $state, now()->addMinutes(self::CODE_TTL_MINUTES));

        $user->notify(new EmailChangeOldAddressCodeNotification($code));
    }

    public function verifyEmailChangeOldAndSendToNew(User $user, string $code): void
    {
        $state = Cache::get($this->emailChangeCacheKey($user->id));
        if (! is_array($state) || (int) ($state['step'] ?? 0) !== 1) {
            throw ValidationException::withMessages([
                'code' => 'Сессия смены почты истекла. Начните заново.',
            ]);
        }

        if (! $this->codesMatch((string) ($state['code'] ?? ''), $code)) {
            throw ValidationException::withMessages([
                'code' => 'Неверный код на старую почту.',
            ]);
        }

        $newEmail = (string) $state['new_email'];
        $newCode = $this->generateEmailCode();

        Cache::put($this->emailChangeCacheKey($user->id), [
            'new_email' => $newEmail,
            'step' => 2,
            'code' => $newCode,
        ], now()->addMinutes(self::CODE_TTL_MINUTES));

        Notification::route('mail', $newEmail)
            ->notify(new EmailChangeNewAddressCodeNotification($newCode));
    }

    public function resendEmailChangeNewCode(User $user): void
    {
        $state = Cache::get($this->emailChangeCacheKey($user->id));
        if (! is_array($state) || (int) ($state['step'] ?? 0) !== 2) {
            throw ValidationException::withMessages([
                'code' => 'Сначала подтвердите код на старую почту.',
            ]);
        }

        $this->hitRateLimit(
            "email-change-send-new:{$user->id}",
            'Слишком много запросов кода. Попробуйте позже.',
            'email_new_code'
        );

        $newEmail = (string) $state['new_email'];
        $code = $this->generateEmailCode();
        $state['code'] = $code;
        Cache::put($this->emailChangeCacheKey($user->id), $state, now()->addMinutes(self::CODE_TTL_MINUTES));

        Notification::route('mail', $newEmail)
            ->notify(new EmailChangeNewAddressCodeNotification($code));
    }

    public function completeEmailChange(User $user, string $code): void
    {
        $state = Cache::get($this->emailChangeCacheKey($user->id));
        if (! is_array($state) || (int) ($state['step'] ?? 0) !== 2) {
            throw ValidationException::withMessages([
                'code' => 'Сессия смены почты истекла. Начните заново.',
            ]);
        }

        if (! $this->codesMatch((string) ($state['code'] ?? ''), $code)) {
            throw ValidationException::withMessages([
                'code' => 'Неверный код на новую почту.',
            ]);
        }

        $newEmail = (string) $state['new_email'];

        $user->forceFill([
            'email' => $newEmail,
            'email_verified_at' => now(),
        ])->save();

        Cache::forget($this->emailChangeCacheKey($user->id));
    }

    public function cancelEmailChange(User $user): void
    {
        Cache::forget($this->emailChangeCacheKey($user->id));
    }

    private function phoneChangeCacheKey(int $userId): string
    {
        return "contact-phone-change:{$userId}";
    }

    private function emailChangeCacheKey(int $userId): string
    {
        return "contact-email-change:{$userId}";
    }

    private function generateEmailCode(): string
    {
        return (string) random_int(100000, 999999);
    }

    private function generateCallPin(): string
    {
        return (string) random_int(1000, 9999);
    }

    private function codesMatch(string $expected, string $given): bool
    {
        return $expected !== '' && hash_equals($expected, $given);
    }

    private function hitRateLimit(string $key, string $message, string $errorBagField = 'code'): void
    {
        if (RateLimiter::tooManyAttempts($key, self::RATE_LIMIT_MAX)) {
            throw ValidationException::withMessages([
                $errorBagField => $message,
            ]);
        }

        RateLimiter::hit($key, self::RATE_LIMIT_DECAY_SECONDS);
    }

    private function validatedPhone(string $newPhone, int $userId): string
    {
        $validator = Validator::make(
            ['phone' => $newPhone],
            [
                'phone' => [
                    'required',
                    'string',
                    'min:11',
                    'max:12',
                    Rule::unique(User::class, 'phone')->ignore($userId),
                ],
            ],
            [
                'phone.required' => 'Укажите новый номер телефона.',
                'phone.min' => 'Номер телефона указан неверно.',
                'phone.max' => 'Номер телефона указан неверно.',
                'phone.unique' => 'Этот номер уже используется.',
            ]
        );

        $validator->validate();

        /** @var string */
        return $validator->validated()['phone'];
    }

    private function validatedNewEmail(string $newEmail, int $userId): string
    {
        $validator = Validator::make(
            ['email' => $newEmail],
            [
                'email' => [
                    'required',
                    'string',
                    'email',
                    'max:255',
                    Rule::unique(User::class, 'email')->ignore($userId),
                ],
            ],
            [
                'email.required' => 'Укажите новый адрес почты.',
                'email.email' => 'Введите корректный email.',
                'email.unique' => 'Этот email уже используется.',
            ]
        );

        $validator->validate();

        /** @var string */
        return strtolower($validator->validated()['email']);
    }
}
