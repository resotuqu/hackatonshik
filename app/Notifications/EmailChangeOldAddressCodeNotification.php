<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Mail\EmailChangeCodeMail;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Mail\Mailable;
use Illuminate\Notifications\Notification;

class EmailChangeOldAddressCodeNotification extends Notification
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
            mailSubject: 'Код для смены электронной почты — Хакатонщик',
            intro: 'Вы запросили смену адреса электронной почты. Введите код в профиле на платформе Хакатонщик, чтобы продолжить.',
            disclaimer: 'Если это были не вы, проигнорируйте это письмо.',
            recipientEmail: null,
        ))->locale('ru');
    }
}
