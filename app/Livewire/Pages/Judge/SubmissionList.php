<?php

namespace App\Livewire\Pages\Judge;

use App\Models\Hackaton;
use App\Models\HackatonCase;
use Illuminate\View\View;
use Livewire\Component;

class SubmissionList extends Component
{
    public Hackaton $hackaton;

    public HackatonCase $case;

    public function mount(Hackaton $hackaton, HackatonCase $case): void
    {
        abort_unless(auth()->check(), 403);
        abort_unless($case->hackaton_id === $hackaton->id, 404);
        abort_unless($hackaton->isJudge(auth()->user()) || (int) $hackaton->user_id === (int) auth()->id(), 403);

        $this->hackaton = $hackaton;
        $this->case = $case;
    }

    public function render(): View
    {
        return view('pages.judge.submission-list', [
            'hackaton' => $this->hackaton,
            'case' => $this->case,
        ])
            ->title("Оценивание • {$this->case->title}")
            ->layout('layouts::app');
    }
}
