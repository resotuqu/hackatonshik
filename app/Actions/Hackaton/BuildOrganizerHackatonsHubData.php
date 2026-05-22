<?php

declare(strict_types=1);

namespace App\Actions\Hackaton;

use App\Enums\ApplicationStatus;
use App\Enums\HackatonStatus;
use App\Models\Hackaton;
use App\Models\HackatonApplication;
use App\Models\JudgeInvitation;
use App\Models\TeamRole;
use App\Models\User;
use Illuminate\Support\Collection;

final class BuildOrganizerHackatonsHubData
{
    /**
     * @return array{
     *     hackatons: Collection<int, Hackaton>,
     *     summary: array{activeHackatons: int, pendingApplications: int, participantsTotal: int, hackatonsTotal: int},
     *     featuredHackaton: Hackaton|null,
     *     globalPending: array{applications: int, judgeInvitations: int},
     *     judgeInvitationsFocusHackatonId: int|null
     * }|null
     */
    public function build(?User $user): ?array
    {
        if ($user === null) {
            return null;
        }

        $userId = (int) $user->id;

        $activeStatusValues = collect(HackatonStatus::cases())
            ->filter(fn (HackatonStatus $status): bool => $status->isActive())
            ->map(fn (HackatonStatus $status): string => $status->value)
            ->values()
            ->all();

        $summary = $this->buildSummary($userId, $activeStatusValues);

        $hackatons = Hackaton::query()
            ->where('user_id', $userId)
            ->withCount([
                'applications as pending_applications_count' => fn ($q) => $q->where('status', ApplicationStatus::PENDING),
                'roles as participants_count' => fn ($q) => $q->whereNotNull('team_roles.user_id'),
                'caseSubmissions as submissions_count',
            ])
            ->orderByDesc('start_at')
            ->get();

        $featuredHackaton = null;
        if ($hackatons->isNotEmpty()) {
            $featuredHackaton = $hackatons->sort(function (Hackaton $a, Hackaton $b): int {
                return [
                    $b->pending_applications_count,
                    $b->participants_count,
                    $b->submissions_count,
                    $b->id,
                ] <=> [
                    $a->pending_applications_count,
                    $a->participants_count,
                    $a->submissions_count,
                    $a->id,
                ];
            })->first();
        }

        $globalPending = [
            'applications' => $summary['pendingApplications'],
            'judgeInvitations' => JudgeInvitation::query()
                ->where('status', JudgeInvitation::STATUS_PENDING)
                ->whereHas('hackaton', fn ($q) => $q->where('user_id', $userId))
                ->count(),
        ];

        $judgeInvitationsFocusHackatonId = null;
        if ($globalPending['judgeInvitations'] > 0) {
            $focusHackatonId = JudgeInvitation::query()
                ->selectRaw('hackaton_id, COUNT(*) as pending_count')
                ->where('status', JudgeInvitation::STATUS_PENDING)
                ->whereHas('hackaton', fn ($q) => $q->where('user_id', $userId))
                ->groupBy('hackaton_id')
                ->orderByDesc('pending_count')
                ->value('hackaton_id');

            if (is_numeric($focusHackatonId)) {
                $judgeInvitationsFocusHackatonId = (int) $focusHackatonId;
            }
        }

        return [
            'hackatons' => $hackatons,
            'summary' => $summary,
            'featuredHackaton' => $featuredHackaton,
            'globalPending' => $globalPending,
            'judgeInvitationsFocusHackatonId' => $judgeInvitationsFocusHackatonId,
        ];
    }

    /**
     * @param  list<string>  $activeStatusValues
     * @return array{activeHackatons: int, pendingApplications: int, participantsTotal: int, hackatonsTotal: int}
     */
    private function buildSummary(int $userId, array $activeStatusValues): array
    {
        $activeHackatons = Hackaton::query()
            ->where('user_id', $userId)
            ->whereIn('status', $activeStatusValues)
            ->count();

        $pendingApplications = HackatonApplication::query()
            ->where('status', ApplicationStatus::PENDING)
            ->whereHas('hackaton', fn ($q) => $q->where('user_id', $userId))
            ->count();

        $participantsTotal = TeamRole::query()
            ->whereNotNull('team_roles.user_id')
            ->whereHas('team', fn ($q) => $q->whereHas('hackaton', fn ($hq) => $hq->where('user_id', $userId)))
            ->count();

        $hackatonsTotal = Hackaton::query()->where('user_id', $userId)->count();

        return [
            'activeHackatons' => $activeHackatons,
            'pendingApplications' => $pendingApplications,
            'participantsTotal' => $participantsTotal,
            'hackatonsTotal' => $hackatonsTotal,
        ];
    }
}
