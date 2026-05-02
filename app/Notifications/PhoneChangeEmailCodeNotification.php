<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PhoneChangeEmailCodeNotification extends Notification
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

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Код для смены номера телефона')
            ->line('Вы начали смену номера телефона в профиле.')
            ->line("Код подтверждения: **{$this->code}**")
            ->line('Если это были не вы, смените пароль и обратитесь в поддержку.');
    }
}
