<?php

use App\Models\Team;
use Illuminate\View\View;
use Livewire\Component;

new class extends Component
{
    public Team $team;

    public function mount(Team $team): void
    {
        $this->authorize('view', $team);
        $team->loadShowRelations();
        $this->team = $team;
    }

    public function render(): View
    {
        return view('pages.teams.show-inner', ['team' => $this->team])
            ->title($this->team->title)
            ->layout('layouts::app');
    }
};
?>

