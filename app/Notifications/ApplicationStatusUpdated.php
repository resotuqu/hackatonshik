<?php

namespace App\Notifications;

use App\Enums\ApplicationStatus;
use App\Models\Hackaton;
use App\Models\Team;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApplicationStatusUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Hackaton $hackaton,
        private readonly Team $team,
        private readonly ApplicationStatus $status,
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $statusLabel = $this->status->label();
        $message = "Статус заявки команды «{$this->team->title}» на хакатон «{$this->hackaton->title}» изменен: {$statusLabel}.";

        return (new MailMessage)
            ->subject("Статус заявки: {$this->team->title}")
            ->line($message)
            ->action('Открыть хакатон', route('hackatons.show', $this->hackaton))
            ->line('Вы получили это уведомление, потому что участвуете в команде.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'hackaton_application_status',
            'title' => "Заявка команды: {$this->status->label()}",
            'message' => "Команда «{$this->team->title}» — хакатон «{$this->hackaton->title}».",
            'status' => $this->status->value,
            'team_id' => $this->team->id,
            'hackaton_id' => $this->hackaton->id,
            'url' => route('hackatons.show', $this->hackaton),
        ];
    }
}
