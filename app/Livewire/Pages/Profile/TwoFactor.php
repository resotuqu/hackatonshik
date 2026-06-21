<?php

namespace App\Livewire\Pages\Profile;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Actions\ConfirmTwoFactorAuthentication;
use Laravel\Fortify\Actions\DisableTwoFactorAuthentication;
use Laravel\Fortify\Actions\EnableTwoFactorAuthentication;
use Laravel\Fortify\Actions\GenerateNewRecoveryCodes;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Mary\Traits\Toast;

class TwoFactor extends Component
{
    use Toast;

    public bool $enabled = false;

    public bool $confirming = false;

    public bool $showingRecoveryCodes = false;

    public bool $enableModal = false;

    public bool $disableModal = false;

    public string $password = '';

    public string $code = '';

    public function mount(): void
    {
        $this->syncState();
    }

    private function syncState(): void
    {
        $user = Auth::user();
        $this->enabled = $user !== null && $user->hasEnabledTwoFactorAuthentication();
        $this->confirming = $user !== null
            && ! is_null($user->two_factor_secret)
            && is_null($user->two_factor_confirmed_at);
    }

    public function openEnableModal(): void
    {
        $this->reset(['password', 'code']);
        $this->resetErrorBag();
        $this->enableModal = true;
    }

    public function enableTwoFactor(EnableTwoFactorAuthentication $enable): void
    {
        $user = Auth::user();
        if (! $user) {
            return;
        }

        if (! Hash::check($this->password, $user->password)) {
            $this->addError('password', 'Текущий пароль указан неверно.');

            return;
        }

        $enable($user, true);
        $user->refresh();
        $this->password = '';
        $this->enableModal = false;
        $this->syncState();
        $this->success('Отсканируйте QR-код и подтвердите настройку.', position: 'toast-center toast-top');
    }

    public function confirmTwoFactor(ConfirmTwoFactorAuthentication $confirm): void
    {
        $user = Auth::user();
        if (! $user) {
            return;
        }

        try {
            $confirm($user, $this->code);
        } catch (ValidationException $e) {
            $this->addError('code', 'Неверный код. Попробуйте ещё раз.');

            return;
        }

        $user->refresh();
        $this->code = '';
        $this->showingRecoveryCodes = true;
        $this->syncState();
        $this->success('Двухфакторная аутентификация включена.', position: 'toast-center toast-top');
    }

    public function regenerateRecoveryCodes(GenerateNewRecoveryCodes $generate): void
    {
        $user = Auth::user();
        if (! $user) {
            return;
        }

        $generate($user);
        $user->refresh();
        $this->showingRecoveryCodes = true;
        $this->success('Резервные коды перевыпущены.', position: 'toast-center toast-top');
    }

    public function openDisableModal(): void
    {
        $this->reset(['password']);
        $this->resetErrorBag();
        $this->disableModal = true;
    }

    public function disableTwoFactor(DisableTwoFactorAuthentication $disable): void
    {
        $user = Auth::user();
        if (! $user) {
            return;
        }

        if (! Hash::check($this->password, $user->password)) {
            $this->addError('password', 'Текущий пароль указан неверно.');

            return;
        }

        $disable($user);
        $user->refresh();
        $this->reset(['password', 'code']);
        $this->showingRecoveryCodes = false;
        $this->disableModal = false;
        $this->syncState();
        $this->success('Двухфакторная аутентификация отключена.', position: 'toast-center toast-top');
    }

    /**
     * @return list<string>
     */
    #[Computed]
    public function recoveryCodes(): array
    {
        $user = Auth::user();
        if (! $user || is_null($user->two_factor_recovery_codes)) {
            return [];
        }

        return $user->recoveryCodes();
    }

    public function qrCodeSvg(): string
    {
        $user = Auth::user();

        return $user ? $user->twoFactorQrCodeSvg() : '';
    }

    public function render()
    {
        return view('pages.profile.two-factor');
    }
}
