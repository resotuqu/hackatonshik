<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmailChangeNewAddressCodeNotification extends Notification
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
            ->subject('Подтвердите новый адрес электронной почты')
            ->line('Введите этот код, чтобы подтвердить новый адрес почты в вашем аккаунте.')
            ->line("Код подтверждения: **{$this->code}**")
            ->line('Если вы не запрашивали смену почты, проигнорируйте это письмо.');
    }
}
