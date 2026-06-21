<?php

namespace App\Livewire;

use App\Models\Hackaton;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

class GlobalSearch extends Component
{
    public string $q = '';

    private const PER_GROUP = 5;

    public function clear(): void
    {
        $this->q = '';
    }

    /**
     * @return array{hackatons: Collection<int, Hackaton>, teams: Collection<int, Team>, users: Collection<int, User>}
     */
    #[Computed]
    public function results(): array
    {
        $term = trim($this->q);

        if (mb_strlen($term) < 2) {
            return ['hackatons' => collect(), 'teams' => collect(), 'users' => collect()];
        }

        $hackatonQuery = Hackaton::query()->where('is_public', true);
        $hackatonQuery->whereLikeInsensitive('title', $term);

        $teamQuery = Team::query()->where('is_public', true);
        $teamQuery->whereLikeInsensitive(['title', 'description'], $term);

        $userQuery = User::query()->where('is_profile_public', true);
        $userQuery->whereLikeInsensitive(['fio', 'nickname'], $term);

        return [
            'hackatons' => $hackatonQuery
                ->orderByDesc('id')
                ->limit(self::PER_GROUP)
                ->get(['id', 'title', 'status']),
            'teams' => $teamQuery
                ->orderByDesc('id')
                ->limit(self::PER_GROUP)
                ->get(['id', 'title']),
            'users' => $userQuery
                ->orderBy('nickname')
                ->limit(self::PER_GROUP)
                ->get(['id', 'fio', 'nickname', 'avatar_path']),
        ];
    }

    #[Computed]
    public function hasResults(): bool
    {
        $results = $this->results();

        return $results['hackatons']->isNotEmpty()
            || $results['teams']->isNotEmpty()
            || $results['users']->isNotEmpty();
    }

    public function render()
    {
        return view('livewire.global-search');
    }
}
