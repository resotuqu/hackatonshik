<?php

namespace App\Notifications;

use App\Models\Hackaton;
use App\Models\HackatonAnnouncement;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;

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
        $hackaton = Hackaton::query()->find($this->announcement->hackaton_id);
        $hackatonTitle = $hackaton instanceof Hackaton ? $hackaton->title : 'Хакатон';
        $url = route('hackatons.show', $this->announcement->hackaton_id);

        return (new MailMessage)
            ->subject("Новый анонс: {$hackatonTitle}")
            ->line("В хакатоне «{$hackatonTitle}» опубликован новый анонс.")
            ->line($this->announcement->title)
            ->action('Перейти к хакатону', $url);
    }

    public function toArray(object $notifiable): array
    {
        $hackaton = Hackaton::query()->find($this->announcement->hackaton_id);

        return [
            'announcement_id' => $this->announcement->id,
            'hackaton_id' => $this->announcement->hackaton_id,
            'hackaton_title' => $hackaton instanceof Hackaton ? $hackaton->title : null,
            'title' => $this->announcement->title,
            'message' => $this->announcement->body,
            'body' => $this->announcement->body,
            'published_at' => Carbon::parse((string) $this->announcement->published_at)->toIso8601String(),
            'url' => route('hackatons.show', $this->announcement->hackaton_id),
        ];
    }
}
