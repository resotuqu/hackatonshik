<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\HackatonApplicationChanged;
use App\Events\TeamApplicationChanged;
use App\Models\Team;
use App\ViewModels\PartnerSidebarCounts;
use Illuminate\Support\Facades\Cache;

final class InvalidateHomeCaches
{
    public function handle(HackatonApplicationChanged|TeamApplicationChanged $event): void
    {
        Cache::forget('home-public-totals-v3');
        Cache::forget('home-public-totals-v4');

        if ($event instanceof HackatonApplicationChanged && $event->invalidateHomeFeatured) {
            Cache::forget('home-featured-hackatons-v1');
            Cache::forget('home-featured-hackatons-v2');
        }

        if (Cache::supportsTags()) {
            Cache::tags(['home', 'home:totals'])->flush();
            if ($event instanceof HackatonApplicationChanged && $event->invalidateHomeFeatured) {
                Cache::tags(['home', 'home:featured'])->flush();
            }
        }

        $team = Team::query()
            ->with('roles:id,team_id,user_id')
            ->find($event->teamId);

        if (! $team) {
            return;
        }

        $userIds = $team->roles
            ->pluck('user_id')
            ->filter()
            ->push($team->user_id)
            ->unique()
            ->values();

        if ($event instanceof HackatonApplicationChanged && $event->organizerId !== null) {
            $userIds->push($event->organizerId);
            PartnerSidebarCounts::forgetForUser($event->organizerId);
        }

        if ($event instanceof TeamApplicationChanged) {
            if ($event->applicantId !== null) {
                $userIds->push($event->applicantId);
            }
            if ($event->captainId !== null) {
                $userIds->push($event->captainId);
            }
        }

        foreach ($userIds->unique()->all() as $userId) {
            Cache::forget("home-dashboard:user:{$userId}:v1");
            if (Cache::supportsTags()) {
                Cache::tags(['dashboard', "dashboard:user:{$userId}"])->flush();
            }
        }
    }
}
