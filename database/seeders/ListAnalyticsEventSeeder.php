<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\ListAnalyticsEvent;
use App\Models\User;
use Illuminate\Database\Seeder;

class ListAnalyticsEventSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::query()->orderBy('id')->take(5)->pluck('id');

        $events = [
            ['list_key' => 'hackatons', 'event_name' => 'list_view', 'payload' => ['source' => 'главная']],
            ['list_key' => 'hackatons', 'event_name' => 'filter_apply', 'payload' => ['level' => 'pro']],
            ['list_key' => 'teams', 'event_name' => 'card_open', 'payload' => []],
        ];

        foreach (range(1, 15) as $i) {
            $template = $events[$i % count($events)];
            ListAnalyticsEvent::query()->create([
                'user_id' => $users->isNotEmpty() ? $users->random() : null,
                'list_key' => $template['list_key'],
                'event_name' => $template['event_name'],
                'payload' => $template['payload'],
            ]);
        }
    }
}
