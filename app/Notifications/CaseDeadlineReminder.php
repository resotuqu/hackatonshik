<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Hackaton;
use App\Models\HackatonCase;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;

class CaseDeadlineReminder extends Notification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function backoff(): array
    {
        return [5, 30, 120];
    }

    public function __construct(private readonly HackatonCase $case) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $hackatonTitle = $this->resolveHackatonTitle();
        $deadline = Carbon::parse((string) $this->case->deadline_at)->format('d.m.Y H:i');

        return (new MailMessage)
            ->subject("Дедлайн кейса: {$this->case->title}")
            ->line("Напоминание: по кейсу «{$this->case->title}» скоро дедлайн.")
            ->line("Хакатон: {$hackatonTitle}")
            ->line('Дедлайн: '.$deadline)
            ->action('Открыть хакатон', route('hackatons.show', $this->case->hackaton_id));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'case_deadline_reminder',
            'title' => 'Напоминание о дедлайне кейса',
            'message' => "Кейс «{$this->case->title}»: дедлайн ".Carbon::parse((string) $this->case->deadline_at)->format('d.m.Y H:i').'.',
            'hackaton_id' => $this->case->hackaton_id,
            'case_id' => $this->case->id,
            'url' => route('hackatons.show', $this->case->hackaton_id),
        ];
    }

    private function resolveHackatonTitle(): string
    {
        $hackaton = Hackaton::query()->find($this->case->hackaton_id);

        return $hackaton instanceof Hackaton ? $hackaton->title : 'Хакатон';
    }
}
