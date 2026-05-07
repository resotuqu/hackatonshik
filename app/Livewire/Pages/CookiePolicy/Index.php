<?php

namespace App\Livewire\Pages\CookiePolicy;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts::app', ['title' => 'Политика использования cookie'])]
class Index extends Component
{
    public function render()
    {
        return view('pages.cookie-policy.index');
    }
}
