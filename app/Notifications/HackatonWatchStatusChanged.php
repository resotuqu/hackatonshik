<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Enums\HackatonStatus;
use App\Models\Hackaton;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class HackatonWatchStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function backoff(): array
    {
        return [5, 30, 120];
    }

    public function __construct(
        private readonly Hackaton $hackaton,
        private readonly HackatonStatus $previousStatus,
        private readonly HackatonStatus $newStatus,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Статус хакатона изменился: {$this->hackaton->title}")
            ->line("Хакатон «{$this->hackaton->title}» перешёл в статус «{$this->newStatus->label()}».")
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
            'previous_status' => $this->previousStatus->value,
            'new_status' => $this->newStatus->value,
            'status_label' => $this->newStatus->label(),
            'message' => "Статус хакатона «{$this->hackaton->title}» изменился: {$this->newStatus->label()}.",
            'url' => route('hackatons.show', $this->hackaton),
        ];
    }
}
