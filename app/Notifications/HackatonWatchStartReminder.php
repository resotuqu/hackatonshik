<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Hackaton;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;

class HackatonWatchStartReminder extends Notification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function backoff(): array
    {
        return [5, 30, 120];
    }

    public function __construct(
        private readonly Hackaton $hackaton,
        private readonly int $daysUntilStart,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $when = Carbon::parse((string) $this->hackaton->start_at)->format('d.m.Y H:i');

        return (new MailMessage)
            ->subject("Скоро старт: {$this->hackaton->title}")
            ->line("Хакатон «{$this->hackaton->title}» начнётся через {$this->daysUntilStart} дн. ({$when}).")
            ->action('Открыть хакатон', route('hackatons.show', $this->hackaton));
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'hackaton_id' => $this->hackaton->id,
            'hackaton_title' => $this->hackaton->title,
            'days_until_start' => $this->daysUntilStart,
            'start_at' => Carbon::parse((string) $this->hackaton->start_at)->toIso8601String(),
            'message' => "До старта хакатона «{$this->hackaton->title}» осталось {$this->daysUntilStart} дн.",
            'url' => route('hackatons.show', $this->hackaton),
        ];
    }
}
