<?php

declare(strict_types=1);

namespace App\Livewire\Pages\Profile\Watches;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts::app', ['title' => 'Мои закладки'])]
class Index extends Component
{
    use WithPagination;

    public function render(): View
    {
        $user = Auth::user();

        $watchedHackatons = $user
            ? $user->watchedHackatons()->withCount('teams')->paginate(9)
            : null;

        return view('pages.profile.watches.index', [
            'watchedHackatons' => $watchedHackatons,
        ]);
    }
}
