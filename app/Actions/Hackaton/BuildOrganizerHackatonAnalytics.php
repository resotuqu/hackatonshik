<?php

declare(strict_types=1);

namespace App\Actions\Hackaton;

use App\Enums\ApplicationStatus;
use App\Models\HackatonApplication;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

final class BuildOrganizerHackatonAnalytics
{
    /**
     * @return array{
     *     applicationsByDay: list<array{date: string, count: int}>,
     *     conversionRate: float|null,
     *     acceptedApplications: int,
     *     totalApplications: int
     * }
     */
    public function handle(User $user): array
    {
        $userId = (int) $user->id;
        $cacheKey = "organizer-analytics:{$userId}";

        return Cache::remember($cacheKey, now()->addMinutes(5), fn (): array => $this->build($user));
    }

    /**
     * @return array{
     *     applicationsByDay: list<array{date: string, count: int}>,
     *     conversionRate: float|null,
     *     acceptedApplications: int,
     *     totalApplications: int
     * }
     */
    private function build(User $user): array
    {
        $userId = (int) $user->id;
        $since = now()->subDays(13)->startOfDay();

        $applications = HackatonApplication::query()
            ->whereHas('hackaton', fn ($q) => $q->where('user_id', $userId))
            ->where('created_at', '>=', $since)
            ->get(['id', 'status', 'created_at']);

        $applicationsByDay = $this->buildApplicationsByDay($applications, $since);

        $totalApplications = HackatonApplication::query()
            ->whereHas('hackaton', fn ($q) => $q->where('user_id', $userId))
            ->count();

        $acceptedApplications = HackatonApplication::query()
            ->whereHas('hackaton', fn ($q) => $q->where('user_id', $userId))
            ->where('status', ApplicationStatus::ACCEPTED)
            ->count();

        $conversionRate = $totalApplications > 0
            ? round(($acceptedApplications / $totalApplications) * 100, 1)
            : null;

        return [
            'applicationsByDay' => $applicationsByDay,
            'conversionRate' => $conversionRate,
            'acceptedApplications' => $acceptedApplications,
            'totalApplications' => $totalApplications,
        ];
    }

    /**
     * @param  Collection<int, HackatonApplication>  $applications
     * @return list<array{date: string, count: int}>
     */
    private function buildApplicationsByDay(Collection $applications, \DateTimeInterface $since): array
    {
        $countsByDate = $applications
            ->groupBy(fn (HackatonApplication $app): string => $app->created_at->toDateString())
            ->map(fn (Collection $group): int => $group->count());

        $result = [];
        $cursor = Carbon::parse($since)->copy();

        while ($cursor->lte(now())) {
            $date = $cursor->toDateString();
            $result[] = [
                'date' => $date,
                'count' => (int) ($countsByDate[$date] ?? 0),
            ];
            $cursor->addDay();
        }

        return $result;
    }
}
