<?php

namespace App\Notifications;

use App\Models\HackatonAnnouncement;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class HackatonAnnouncementPublished extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly HackatonAnnouncement $announcement) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $hackaton = $this->announcement->hackaton;
        $url = route('hackatons.show', $hackaton);

        return (new MailMessage)
            ->subject("Новый анонс: {$hackaton->title}")
            ->line("В хакатоне «{$hackaton->title}» опубликован новый анонс.")
            ->line($this->announcement->title)
            ->action('Перейти к хакатону', $url);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'announcement_id' => $this->announcement->id,
            'hackaton_id' => $this->announcement->hackaton_id,
            'hackaton_title' => $this->announcement->hackaton->title,
            'title' => $this->announcement->title,
            'body' => $this->announcement->body,
            'published_at' => $this->announcement->published_at?->toIso8601String(),
            'url' => route('hackatons.show', $this->announcement->hackaton_id),
        ];
    }
}
