<?php

declare(strict_types=1);

namespace App\Livewire\Pages\Profile\Hackatons;

use App\Enums\HackatonStatus;
use App\Models\Hackaton;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Finished extends Component
{
    public function mount(): void
    {
        abort_unless(Auth::user()?->isOrganizer(), 403);
    }

    /**
     * @return Collection<int, Hackaton>
     */
    #[Computed]
    public function finishedHackatons(): Collection
    {
        return Hackaton::query()
            ->where('user_id', Auth::id())
            ->whereIn('status', [
                HackatonStatus::FINISHED->value,
                HackatonStatus::ARCHIVED->value,
            ])
            ->withCount([
                'roles as participants_count' => fn ($q) => $q->whereNotNull('team_roles.user_id'),
            ])
            ->orderByDesc('end_at')
            ->get();
    }

    #[Layout('layouts::app', ['title' => 'Завершённые хакатоны'])]
    public function render(): View
    {
        return view('pages.profile.hackatons.finished');
    }
}
