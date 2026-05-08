<?php

namespace App\Livewire\Pages\Teams;

use App\Models\Team;
use App\Support\SafeMarkdown;
use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Show extends Component
{
    public Team $team;

    public function mount(Team $team): void
    {
        $this->authorize('view', $team);
        $team->loadShowRelations();
        $this->team = $team;
    }

    public function placeholder(array $params = []): ViewContract
    {
        return view('pages.teams.show-placeholder', $params);
    }

    #[Layout('layouts::app')]
    public function render(): View
    {
        $plainDescription = strip_tags(SafeMarkdown::toHtml($this->team->description ?? ''));
        $plainDescription = preg_replace('/\s+/u', ' ', $plainDescription) ?? '';
        $seoDescription = trim(mb_substr($plainDescription !== '' ? $plainDescription : 'Команда участників хакатонов на платформе «Хакатонщик».', 0, 180, 'UTF-8'));

        $teamImage = filled($this->team->image_url)
            ? (str_starts_with($this->team->image_url, 'http') ? $this->team->image_url : asset('storage/'.$this->team->image_url))
            : null;

        return view('pages.teams.show', [
            'team' => $this->team,
            'seoDescription' => $seoDescription,
            'teamImage' => $teamImage,
        ])->title($this->team->title);
    }
}
