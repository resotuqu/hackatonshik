<?php

declare(strict_types=1);

namespace App\Livewire\Organizer;

use App\Actions\Hackaton\BuildOrganizerHackatonsHubData;
use App\Livewire\Concerns\ManagesOwnedHackatons;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Dashboard extends Component
{
    use ManagesOwnedHackatons;

    #[Layout('layouts::app', ['title' => 'Организатор'])]
    public function render(BuildOrganizerHackatonsHubData $hubData): View
    {
        $data = $hubData->build(Auth::user());

        return view('pages.organizer.dashboard', $data);
    }
}
