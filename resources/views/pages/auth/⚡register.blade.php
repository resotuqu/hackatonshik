<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;

new #[Layout('layouts::app', ['title' => 'Регистрация'])]
class extends Component {

    use \Mary\Traits\Toast;

    public $fio = '';
    public $date_of_birth = '';
    public $email = '';
    public $nickname = '';
    public $phone = '';
    public $password = '';
    public $password_confirmation = '';


    protected $rules = [
        'fio' => 'required|string|max:255',
        'date_of_birth' => 'required|date',
        'email' => 'required|email|unique:users,email',
        'nickname' => 'required|string|max:255|unique:users,nickname',
        'phone' => 'required|string|max:20',
        'password' => 'required|string|min:8|confirmed',
        'password_confirmation' => 'required',
    ];

    public function save()
    {
        try {
            $this->validate();
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->error('Ошибка заполнения полей !', position: 'toast-center toast-top');
            throw $e;
        }

        $user = \App\Models\User::create([
            'fio' => $this->fio,
            'email' => $this->email,
            'nickname' => $this->nickname,
            'phone' => $this->phone,
            'date_of_birth' => $this->date_of_birth,
            'password' => $this->password,
        ]);

        Auth::login($user);
        session()->regenerate();

        $this->success('Успешная регистрация !', position: 'toast-center toast-top');

        return $this->redirect('/');
    }
};
?>

<div class="mx-auto w-full max-w-5xl">
    <x-marytoast />
    <div class="grid grid-cols-1 gap-4 lg:grid-cols-5">
        <section class="card h-fit self-start border border-base-200 bg-base-100 shadow-sm lg:col-span-2">
            <div class="card-body justify-start space-y-4">
                <h2 class="text-2xl font-semibold leading-tight">Добро пожаловать в Хакатонщик</h2>
                <p class="text-sm text-base-content/70">
                    Создайте аккаунт, чтобы участвовать в хакатонах, собирать команды и отправлять решения кейсов.
                </p>
                <div class="grid gap-2 text-sm">
                    <div class="rounded-xl border border-base-300 bg-base-200/50 p-3">
                        Создавайте профиль участника и вступайте в команды
                    </div>
                    <div class="rounded-xl border border-base-300 bg-base-200/50 p-3">
                        Подавайте заявки на хакатоны и отслеживайте статусы
                    </div>
                    <div class="rounded-xl border border-base-300 bg-base-200/50 p-3">
                        Получайте анонсы и сертификаты в личном кабинете
                    </div>
                </div>
            </div>
        </section>

        <x-maryform wire:submit="save" class="card border border-base-200 bg-base-100 p-4 shadow-sm sm:p-6 lg:col-span-3">
            <x-mary-header title="Регистрация" separator />
            <x-mary-input label="Фамилия, Имя, Отчество" wire:model="fio" placeholder="Владимир" hint="Введите ваше фио" />
            <x-marydatetime label="Дата рождения" hint="Введите вашу дату рождения" wire:model="date_of_birth" />
            <x-mary-input label="Адрес электронной почты" wire:model="email" placeholder="example@mail.com"
                hint="Введите вашу электронную почту" />
            <x-mary-input label="Псевдоним" wire:model="nickname" placeholder="vova_vlad_123" hint="Введите ваш псевдоним" />
            <x-marypassword label="Пароль" wire:model="password" />
            <x-marypassword label="Подтверждение пароля" wire:model="password_confirmation" />
            <x-mary-input label="Контактный номер телефона" wire:model="phone" prefix="+" />
            <x-slot:actions class="w-full">
                <x-marybutton class="btn-primary w-full" label="Зарегистрироваться" type="submit" />
            </x-slot:actions>
        </x-maryform>
    </div>
</div>
