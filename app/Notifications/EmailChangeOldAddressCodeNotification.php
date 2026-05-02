<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
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

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Код для смены электронной почты')
            ->line('Вы запросили смену адреса электронной почты.')
            ->line("Код подтверждения: **{$this->code}**")
            ->line('Если это были не вы, проигнорируйте это письмо.');
    }
}
