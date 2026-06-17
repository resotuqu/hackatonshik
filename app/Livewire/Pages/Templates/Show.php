<?php

declare(strict_types=1);

namespace App\Livewire\Pages\Templates;

use App\Models\HackatonTemplate;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts::app', ['title' => 'Шаблон хакатона'])]
class Show extends Component
{
    public HackatonTemplate $template;

    public function mount(string $slug): void
    {
        $template = HackatonTemplate::query()->public()->where('slug', $slug)->firstOrFail();
        $this->template = $template;
    }

    public function render(): View
    {
        return view('pages.templates.show', [
            'template' => $this->template,
        ]);
    }
}
