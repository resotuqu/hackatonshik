<?php

namespace App\Livewire\Pages\Judge;

use App\Actions\Judge\BuildJudgeHackatonScoringSummary;
use App\Models\Hackaton;
use Illuminate\View\View;
use Livewire\Component;

class HackatonShow extends Component
{
    public Hackaton $hackaton;

    /**
     * @var array<string, mixed>
     */
    public array $scoringSummary = [];

    public function mount(Hackaton $hackaton, BuildJudgeHackatonScoringSummary $summary): void
    {
        abort_unless(auth()->check(), 403);
        abort_unless($hackaton->isJudge(auth()->user()) || (int) $hackaton->user_id === (int) auth()->id(), 403);

        $this->hackaton = $hackaton->load(['cases:id,hackaton_id,title']);
        $this->scoringSummary = $summary->handle($hackaton, auth()->user());
    }

    public function render(): View
    {
        return view('pages.judge.hackaton-show', [
            'hackaton' => $this->hackaton,
            'scoringSummary' => $this->scoringSummary,
        ])
            ->title("Судья • {$this->hackaton->title}")
            ->layout('layouts::app');
    }
}
