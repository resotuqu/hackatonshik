<?php

namespace App\Livewire\Pages\Judge;

use App\Models\HackatonCaseSubmission;
use Illuminate\View\View;
use Livewire\Component;

class EvaluateSubmission extends Component
{
    public HackatonCaseSubmission $submission;

    public function mount(HackatonCaseSubmission $submission): void
    {
        abort_unless(auth()->check(), 403);

        $this->submission = $submission;
        $this->authorize('view', $submission);
    }

    public function render(): View
    {
        return view('pages.judge.evaluate-submission', [
            'submission' => $this->submission,
        ])
            ->title('Оценка решения')
            ->layout('layouts::app');
    }
}
