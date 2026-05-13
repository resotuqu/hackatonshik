<?php

declare(strict_types=1);

namespace App\Livewire\Pages\Profile\Hackatons;

use App\Enums\ApplicationStatus;
use App\Models\HackatonApplication;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Applications extends Component
{
    public function mount(): void
    {
        abort_unless(Auth::user()?->isOrganizer(), 403);
    }

    /**
     * @return Collection<int, HackatonApplication>
     */
    #[Computed]
    public function pendingApplications()
    {
        return HackatonApplication::query()
            ->with(['team', 'hackaton'])
            ->where('status', ApplicationStatus::PENDING)
            ->whereHas('hackaton', fn ($q) => $q->where('user_id', Auth::id()))
            ->latest()
            ->get();
    }

    #[Layout('layouts::app', ['title' => 'Заявки на рассмотрении'])]
    public function render(): View
    {
        return view('pages.profile.hackatons.applications');
    }
}
