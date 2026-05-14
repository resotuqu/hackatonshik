<?php

declare(strict_types=1);

namespace App\Actions\Hackaton;

use App\Enums\ApplicationStatus;
use App\Models\Hackaton;

final class BuildOrganizerHackatonReadinessChecklist
{
    private const MIN_APPLICATIONS_THRESHOLD = 3;

    /**
     * @return list<array{done: bool, label: string, href: string|null}>
     */
    public function handle(Hackaton $hackaton): array
    {
        $hackaton->loadMissing(['documents', 'cases.fields', 'judges', 'applications']);

        $hasDocuments = $hackaton->documents->isNotEmpty();
        $publishedCases = $hackaton->cases->filter(fn ($c) => $c->isPublishedNow())->count();
        $hasPublishedCase = $publishedCases > 0;
        $casesWithFields = $hackaton->cases->filter(fn ($c) => $c->fields->isNotEmpty())->count();
        $hasJudges = $hackaton->judges->isNotEmpty();
        $applicationsAccepted = $hackaton->applications->where('status', ApplicationStatus::ACCEPTED)->count();
        $enoughApplications = $applicationsAccepted >= self::MIN_APPLICATIONS_THRESHOLD;

        return [
            [
                'done' => $hasDocuments,
                'label' => 'Настроены документы для участников',
                'href' => '#hackaton-tab-documents',
            ],
            [
                'done' => $hasPublishedCase,
                'label' => 'Есть опубликованный кейс',
                'href' => '#hackaton-tab-cases',
            ],
            [
                'done' => $casesWithFields > 0,
                'label' => 'У кейсов заданы поля решения',
                'href' => '#hackaton-tab-cases',
            ],
            [
                'done' => $hasJudges,
                'label' => 'Назначены судьи',
                'href' => '#hackaton-tab-organization',
            ],
            [
                'done' => $enoughApplications,
                'label' => 'Минимум '.self::MIN_APPLICATIONS_THRESHOLD.' принятых команд',
                'href' => '#hackaton-tab-participants',
            ],
        ];
    }
}
