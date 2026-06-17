<?php

declare(strict_types=1);

namespace App\Actions\Hackaton;

use App\Enums\HackatonStatus;
use App\Models\Hackaton;
use App\Models\User;
use App\Notifications\HackatonWatchStatusChanged;
use Illuminate\Support\Facades\Notification;

final class NotifyHackatonWatchersOfStatusChange
{
    public function handle(Hackaton $hackaton, HackatonStatus|string|null $previousStatus, HackatonStatus $newStatus): void
    {
        if ($previousStatus === null) {
            return;
        }

        $previous = $previousStatus instanceof HackatonStatus
            ? $previousStatus
            : HackatonStatus::tryFrom((string) $previousStatus);

        if ($previous === null || $previous === $newStatus) {
            return;
        }

        $watchers = User::query()
            ->whereHas('watchedHackatons', fn ($query) => $query->where('hackatons.id', $hackaton->id))
            ->get();

        if ($watchers->isEmpty()) {
            return;
        }

        Notification::send($watchers, new HackatonWatchStatusChanged($hackaton, $previous, $newStatus));
    }
}
