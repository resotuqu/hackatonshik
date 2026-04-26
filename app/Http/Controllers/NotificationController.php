<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    public function markAsRead(DatabaseNotification $notification): RedirectResponse
    {
        if ((int) $notification->notifiable_id !== (int) auth()->id()) {
            abort(403);
        }

        if ($notification->read_at === null) {
            $notification->markAsRead();
        }

        return back();
    }

    public function markAllAsRead(): RedirectResponse
    {
        auth()->user()?->unreadNotifications->markAsRead();

        return back();
    }
}
