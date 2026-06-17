<?php

declare(strict_types=1);

namespace App\Actions\Hackaton;

use App\Models\Hackaton;
use App\Models\HackatonAnalyticsEvent;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

final class RecordHackatonAnalyticsEvent
{
    public function handle(Hackaton $hackaton, string $eventName, ?User $user = null, array $payload = []): void
    {
        if ($eventName === 'page_view') {
            $sessionKey = 'hackaton_analytics_viewed_'.$hackaton->id;

            if (Session::has($sessionKey)) {
                return;
            }

            Session::put($sessionKey, true);
        }

        HackatonAnalyticsEvent::query()->create([
            'hackaton_id' => $hackaton->id,
            'user_id' => $user?->id,
            'event_name' => $eventName,
            'payload' => $payload,
        ]);

        Cache::forget('organizer:'.(int) $hackaton->user_id.':funnel');
    }
}
