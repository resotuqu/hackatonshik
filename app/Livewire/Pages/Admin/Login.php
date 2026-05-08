<?php

namespace App\Livewire\Pages\Admin;

use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Mary\Traits\Toast;

#[Layout('layouts::app', ['title' => 'Вход в админ-панель'])]
class Login extends Component
{
    use Toast;

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

    public function render()
    {
        return view('pages.admin.login');
    }
}
