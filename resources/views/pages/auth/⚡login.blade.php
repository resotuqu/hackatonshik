<?php

use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Http\Responses\LoginResponse;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\Attributes\Layout;

new #[Layout('layouts::app', ['title' => 'Авторизация'])]
class extends Component {

    #[Validate(['email' => ['required', 'email']])]
    public $email = '';
    #[Validate(['password' => 'required'])]
    public $password = '';
    public $remember = false;

    public function save()
    {
        $this->validate();

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            session()->regenerate();
            return app(LoginResponse::class);
        }

        $this->addError('email', __('auth.failed'));
    }
};
?>

<div>
    <x-maryform wire:submit="save" class="justify-self-center w-full md:w-1/2">
        <x-mary-header title="Авторизация" separator/>
        <x-mary-input label="Адрес электронной почты" wire:model="email" placeholder="example@mail.com" hint="Введите вашу электронную почту"/>
        <x-marypassword label="Пароль" wire:model="password"/>
        <x-marytoggle label="Запомнить меня" wire:model="remember"/>
        <x-slot:actions>
            <x-mary-button class="btn-primary" label="Авторизироваться" type="submit"/>
        </x-slot:actions>
    </x-maryform>
</div>
