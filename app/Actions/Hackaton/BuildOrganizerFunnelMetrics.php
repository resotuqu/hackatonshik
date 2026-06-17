<?php

declare(strict_types=1);

namespace App\Actions\Hackaton;

use App\Enums\ApplicationStatus;
use App\Models\Hackaton;
use App\Models\HackatonAnalyticsEvent;
use App\Models\HackatonApplication;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

final class BuildOrganizerFunnelMetrics
{
    /**
     * @return array{
     *     summary: array{views: int, applications: int, accepted: int, viewToApplicationRate: float|null, applicationToAcceptedRate: float|null},
     *     slices: array{weekly: list<array{label: string, views: int, applications: int, accepted: int}>, monthly: list<array{label: string, views: int, applications: int, accepted: int}>},
     *     statusSegments: array<string, int>,
     *     retentionRate: float|null,
     *     hackatons: list<array{hackaton_id: int, title: string, views: int, applications: int, accepted: int, conversionRate: float|null, completionRate: float|null, pending: int, rejected: int}>
     * }
     */
    public function handle(User $user): array
    {
        $userId = (int) $user->id;

        return Cache::remember(
            'organizer:'.$userId.':funnel',
            now()->addMinutes(15),
            fn (): array => $this->buildUncached($userId),
        );
    }

    /**
     * @return array{
     *     summary: array{views: int, applications: int, accepted: int, viewToApplicationRate: float|null, applicationToAcceptedRate: float|null},
     *     slices: array{weekly: list<array{label: string, views: int, applications: int, accepted: int}>, monthly: list<array{label: string, views: int, applications: int, accepted: int}>},
     *     statusSegments: array<string, int>,
     *     retentionRate: float|null,
     *     hackatons: list<array{hackaton_id: int, title: string, views: int, applications: int, accepted: int, conversionRate: float|null, completionRate: float|null, pending: int, rejected: int}>
     * }
     */
    private function buildUncached(int $userId): array
    {
        $hackatonIds = Hackaton::query()
            ->where('user_id', $userId)
            ->pluck('id');

        if ($hackatonIds->isEmpty()) {
            return [
                'summary' => [
                    'views' => 0,
                    'applications' => 0,
                    'accepted' => 0,
                    'viewToApplicationRate' => null,
                    'applicationToAcceptedRate' => null,
                ],
                'slices' => [
                    'weekly' => [],
                    'monthly' => [],
                ],
                'statusSegments' => [],
                'retentionRate' => null,
                'hackatons' => [],
            ];
        }

        $viewsByHackaton = HackatonAnalyticsEvent::query()
            ->whereIn('hackaton_id', $hackatonIds)
            ->where('event_name', 'page_view')
            ->selectRaw('hackaton_id, COUNT(*) as total')
            ->groupBy('hackaton_id')
            ->pluck('total', 'hackaton_id');

        $applicationsByHackaton = HackatonApplication::query()
            ->whereIn('hackaton_id', $hackatonIds)
            ->selectRaw('hackaton_id, COUNT(*) as total')
            ->groupBy('hackaton_id')
            ->pluck('total', 'hackaton_id');

        $acceptedByHackaton = HackatonApplication::query()
            ->whereIn('hackaton_id', $hackatonIds)
            ->where('status', ApplicationStatus::ACCEPTED)
            ->selectRaw('hackaton_id, COUNT(*) as total')
            ->groupBy('hackaton_id')
            ->pluck('total', 'hackaton_id');

        $pendingByHackaton = HackatonApplication::query()
            ->whereIn('hackaton_id', $hackatonIds)
            ->where('status', ApplicationStatus::PENDING)
            ->selectRaw('hackaton_id, COUNT(*) as total')
            ->groupBy('hackaton_id')
            ->pluck('total', 'hackaton_id');

        $rejectedByHackaton = HackatonApplication::query()
            ->whereIn('hackaton_id', $hackatonIds)
            ->where('status', ApplicationStatus::REJECTED)
            ->selectRaw('hackaton_id, COUNT(*) as total')
            ->groupBy('hackaton_id')
            ->pluck('total', 'hackaton_id');

        $hackatons = Hackaton::query()
            ->whereIn('id', $hackatonIds)
            ->orderByDesc('start_at')
            ->get(['id', 'title']);

        $rows = [];
        $totalViews = 0;
        $totalApplications = 0;
        $totalAccepted = 0;

        foreach ($hackatons as $hackaton) {
            $views = (int) ($viewsByHackaton[$hackaton->id] ?? 0);
            $applications = (int) ($applicationsByHackaton[$hackaton->id] ?? 0);
            $accepted = (int) ($acceptedByHackaton[$hackaton->id] ?? 0);
            $pending = (int) ($pendingByHackaton[$hackaton->id] ?? 0);
            $rejected = (int) ($rejectedByHackaton[$hackaton->id] ?? 0);

            $totalViews += $views;
            $totalApplications += $applications;
            $totalAccepted += $accepted;

            $rows[] = [
                'hackaton_id' => $hackaton->id,
                'title' => $hackaton->title,
                'views' => $views,
                'applications' => $applications,
                'accepted' => $accepted,
                'pending' => $pending,
                'rejected' => $rejected,
                'conversionRate' => $views > 0
                    ? round(($applications / $views) * 100, 1)
                    : null,
                'completionRate' => $applications > 0
                    ? round(($accepted / $applications) * 100, 1)
                    : null,
            ];
        }

        return [
            'summary' => [
                'views' => $totalViews,
                'applications' => $totalApplications,
                'accepted' => $totalAccepted,
                'viewToApplicationRate' => $totalViews > 0
                    ? round(($totalApplications / $totalViews) * 100, 1)
                    : null,
                'applicationToAcceptedRate' => $totalApplications > 0
                    ? round(($totalAccepted / $totalApplications) * 100, 1)
                    : null,
            ],
            'slices' => [
                'weekly' => $this->buildTimeSlice($hackatonIds->all(), 'week', 8),
                'monthly' => $this->buildTimeSlice($hackatonIds->all(), 'month', 6),
            ],
            'statusSegments' => $this->buildStatusSegments($hackatonIds->all()),
            'retentionRate' => $this->calculateRetentionRate($userId, $hackatonIds->all()),
            'hackatons' => $rows,
        ];
    }

    /**
     * @param  list<int>  $hackatonIds
     * @return list<array{label: string, views: int, applications: int, accepted: int}>
     */
    private function buildTimeSlice(array $hackatonIds, string $period, int $points): array
    {
        $cursor = CarbonImmutable::now()->startOf($period)->sub($points - 1, $period);
        $rows = [];

        for ($index = 0; $index < $points; $index++) {
            $start = $cursor->add($index, $period);
            $end = $start->endOf($period);
            $views = HackatonAnalyticsEvent::query()
                ->whereIn('hackaton_id', $hackatonIds)
                ->where('event_name', 'page_view')
                ->whereBetween('created_at', [$start, $end])
                ->count();
            $applications = HackatonApplication::query()
                ->whereIn('hackaton_id', $hackatonIds)
                ->whereBetween('created_at', [$start, $end])
                ->count();
            $accepted = HackatonApplication::query()
                ->whereIn('hackaton_id', $hackatonIds)
                ->where('status', ApplicationStatus::ACCEPTED)
                ->whereBetween('created_at', [$start, $end])
                ->count();

            $rows[] = [
                'label' => $period === 'week' ? $start->format('d.m') : $start->format('m.Y'),
                'views' => $views,
                'applications' => $applications,
                'accepted' => $accepted,
            ];
        }

        return $rows;
    }

    /**
     * @param  list<int>  $hackatonIds
     * @return array<string, int>
     */
    private function buildStatusSegments(array $hackatonIds): array
    {
        return HackatonApplication::query()
            ->whereIn('hackaton_id', $hackatonIds)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->map(fn (int $total): int => (int) $total)
            ->all();
    }

    /**
     * @param  list<int>  $hackatonIds
     */
    private function calculateRetentionRate(int $organizerId, array $hackatonIds): ?float
    {
        if (count($hackatonIds) < 2) {
            return null;
        }

        $participantCounts = DB::table('team_roles')
            ->join('teams', 'teams.id', '=', 'team_roles.team_id')
            ->whereIn('teams.hackaton_id', $hackatonIds)
            ->whereNotNull('team_roles.user_id')
            ->selectRaw('team_roles.user_id, COUNT(DISTINCT teams.hackaton_id) as hackaton_count')
            ->groupBy('team_roles.user_id')
            ->pluck('hackaton_count');

        if ($participantCounts->isEmpty()) {
            return null;
        }

        $repeatParticipants = $participantCounts->filter(fn (int $count): bool => $count >= 2)->count();
        $totalParticipants = $participantCounts->count();

        return round(($repeatParticipants / $totalParticipants) * 100, 1);
    }
}
