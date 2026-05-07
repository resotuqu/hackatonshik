<?php

namespace App\Livewire\Pages\PrivacyPolicy;

use Livewire\Attributes\Layout;
use Livewire\Component;

class Index extends Component
{
    #[Layout('layouts::app', ['title' => 'Политика конфиденциальности и обработки персональных данных'])]
    public function render()
    {
        return view('pages.privacy-policy.index');
    }
}
