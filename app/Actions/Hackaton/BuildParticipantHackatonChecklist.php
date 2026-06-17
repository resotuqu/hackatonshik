<?php

declare(strict_types=1);

namespace App\Actions\Hackaton;

use App\Enums\ApplicationStatus;
use App\Models\Hackaton;
use App\Models\HackatonCaseSubmission;
use App\Models\User;
use App\Models\UserHackatonDocument;
use Illuminate\Support\Collection;

final class BuildParticipantHackatonChecklist
{
    /**
     * @return array{
     *     items: list<array{done: bool, label: string, href: string|null}>,
     *     documentsProgress: array{uploaded: int, required: int},
     *     teamSubmissionsProgress: array{submitted: int, totalCases: int}
     * }
     */
    public function handle(Hackaton $hackaton, User $user, Collection $teams): array
    {
        $items = [];

        $application = $hackaton->applications()
            ->whereIn('team_id', $teams->pluck('id'))
            ->latest()
            ->first();

        $applicationAccepted = $application?->status === ApplicationStatus::ACCEPTED;
        $items[] = [
            'done' => $applicationAccepted,
            'label' => $applicationAccepted
                ? 'Заявка команды принята'
                : ($application ? 'Ожидается решение по заявке' : 'Подайте заявку на участие'),
            'href' => route('hackatons.show', $hackaton).'#hackaton-tab-participants',
        ];

        $requiredDocuments = $hackaton->documents()
            ->where('filling_by_team_member', true)
            ->get();

        $requiredCount = $requiredDocuments->count();
        $uploadedCount = $requiredCount > 0
            ? UserHackatonDocument::query()
                ->where('user_id', $user->id)
                ->whereIn('hackaton_document_id', $requiredDocuments->pluck('id'))
                ->count()
            : 0;

        $documentsComplete = $requiredCount === 0 || $uploadedCount >= $requiredCount;
        $items[] = [
            'done' => $documentsComplete,
            'label' => $requiredCount > 0
                ? "Загрузить документы ({$uploadedCount}/{$requiredCount})"
                : 'Документы не требуются',
            'href' => route('participant.hackatons.hub', $hackaton),
        ];

        $nextCase = $hackaton->cases()
            ->where('is_published', true)
            ->whereNotNull('deadline_at')
            ->where('deadline_at', '>', now())
            ->orderBy('deadline_at')
            ->first(['id', 'title', 'deadline_at']);

        $teamIds = $teams->pluck('id');
        $publishedCases = $hackaton->cases()
            ->where('is_published', true)
            ->get(['id']);

        $submittedCaseIds = HackatonCaseSubmission::query()
            ->whereIn('team_id', $teamIds)
            ->whereIn('hackaton_case_id', $publishedCases->pluck('id'))
            ->pluck('hackaton_case_id')
            ->unique();

        $items[] = [
            'done' => $nextCase === null || $submittedCaseIds->contains($nextCase->id),
            'label' => $nextCase
                ? "Сдать решение по кейсу «{$nextCase->title}» до {$nextCase->deadline_at->format('d.m.Y H:i')}"
                : 'Все дедлайны кейсов прошли или кейсы не опубликованы',
            'href' => route('hackatons.show', $hackaton).'#hackaton-tab-cases',
        ];

        return [
            'items' => $items,
            'documentsProgress' => [
                'uploaded' => $uploadedCount,
                'required' => $requiredCount,
            ],
            'teamSubmissionsProgress' => [
                'submitted' => $submittedCaseIds->count(),
                'totalCases' => $publishedCases->count(),
            ],
        ];
    }
}
