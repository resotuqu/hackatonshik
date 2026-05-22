<?php

declare(strict_types=1);

namespace App\Livewire\Concerns;

use App\Models\Hackaton;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

trait ManagesOwnedHackatons
{
    use AuthorizesRequests;

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
        if ($hackaton === null) {
            $this->deleteHackatonId = null;
            $this->deleteHackatonModal = false;

            return;
        }

        $this->authorize('delete', $hackaton);
        $hackaton->delete();

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
}
