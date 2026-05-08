<?php

namespace App\Livewire\Pages\Profile;

use App\Models\User;
use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

class PublicProfileShow extends Component
{
    public User $profileUser;

    public function mount(User $user): void
    {
        abort_unless($user->is_profile_public, 404);
        $user->load([
            'teams' => fn ($query) => $query->latest()->limit(6),
            'hackatons' => fn ($query) => $query->latest()->limit(6),
            'judgeAssignments.hackaton',
            'certificates.hackaton',
            'teamRoles.role',
            'teamRoles.skills',
        ]);
        $this->profileUser = $user;
    }

    public function placeholder(array $params = []): ViewContract
    {
        return view('pages.profile.public-profile-skeleton', $params);
    }

    #[Layout('layouts::app')]
    public function render(): View
    {
        $title = $this->profileUser->fio ?? $this->profileUser->nickname;

        return view('pages.profile.public-show-inner', ['profileUser' => $this->profileUser])
            ->title($title);
    }
}
