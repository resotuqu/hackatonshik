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

        $registrationDeadline = $this->asCarbon($hackaton->registration_deadline_at);
        if ($registrationDeadline?->isFuture()) {
            $candidates['Регистрация'] = $registrationDeadline;
        }

        foreach ($hackaton->cases as $case) {
            $publishAt = $this->asCarbon($case->publish_at);
            if ($publishAt?->isFuture()) {
                $candidates['Публикация кейса: '.$case->title] = $publishAt;
            }

            $deadlineAt = $this->asCarbon($case->deadline_at);
            if ($deadlineAt?->isFuture()) {
                $candidates['Дедлайн кейса: '.$case->title] = $deadlineAt;
            }
        }

        $startAt = $this->asCarbon($hackaton->start_at);
        if ($startAt?->isFuture()) {
            $candidates['Старт хакатона'] = $startAt;
        }

        $endAt = $this->asCarbon($hackaton->end_at);
        if ($endAt?->isFuture()) {
            $candidates['Финиш хакатона'] = $endAt;
        }

        if ($candidates === []) {
            return [null, null];
        }

        uasort($candidates, fn (Carbon $a, Carbon $b): int => $a <=> $b);

        $label = array_key_first($candidates);
        $at = $candidates[$label];

        return [$label, $at];
    }

    private function asCarbon(mixed $value): ?Carbon
    {
        if ($value === null || $value === '') {
            return null;
        }

        return $value instanceof Carbon ? $value : Carbon::parse((string) $value);
    }
}
