<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\TeamMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class TeamChatMention extends Notification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function __construct(
        private readonly TeamMessage $message,
    ) {}

    /** @return list<string> */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /** @return array<string, mixed> */
    public function toArray(object $notifiable): array
    {
        $author = $this->message->user?->fio ?? $this->message->user?->nickname ?? 'Участник';
        $preview = mb_strimwidth((string) $this->message->content, 0, 80, '…');
        $teamTitle = $this->message->team?->title ?? 'команды';

        return [
            'type' => 'team_chat_mention',
            'team_id' => $this->message->team_id,
            'team_title' => $teamTitle,
            'message_id' => $this->message->id,
            'author' => $author,
            'preview' => $preview,
            'message' => "{$author} упомянул вас в чате «{$teamTitle}»: {$preview}",
            'url' => route('teams.show', $this->message->team_id),
        ];
    }
}
