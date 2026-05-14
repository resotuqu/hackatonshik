<?php

declare(strict_types=1);

namespace App\Actions\Hackaton;

use App\Models\Hackaton;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

final class BuildOrganizerShowHeaderMetrics
{
    /**
     * @param  array<string, int>  $metrics
     * @param  array{judgeCandidates: Collection, pendingJudgeInvitations: Collection}  $judgeManagement
     * @return array{
     *     cases_total: int,
     *     cases_published_now: int,
     *     cases_published_percent: int,
     *     judges_assigned: int,
     *     judges_pending_invites: int,
     *     next_deadline_label: string|null,
     *     next_deadline_at: Carbon|null
     * }
     */
    public function handle(Hackaton $hackaton, array $metrics, array $judgeManagement): array
    {
        $cases = $hackaton->cases;
        $casesTotal = $cases->count();
        $casesPublishedNow = $cases->filter(fn ($case) => $case->isPublishedNow())->count();
        $casesPublishedPercent = $casesTotal > 0
            ? (int) round(($casesPublishedNow / $casesTotal) * 100)
            : 0;

        $judgesAssigned = $hackaton->judges->count();
        $judgesPendingInvites = $judgeManagement['pendingJudgeInvitations']->count();

        [$nextLabel, $nextAt] = $this->resolveNextDeadline($hackaton);

        return [
            'cases_total' => $casesTotal,
            'cases_published_now' => $casesPublishedNow,
            'cases_published_percent' => $casesPublishedPercent,
            'judges_assigned' => $judgesAssigned,
            'judges_pending_invites' => $judgesPendingInvites,
            'next_deadline_label' => $nextLabel,
            'next_deadline_at' => $nextAt,
            'applications_pending' => $metrics['applications_pending'] ?? 0,
        ];
    }

    /**
     * @return array{0: string|null, 1: Carbon|null}
     */
    private function resolveNextDeadline(Hackaton $hackaton): array
    {
        $now = now();
        $candidates = [];

        if ($hackaton->registration_deadline_at instanceof Carbon && $hackaton->registration_deadline_at->isFuture()) {
            $candidates['Регистрация'] = $hackaton->registration_deadline_at;
        }

        foreach ($hackaton->cases as $case) {
            if ($case->publish_at instanceof Carbon && $case->publish_at->isFuture()) {
                $candidates['Публикация кейса: '.$case->title] = $case->publish_at;
            }
            if ($case->deadline_at instanceof Carbon && $case->deadline_at->isFuture()) {
                $candidates['Дедлайн кейса: '.$case->title] = $case->deadline_at;
            }
        }

        if ($hackaton->start_at instanceof Carbon && $hackaton->start_at->isFuture()) {
            $candidates['Старт хакатона'] = $hackaton->start_at;
        }

        if ($hackaton->end_at instanceof Carbon && $hackaton->end_at->isFuture()) {
            $candidates['Финиш хакатона'] = $hackaton->end_at;
        }

        if ($candidates === []) {
            return [null, null];
        }

        uasort($candidates, fn (Carbon $a, Carbon $b): int => $a <=> $b);

        foreach ($candidates as $label => $at) {
            if ($at->isAfter($now)) {
                return [$label, $at];
            }
        }

        return [null, null];
    }
}
