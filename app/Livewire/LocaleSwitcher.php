<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Symfony\Component\HttpFoundation\Cookie;

class LocaleSwitcher extends Component
{
    private const SUPPORTED = ['ru', 'en'];

    public string $current = 'ru';

    public function mount(): void
    {
        $this->current = app()->getLocale();
    }

    public function switch(string $locale): void
    {
        if (! in_array($locale, self::SUPPORTED, true)) {
            return;
        }

        if (Auth::check()) {
            Auth::user()->update(['locale' => $locale]);
        }

        $cookie = new Cookie(
            name: 'locale',
            value: $locale,
            expire: time() + 60 * 60 * 24 * 365,
            path: '/',
            httpOnly: false,
            sameSite: 'lax',
        );

        $this->current = $locale;

        cookie()->queue($cookie);

        $this->redirect(request()->header('Referer', '/'), navigate: true);
    }

    public function render()
    {
        return view('livewire.locale-switcher');
    }
}
