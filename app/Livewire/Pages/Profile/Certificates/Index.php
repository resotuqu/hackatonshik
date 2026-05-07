<?php

namespace App\Livewire\Pages\Profile\Certificates;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Index extends Component
{
    #[Computed]
    public function certificates()
    {
        return Auth::user()?->certificates()->with('hackaton')->latest('issued_at')->get() ?? collect();
    }

    #[Layout('layouts::app', ['title' => 'Сертификаты'])]
    public function render()
    {
        return view('pages.profile.certificates.index');
    }
}
