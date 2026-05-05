<?php

use App\Actions\Hackaton\BuildHackatonShowPageData;
use App\Models\Hackaton;
use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\View\View;
use Livewire\Component;

new class extends Component
{
    public Hackaton $hackaton;

    public function mount(Hackaton $hackaton): void
    {
        $hackaton->syncStatusByTimeline();
        $this->authorize('view', $hackaton);
        $this->hackaton = $hackaton;
    }

    public function placeholder(array $params = []): ViewContract
    {
        return view('pages.hackatons.show-placeholder', $params);
    }

    public function render(BuildHackatonShowPageData $pageData): View
    {
        $data = $pageData->build($this->hackaton, request());

        return view('pages.hackatons.show-inner', array_merge(
            ['hackaton' => $this->hackaton],
            $data,
        ))
            ->title($this->hackaton->title)
            ->layout('layouts::app');
    }
};
?>

