<?php

declare(strict_types=1);

namespace App\Livewire\Pages\Auth;

use App\Support\PostLoginRedirect;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts::app', ['title' => 'Согласие на обработку данных', 'compactMain' => true])]
class OAuthConsent extends Component
{
    public bool $pd_consent = false;

    public ?string $date_of_birth = null;

    public function mount(): void
    {
        $user = auth()->user();

        abort_unless($user !== null, 403);

        if ($user->pd_consent_accepted_at !== null) {
            $this->redirect($this->nextRoute());
        }

        $this->date_of_birth = $user->date_of_birth?->format('Y-m-d');
    }

    public function save(): void
    {
        $user = auth()->user();

        abort_unless($user !== null, 403);

        $rules = [
            'pd_consent' => ['accepted'],
        ];

        if ($user->date_of_birth === null) {
            $rules['date_of_birth'] = ['required', 'date', 'before:now'];
        }

        $this->validate($rules);

        $user->forceFill([
            'pd_consent_accepted_at' => now(),
            'date_of_birth' => $user->date_of_birth ?? $this->date_of_birth,
        ])->save();

        $this->redirect($this->nextRoute());
    }

    private function nextRoute(): string
    {
        $user = auth()->user();

        if ($user === null) {
            return route('login');
        }

        if ($user->hasVerifiedContactChannels()) {
            return PostLoginRedirect::intendedUrl($user);
        }

        return route('phone.verify.notice');
    }

    public function render()
    {
        return view('pages.auth.oauth-consent');
    }
}
