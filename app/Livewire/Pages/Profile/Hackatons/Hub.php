<?php

namespace App\Livewire\Pages\Profile\Hackatons;

use App\Actions\Hackaton\BuildParticipantHackatonHubPageData;
use App\Models\Hackaton;
use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Hub extends Component
{
    public Hackaton $hackaton;

    public function mount(Hackaton $hackaton): void
    {
        $this->authorize('view', $hackaton);
        $this->hackaton = $hackaton;
    }

    public function placeholder(array $params = []): ViewContract
    {
        return view('pages.profile.hackatons.hub-skeleton', $params);
    }

    #[Layout('layouts::app')]
    public function render(BuildParticipantHackatonHubPageData $builder): View
    {
        $user = auth()->user();
        $data = $builder->build($this->hackaton, $user);
        abort_if($data === null, 403);

        return view('pages.profile.hackatons.hub-inner', array_merge(
            ['hackaton' => $this->hackaton],
            $data,
        ))
            ->title('Мой хакатон: '.$this->hackaton->title);
    }
}
