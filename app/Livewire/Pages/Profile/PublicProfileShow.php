<?php

namespace App\Livewire\Pages\Profile;

use App\Enums\ApplicationStatus;
use App\Models\Hackaton;
use App\Models\User;
use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

class PublicProfileShow extends Component
{
    public User $profileUser;

    public function mount(User $user): void
    {
        if ($user->isAccountDeleted()) {
            $this->redirect(route('profile.public.deleted', $user->id));
        }

        abort_unless($user->is_profile_public, 404);
        $cacheKey = "profile:public-show:{$user->id}:v2";
        $cache = Cache::supportsTags()
            ? Cache::tags(['profiles', "profile:{$user->id}"])
            : Cache::store();

        $this->profileUser = $cache->remember(
            $cacheKey,
            now()->addMinutes(5),
            function () use ($user): User {
                return User::query()
                    ->whereKey($user->id)
                    ->withCount('judgeAssignments')
                    ->with([
                        'teams' => fn ($query) => $query->latest()->limit(6),
                        'hackatons' => fn ($query) => $query->latest()->limit(6),
                        'certificates.hackaton',
                        'teamRoles.skills',
                        'teamRoles.team.applications' => fn ($q) => $q->where('status', ApplicationStatus::ACCEPTED->value)
                            ->with('hackaton'),
                        'skills',
                    ])
                    ->firstOrFail();
            }
        );
    }

    /**
     * Get hackatons the user participated in (accepted team applications).
     *
     * @return Collection<int, Hackaton>
     */
    public function getParticipatedHackatons(): Collection
    {
        $teamIds = $this->profileUser->teamRoles->pluck('team_id')->unique();

        return Hackaton::query()
            ->whereIn('id', function ($q) use ($teamIds): void {
                $q->select('hackaton_id')
                    ->from('hackaton_applications')
                    ->where('status', ApplicationStatus::ACCEPTED->value)
                    ->whereIn('team_id', $teamIds);
            })
            ->latest('start_at')
            ->limit(8)
            ->get();
    }

    public function placeholder(array $params = []): ViewContract
    {
        return view('pages.profile.public-profile-skeleton', $params);
    }

    #[Layout('layouts::app')]
    public function render(): View
    {
        $title = $this->profileUser->publicName();

        return view('pages.profile.public-show-inner', [
            'profileUser' => $this->profileUser,
            'participatedHackatons' => $this->getParticipatedHackatons(),
        ])->title($title);
    }
}
