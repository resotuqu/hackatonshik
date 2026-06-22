<?php

namespace App\Livewire\Pages\Auth;

use App\Models\User;
use App\Support\PostLoginRedirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Mary\Traits\Toast;

#[Layout('layouts::app', ['title' => 'Авторизация', 'compactMain' => true])]
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
        } catch (ValidationException $e) {
            $this->error('Проверьте заполнение полей формы.', position: 'toast-center toast-top');
            throw $e;
        }

        $throttleKey = Str::transliterate(Str::lower($this->email)).'|'.request()->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->error("Слишком много попыток входа. Попробуйте через {$seconds} сек.", position: 'toast-center toast-top');

            return;
        }

        $user = User::where('email', $this->email)->first();

        if (! $user || ! Hash::check($this->password, $user->password)) {
            RateLimiter::hit($throttleKey);
            $this->error('Не удалось войти. Проверьте email и пароль.', position: 'toast-center toast-top');
            $this->addError('email', 'Неверный email или пароль.');

            return;
        }

        RateLimiter::clear($throttleKey);

        if ($user->hasEnabledTwoFactorAuthentication()) {
            session()->put([
                'login.id' => $user->getKey(),
                'login.remember' => $this->remember,
            ]);

            return $this->redirect(route('two-factor.login'));
        }

        Auth::login($user, $this->remember);
        $this->success('Успешная авторизация !', position: 'toast-center toast-top');
        session()->regenerate();

        return $this->redirect(PostLoginRedirect::intendedUrl($user));
    }

    public function render()
    {
        return view('pages.auth.login');
    }
}
