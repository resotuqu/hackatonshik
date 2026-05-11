<?php

namespace App\Livewire\Pages\Judge;

use Illuminate\View\View;
use Livewire\Component;

class Dashboard extends Component
{
    public function mount(): void
    {
        abort_unless(auth()->check(), 403);
        abort_unless(auth()->user()->isJudge(), 403);
    }

    public function render(): View
    {
        return view('pages.judge.dashboard')
            ->title('Панель судьи')
            ->layout('layouts::app');
    }
}
