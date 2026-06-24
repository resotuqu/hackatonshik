<?php

namespace App\Livewire\Pages\Profile;

use App\Models\User;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

class PublicProfileDeleted extends Component
{
    public User $user;

    public function mount(User $user): void
    {
        abort_unless($user->isAccountDeleted(), 404);
        $this->user = $user;
    }

    #[Layout('layouts::app')]
    public function render(): View
    {
        return view('pages.profile.public-profile-deleted')
            ->title($this->user->getDeletedDisplayName());
    }
}
