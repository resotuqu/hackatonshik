<?php

declare(strict_types=1);

namespace App\ViewModels;

use App\Enums\ApplicationStatus;
use App\Enums\HackatonStatus;
use App\Models\Hackaton;
use App\Models\HackatonApplication;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

final readonly class PartnerSidebarCounts
{
    private function __construct(
        public int $activeHackatonsCount,
        public int $totalHackatonsCount,
        public int $pendingApplicationsCount,
    ) {}

    public static function forUser(User $user): self
    {
        return Cache::remember(
            "partner-sidebar-counts:user:{$user->id}",
            now()->addSeconds(60),
            fn (): self => self::computeForUser($user),
        );
    }

    public static function forgetForUser(int $userId): void
    {
        Cache::forget("partner-sidebar-counts:user:{$userId}");
    }

    private static function computeForUser(User $user): self
    {
        $activeStatusValues = collect(HackatonStatus::cases())
            ->filter(fn (HackatonStatus $status): bool => $status->isActive())
            ->map(fn (HackatonStatus $status): string => $status->value)
            ->values()
            ->all();

        $totalHackatonsCount = Hackaton::query()->where('user_id', $user->id)->count();

        $activeHackatonsCount = Hackaton::query()
            ->where('user_id', $user->id)
            ->whereIn('status', $activeStatusValues)
            ->count();

        $pendingApplicationsCount = HackatonApplication::query()
            ->where('status', ApplicationStatus::PENDING)
            ->whereHas('hackaton', fn ($q) => $q->where('user_id', $user->id))
            ->count();

        return new self(
            $activeHackatonsCount,
            $totalHackatonsCount,
            $pendingApplicationsCount,
        );
    }
}
