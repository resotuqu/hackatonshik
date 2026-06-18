<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\NewNotification;
use Illuminate\Notifications\Events\NotificationSent;

final class BroadcastNewNotification
{
    public function handle(NotificationSent $event): void
    {
        if ($event->channel !== 'database') {
            return;
        }

        $notifiable = $event->notifiable;

        if (! method_exists($notifiable, 'getKey')) {
            return;
        }

        broadcast(new NewNotification((int) $notifiable->getKey()));
    }
}
