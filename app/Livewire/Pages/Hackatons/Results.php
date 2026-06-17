<?php

declare(strict_types=1);

namespace App\Livewire\Pages\Hackatons;

use App\Actions\Hackaton\BuildHackatonTeamLeaderboard;
use App\Models\Hackaton;
use App\Models\Team;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts::app')]
class Results extends Component
{
    use AuthorizesRequests;

    public Hackaton $hackaton;

    /** @var list<array{team: Team|null, total_score: int, max_score: int, completion_percent: int, place: int}> */
    public array $leaderboard = [];

    public function mount(Hackaton $hackaton, BuildHackatonTeamLeaderboard $buildLeaderboard): void
    {
        $hackaton->syncStatusByTimeline();
        $this->authorize('viewPublicResults', $hackaton);
        $this->hackaton = $hackaton;

        $entries = $buildLeaderboard->handle($hackaton);
        $place = 1;
        foreach ($entries as $entry) {
            $this->leaderboard[] = array_merge($entry, ['place' => $place++]);
        }
    }

    public function render(): View
    {
        return view('pages.hackatons.results')
            ->title('Итоги: '.$this->hackaton->title)
            ->layoutData([
                'meta_description' => 'Публичные итоги хакатона «'.$this->hackaton->title.'».',
                'canonical_url' => route('hackatons.results', $this->hackaton),
            ]);
    }
}
