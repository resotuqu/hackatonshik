<?php

declare(strict_types=1);

namespace App\Livewire\Pages\Profile\Hackatons;

use App\Enums\ApplicationStatus;
use App\Enums\HackatonStatus;
use App\Models\Hackaton;
use App\Models\HackatonApplication;
use App\Models\TeamRole;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Index extends Component
{
    public bool $deleteHackatonModal = false;

    public ?int $deleteHackatonId = null;

    public function showDeleteHackatonModal(int $hackatonId): void
    {
        $this->deleteHackatonId = $hackatonId;
        $this->deleteHackatonModal = true;
    }

    public function deleteHackaton(): void
    {
        if ($this->deleteHackatonId === null) {
            return;
        }

        $hackaton = Hackaton::query()->find($this->deleteHackatonId);
        $hackaton?->delete();
        $this->deleteHackatonId = null;
        $this->deleteHackatonModal = false;
    }

    public function editHackaton(int $id)
    {
        return redirect()->route('hackatons.edit', ['hackaton' => $id]);
    }

    public function participantsHackaton(int $id)
    {
        return redirect()->route('profile.hackatons.participants', ['hackaton' => $id]);
    }

    /**
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

    #[Layout('layouts::app', ['title' => 'Мои хакатоны'])]
    public function render(): View
    {
        $user = Auth::user();
        $userId = (int) $user->id;

        $activeStatusValues = collect(HackatonStatus::cases())
            ->filter(fn (HackatonStatus $status): bool => $status->isActive())
            ->map(fn (HackatonStatus $status): string => $status->value)
            ->values()
            ->all();

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

        return view('pages.profile.hackatons.index', [
            'hackatons' => $hackatons,
            'summary' => $this->buildSummary($userId, $activeStatusValues),
            'featuredHackaton' => $featuredHackaton,
        ]);
    }
}
