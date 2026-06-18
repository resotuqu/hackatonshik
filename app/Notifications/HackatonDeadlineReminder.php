<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Hackaton;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;

class HackatonDeadlineReminder extends Notification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function backoff(): array
    {
        return [5, 30, 120];
    }

    public function __construct(
        private readonly Hackaton $hackaton,
        private readonly int $daysUntilDeadline,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $deadline = Carbon::parse((string) $this->hackaton->registration_deadline_at)->format('d.m.Y H:i');

        $daysLabel = match ($this->daysUntilDeadline) {
            1 => 'завтра',
            default => "через {$this->daysUntilDeadline} дн.",
        };

        return (new MailMessage)
            ->subject("Дедлайн заявок {$daysLabel}: {$this->hackaton->title}")
            ->greeting('Не пропустите дедлайн!')
            ->line("Регистрация на хакатон «{$this->hackaton->title}» закрывается {$daysLabel} ({$deadline}).")
            ->line('Вы следите за этим хакатоном, но ещё не подали заявку. Успейте собрать команду и зарегистрироваться.')
            ->action('Подать заявку', route('hackatons.show', $this->hackaton))
            ->line('Если вы уже подали заявку через другую команду — просто проигнорируйте это письмо.');
    }

    /** @return array<string, mixed> */
    public function toArray(object $notifiable): array
    {
        return [
            'hackaton_id' => $this->hackaton->id,
            'hackaton_title' => $this->hackaton->title,
            'days_until_deadline' => $this->daysUntilDeadline,
            'registration_deadline_at' => Carbon::parse((string) $this->hackaton->registration_deadline_at)->toIso8601String(),
            'message' => "Дедлайн заявок на «{$this->hackaton->title}» — через {$this->daysUntilDeadline} дн. Вы ещё не подали заявку.",
            'url' => route('hackatons.show', $this->hackaton),
        ];
    }
}
