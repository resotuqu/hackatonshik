<?php

namespace App\Livewire\Pages\Profile\Hackatons;

use App\Models\Team;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\Attributes\Computed;

class Index extends Component
{
    #[Computed]
    public function hackatons()
    {
        return \App\Models\Hackaton::query()->where('user_id', '=', Auth::user()->id)->get();
    }

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

        $hackaton = \App\Models\Hackaton::find($this->deleteHackatonId);
        $hackaton?->delete();
        $this->deleteHackatonId = null;
        $this->deleteHackatonModal = false;
    }

    public function editHackaton($id) {
        return redirect('/hackatons/' . $id . '/edit');
    }

    public function participantsHackaton($id) {
        return redirect('/profile/hackatons/' . $id . '/participants');
    }

    #[Layout('layouts::app', ['title' => 'Мои хакатоны'])]
    public function render()
    {
        return view('pages.profile.hackatons.index');
    }
}
