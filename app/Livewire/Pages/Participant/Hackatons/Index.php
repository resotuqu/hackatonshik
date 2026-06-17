<?php

declare(strict_types=1);

namespace App\Livewire\Pages\Participant\Hackatons;

use App\Models\User;
use App\ViewModels\HomeDashboardData;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts::app', ['title' => 'Мои заявки и хакатоны'])]
class Index extends Component
{
    public int $teamsCount = 0;

    public int $certificatesCount = 0;

    public int $pendingTeamApplicationsCount = 0;

    public int $pendingHackatonApplicationsCount = 0;

    /** @var list<array{id: int, hackaton_id: int, title: string, team_title: string, status_label: string}> */
    public array $hackatonApplicationsPreview = [];

    /** @var list<array{id: int, title: string, start_at: string|null, hub_url: string|null}> */
    public array $participantHackatons = [];

    public string $participantNextStepTitle = '';

    public string $participantNextStepHint = '';

    public ?string $participantNextStepHref = null;

    public ?string $participantNextStepLabel = null;

    public function mount(): void
    {
        $user = Auth::user();
        abort_unless($user instanceof User && $user->isParticipant(), 403);

        $dashboard = HomeDashboardData::fromUser($user);

        $this->teamsCount = $dashboard->teamsCount;
        $this->certificatesCount = $dashboard->certificatesCount;
        $this->pendingTeamApplicationsCount = $dashboard->pendingTeamApplicationsCount;
        $this->pendingHackatonApplicationsCount = $dashboard->pendingHackatonApplicationsCount;
        $this->hackatonApplicationsPreview = $dashboard->hackatonApplicationsPreview;
        $this->participantNextStepTitle = $dashboard->participantNextStepTitle;
        $this->participantNextStepHint = $dashboard->participantNextStepHint;
        $this->participantNextStepHref = $dashboard->participantNextStepHref;
        $this->participantNextStepLabel = $dashboard->participantNextStepLabel;

        $this->participantHackatons = $this->buildHackatonsList($user, $dashboard->participantHackatonsPreview);
    }

    /**
     * @param  list<array{id: int, title: string, start_at: string|null}>  $preview
     * @return list<array{id: int, title: string, start_at: string|null, hub_url: string|null}>
     */
    private function buildHackatonsList(User $user, array $preview): array
    {
        $teamHackatonIds = $user->teams()
            ->pluck('hackaton_id')
            ->merge(
                $user->teamRoles()->with('team')->get()->pluck('team.hackaton_id')
            )
            ->filter()
            ->unique()
            ->values()
            ->all();

        return collect($preview)
            ->map(function (array $row) use ($teamHackatonIds): array {
                $hasTeam = in_array($row['id'], $teamHackatonIds, true);

                return [
                    'id' => $row['id'],
                    'title' => $row['title'],
                    'start_at' => $row['start_at'],
                    'hub_url' => $hasTeam ? route('participant.hackatons.hub', ['hackaton' => $row['id']]) : null,
                ];
            })
            ->all();
    }

    public function render()
    {
        return view('pages.participant.hackatons.index');
    }
}
