<?php

use Laravel\Fortify\Http\Responses\RegisterResponse;
use Livewire\Component;
use Livewire\Attributes\Layout;

new #[Layout('layouts::app', ['title' => 'Регистрация'])]class extends Component {
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
        $this->validate();

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

        return app(RegisterResponse::class);
    }
};
?>

<div>

    <x-form wire:submit="save" class="justify-self-center w-full md:w-1/2">
        <x-header title="Регистрация" separator/>
        <x-input label="Фамилия, Имя, Отчество" wire:model="fio" placeholder="Владимир" hint="Введите ваше фио"/>
        <p>{{$fio}}</p>
        <x-datetime label="Дата рождения" hint="Введите вашу дату рождения" wire:model="date_of_birth"/>
        <x-input label="Адрес электронной почты" wire:model="email" placeholder="example@mail.com"
                 hint="Введите вашу электронную почту"/>
        <x-input label="Псевдоним" wire:model="nickname" placeholder="vova_vlad_123" hint="Введите ваш псевдоним"/>
        <x-password label="Пароль" wire:model="password"/>
        <x-password label="Подтверждение пароля" wire:model="password_confirmation"/>
        <x-input label="Контактный номер телефона" wire:model="phone" prefix="+"/>
        <x-slot:actions>
            <x-button class="btn-primary" label="Зарегистрироваться" type="submit"/>
        </x-slot:actions>
    </x-form>
</div>
