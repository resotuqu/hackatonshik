<?php

declare(strict_types=1);

namespace App\Livewire\Organizer;

use App\Actions\Hackaton\BuildOrganizerHackatonsHubData;
use App\Models\Hackaton;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Dashboard extends Component
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

    public function editHackaton(int $id): mixed
    {
        return redirect()->route('hackatons.edit', ['hackaton' => $id]);
    }

    public function participantsHackaton(int $id): mixed
    {
        return redirect()->route('profile.hackatons.participants', ['hackaton' => $id]);
    }

    #[Layout('layouts::app', ['title' => 'Организатор'])]
    public function render(BuildOrganizerHackatonsHubData $hubData): View
    {
        $data = $hubData->build(Auth::user());
        abort_if($data === null, 403);

        return view('pages.organizer.dashboard', $data);
    }
}
