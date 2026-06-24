<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Mail\EmailChangeCodeMail;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Mail\Mailable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class EmailChangeNewAddressCodeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly User $user,
        private readonly string $newEmail,
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
        return (new EmailChangeCodeMail(
            user: $this->user,
            code: $this->code,
            mailSubject: 'Подтвердите новый адрес — Хакатонщик',
            intro: 'Введите этот код в профиле на платформе Хакатонщик, чтобы подтвердить новый адрес электронной почты.',
            disclaimer: 'Если вы не запрашивали смену почты, проигнорируйте это письмо.',
            recipientEmail: $this->newEmail,
        ))->locale('ru');
    }
}
