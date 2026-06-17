<?php

namespace App\Livewire\Pages\Hackatons;

use App\Actions\Hackaton\BuildHackatonShowPresentationData;
use App\Actions\Hackaton\ComposeHackatonShowPageData;
use App\Actions\Hackaton\RecordHackatonAnalyticsEvent;
use App\Models\Hackaton;
use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Component;

class Show extends Component
{
    public Hackaton $hackaton;

    public function mount(Hackaton $hackaton, RecordHackatonAnalyticsEvent $recordEvent): void
    {
        $hackaton->syncStatusByTimeline();
        $this->authorize('view', $hackaton);
        $this->hackaton = $hackaton;

        if ($hackaton->is_public) {
            $recordEvent->handle($hackaton, 'page_view', Auth::user());
        }
    }

    public function placeholder(array $params = []): ViewContract
    {
        return view('pages.hackatons.show-skeleton', $params);
    }

    public function render(
        ComposeHackatonShowPageData $composePageData,
        BuildHackatonShowPresentationData $buildPresentationData,
    ): View {
        $pageData = $composePageData->build($this->hackaton, request());
        $presentationData = $buildPresentationData->build(
            $this->hackaton,
            $pageData['availableTeams'],
            $pageData['isOrganizer'],
            $pageData['isAssignedJudge'],
        );

        $data = array_merge($pageData, $presentationData);

        return view('pages.hackatons.show-inner', array_merge(
            ['hackaton' => $this->hackaton],
            $data,
        ))
            ->title($this->hackaton->title)
            ->layout('layouts::app');
    }
}
