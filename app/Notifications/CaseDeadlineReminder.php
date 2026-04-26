<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\HackatonCase;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CaseDeadlineReminder extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly HackatonCase $case) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $hackaton = $this->case->hackaton;

        return (new MailMessage)
            ->subject("Дедлайн кейса: {$this->case->title}")
            ->line("Напоминание: по кейсу «{$this->case->title}» скоро дедлайн.")
            ->line("Хакатон: {$hackaton->title}")
            ->line('Дедлайн: '.$this->case->deadline_at?->format('d.m.Y H:i'))
            ->action('Открыть хакатон', route('hackatons.show', $hackaton));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'case_deadline_reminder',
            'title' => 'Напоминание о дедлайне кейса',
            'message' => "Кейс «{$this->case->title}»: дедлайн {$this->case->deadline_at?->format('d.m.Y H:i')}.",
            'hackaton_id' => $this->case->hackaton_id,
            'case_id' => $this->case->id,
            'url' => route('hackatons.show', $this->case->hackaton_id),
        ];
    }
}
