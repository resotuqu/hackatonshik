<?php

use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Support\Facades\Session;

new #[Layout('layouts::app', ['title' => 'Вход в админ-панель'])]
class extends Component {
    use \Mary\Traits\Toast;

    public string $login = '';
    public string $password = '';

    public function mount()
    {
        if (Session::get('admin_panel_auth') === true) {
            $this->redirect('/admin');
        }
    }

    public function save(): void
    {
        $this->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if ($this->login === 'Admin' && $this->password === 'Admin') {
            Session::put('admin_panel_auth', true);
            Session::regenerate();

            $this->success('Вход выполнен.', position: 'toast-center toast-top');
            $this->redirect('/admin');
            return;
        }

        $this->addError('login', 'Неверный логин или пароль.');
        $this->error('Ошибка входа.', position: 'toast-center toast-top');
    }
};
?>

<div>
    <x-marytoast />

    <x-maryform wire:submit="save" class="justify-self-center w-full lg:w-1/2">
        <x-mary-header title="Вход в админ-панель" separator />

        <x-mary-input label="Логин" wire:model="login" placeholder="Admin" />
        <x-marypassword label="Пароль" wire:model="password" />

        <x-slot:actions>
            <x-mary-button class="btn-primary" label="Войти" type="submit" />
        </x-slot:actions>
    </x-maryform>
</div>
