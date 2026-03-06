<?php

use Laravel\Fortify\Http\Responses\RegisterResponse;
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

        return app(RegisterResponse::class);
    }
};
?>

<div>
    <x-marytoast/>
    <x-maryform wire:submit="save" class="justify-self-center w-full lg:w-1/2">
        <x-mary-header title="Регистрация" separator/>
        <x-mary-input label="Фамилия, Имя, Отчество" wire:model="fio" placeholder="Владимир" hint="Введите ваше фио"/>
        <x-marydatetime label="Дата рождения" hint="Введите вашу дату рождения" wire:model="date_of_birth"/>
        <x-mary-input label="Адрес электронной почты" wire:model="email" placeholder="example@mail.com"
                      hint="Введите вашу электронную почту"/>
        <x-mary-input label="Псевдоним" wire:model="nickname" placeholder="vova_vlad_123" hint="Введите ваш псевдоним"/>
        <x-marypassword label="Пароль" wire:model="password"/>
        <x-marypassword label="Подтверждение пароля" wire:model="password_confirmation"/>
        <x-mary-input label="Контактный номер телефона" wire:model="phone" prefix="+"/>
        <x-slot:actions>
            <x-marybutton class="btn-primary" label="Зарегистрироваться" type="submit"/>
        </x-slot:actions>
    </x-maryform>
</div>
