<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Mail\EmailChangeCodeMail;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Mail\Mailable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class PhoneChangeEmailCodeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly string $code,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): Mailable
    {
        /** @var User $notifiable */
        return (new EmailChangeCodeMail(
            user: $notifiable,
            code: $this->code,
            mailSubject: 'Код для смены номера телефона — Хакатонщик',
            intro: 'Вы начали смену номера телефона в профиле на платформе Хакатонщик. Введите код ниже, чтобы продолжить.',
            disclaimer: 'Если это были не вы, смените пароль и обратитесь в поддержку.',
            recipientEmail: null,
        ))->locale('ru');
    }
}
