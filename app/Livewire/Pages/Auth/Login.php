<?php

namespace App\Livewire\Pages\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Mary\Traits\Toast;

#[Layout('layouts::app', ['title' => 'Авторизация'])]
class Login extends Component
{
    use Toast;

    #[Validate(
        ['email' => ['required', 'email']],
        message: [
            'email.required' => 'Введите адрес электронной почты.',
            'email.email' => 'Укажите корректный email в формате name@example.com.',
        ]
    )]
    public $email = '';

    #[Validate(
        ['password' => 'required'],
        message: [
            'password.required' => 'Введите пароль.',
        ]
    )]
    public $password = '';

    public $remember = false;

    public function save()
    {
        try {
            $this->validate();
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->error('Проверьте заполнение полей формы.', position: 'toast-center toast-top');
            throw $e;
        }

        $throttleKey = Str::transliterate(Str::lower($this->email)).'|'.request()->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->error("Слишком много попыток входа. Попробуйте через {$seconds} сек.", position: 'toast-center toast-top');

            return;
        }

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            RateLimiter::clear($throttleKey);
            $this->success('Успешная авторизация !', position: 'toast-center toast-top');
            session()->regenerate();

            return $this->redirect('/');
        }

        RateLimiter::hit($throttleKey);
        $this->error('Не удалось войти. Проверьте email и пароль.', position: 'toast-center toast-top');
        $this->addError('email', 'Неверный email или пароль.');
    }

    public function render()
    {
        return view('pages.auth.login');
    }
}
