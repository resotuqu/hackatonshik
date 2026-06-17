<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Hackaton;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class DocumentUploadReminder extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Hackaton $hackaton,
    ) {}

    /**
     * @return list<string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'document_upload_reminder',
            'hackaton_id' => $this->hackaton->id,
            'hackaton_title' => $this->hackaton->title,
            'message' => "Организатор напоминает: загрузите обязательные документы для хакатона «{$this->hackaton->title}».",
            'url' => route('participant.hackatons.hub', $this->hackaton),
        ];
    }
}
