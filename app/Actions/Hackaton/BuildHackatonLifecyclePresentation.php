<?php

declare(strict_types=1);

namespace App\Actions\Hackaton;

use App\Enums\HackatonStatus;
use App\Models\Hackaton;

final class BuildHackatonLifecyclePresentation
{
    /**
     * Ordered lifecycle phases for the organizer timeline (subset of statuses).
     *
     * @return array{
     *     steps: list<array{value: string, label: string}>,
     *     current_index: int,
     *     current_label: string,
     *     next_step_label: string|null
     * }
     */
    public function handle(Hackaton $hackaton): array
    {
        $ordered = [
            HackatonStatus::DRAFT,
            HackatonStatus::PUBLISHED,
            HackatonStatus::REGISTRATION_OPEN,
            HackatonStatus::REGISTRATION_CLOSED,
            HackatonStatus::WAITING_START,
            HackatonStatus::CASES_ANNOUNCED,
            HackatonStatus::IN_PROGRESS,
            HackatonStatus::JUDGING,
            HackatonStatus::FINISHED,
            HackatonStatus::ARCHIVED,
        ];

        $steps = [];
        foreach ($ordered as $status) {
            $steps[] = [
                'value' => $status->value,
                'label' => $status->label(),
            ];
        }

        $current = $hackaton->status;
        $currentIndex = 0;
        foreach ($ordered as $i => $status) {
            if ($status === $current) {
                $currentIndex = $i;

                break;
            }
        }

        $nextStepLabel = null;
        if ($currentIndex < count($ordered) - 1) {
            $next = $ordered[$currentIndex + 1];
            $nextStepLabel = $next->label();
        }

        return [
            'steps' => $steps,
            'current_index' => $currentIndex,
            'current_label' => $current->label(),
            'next_step_label' => $nextStepLabel,
        ];
    }
}
