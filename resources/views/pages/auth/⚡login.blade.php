<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\Attributes\Layout;
new #[Layout('layouts::app', ['title' => 'Авторизация'])]
class extends Component {


    use \Mary\Traits\Toast;

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

        $throttleKey = Str::transliterate(Str::lower($this->email)) . '|' . request()->ip();

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

};
?>
<div class="mx-auto w-full max-w-5xl">
    <x-marytoast />
    <div class="grid grid-cols-1 gap-4 lg:grid-cols-5">
        <section class="card h-fit self-start border border-base-200 bg-base-100 shadow-sm lg:col-span-2">
            <div class="card-body justify-start space-y-4">
                <h2 class="text-2xl font-semibold leading-tight">С возвращением в Хакатонщик</h2>
                <p class="text-sm text-base-content/70">
                    Войдите, чтобы продолжить работу с командами, заявками и кейсами.
                </p>
                <div class="grid gap-2 text-sm">
                    <div class="rounded-xl border border-base-300 bg-base-200/50 p-3">
                        Отслеживайте статусы заявок в одном месте
                    </div>
                    <div class="rounded-xl border border-base-300 bg-base-200/50 p-3">
                        Отправляйте решения кейсов без лишней переписки
                    </div>
                    <div class="rounded-xl border border-base-300 bg-base-200/50 p-3">
                        Получайте анонсы и обновления по хакатонам
                    </div>
                </div>
            </div>
        </section>

        <x-maryform wire:submit="save" class="card border border-base-200 bg-base-100 p-4 shadow-sm sm:p-6 lg:col-span-3">
            <x-mary-header title="Авторизация" separator />
            <div class="space-y-3">
                <x-mary-input
                    label="Адрес электронной почты"
                    wire:model="email"
                    placeholder="example@mail.com"
                    hint="Введите адрес, который использовали при регистрации" />
                <x-marypassword label="Пароль" wire:model="password" />
                <div class="flex items-center justify-between gap-3 pt-1">
                    <x-marytoggle label="Запомнить меня" wire:model="remember" />
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="link link-primary text-sm">
                            Забыли пароль?
                        </a>
                    @endif
                </div>
            </div>

            <x-slot:actions class="w-full">
                <x-mary-button class="btn-primary w-full" label="Войти в аккаунт" type="submit" wire:loading.attr="disabled" wire:target="save" spinner="save" />
            </x-slot:actions>

            <a href="/auth/yandex/redirect" class="block w-full rounded-xl bg-[#FC3F1D] px-4 py-3 text-white transition hover:brightness-95 focus:outline-none focus:ring-2 focus:ring-[#FC3F1D]/40">
                <span class="inline-flex w-full items-center justify-center gap-3 text-sm font-semibold">
                    <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-white text-sm font-black text-[#FC3F1D]">Я</span>
                    Войти через Яндекс ID
                </span>
            </a>
            <a href="/auth/vk/redirect" class="btn btn-outline w-full border-[#2787F5] text-[#2787F5] hover:bg-[#2787F5] hover:text-white">
                Войти через VK ID
            </a>
        </x-maryform>
    </div>
</div>
