<?php

declare(strict_types=1);

namespace App\Livewire\Pages\Templates;

use App\Models\HackatonTemplate;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts::app', ['title' => 'Шаблоны хакатонов'])]
class Index extends Component
{
    #[Url(as: 'locale')]
    public string $locale = '';

    #[Url(as: 'level')]
    public string $level = '';

    public function render(): View
    {
        return view('pages.templates.index', [
            'templates' => HackatonTemplate::query()
                ->public()
                ->when($this->locale !== '', fn ($query) => $query->where('locale', $this->locale))
                ->when($this->level !== '', fn ($query) => $query->where('level', $this->level))
                ->orderBy('sort_order')
                ->get(),
        ]);
    }
}
