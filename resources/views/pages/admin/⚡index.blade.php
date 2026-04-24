<?php

use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

new #[Layout('layouts::app', ['title' => 'Админ-панель'])]
class extends Component {
    use \Mary\Traits\Toast;

    public string $fio = '';
    public string $date_of_birth = '';
    public string $email = '';
    public string $nickname = '';
    public string $phone = '';
    public string $password = '';
    public string $password_confirmation = '';
    public ?string $description = null;

    public function logout(): void
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
        $this->redirect('/login');
    }

    public function savePartner(): void
    {
        $this->validate([
            'fio' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['required', 'date'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'nickname' => ['required', 'string', 'max:255', 'unique:users,nickname'],
            'phone' => ['required', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'description' => ['nullable', 'string', 'max:2000'],
        ], [
            'fio.required' => 'ФИО обязательно.',
            'date_of_birth.required' => 'Дата рождения обязательна.',
            'email.required' => 'Email обязателен.',
            'email.unique' => 'Такой email уже существует.',
            'nickname.required' => 'Никнейм обязателен.',
            'nickname.unique' => 'Такой никнейм уже существует.',
            'phone.required' => 'Телефон обязателен.',
            'password.required' => 'Пароль обязателен.',
            'password.min' => 'Пароль должен содержать минимум 8 символов.',
            'password.confirmed' => 'Подтверждение пароля не совпадает.',
        ]);

        User::query()->create([
            'fio' => $this->fio,
            'date_of_birth' => $this->date_of_birth,
            'email' => $this->email,
            'nickname' => $this->nickname,
            'phone' => $this->phone,
            'password' => $this->password,
            'role' => 'partner',
            'description' => $this->description,
        ]);

        $this->reset(['fio', 'date_of_birth', 'email', 'nickname', 'phone', 'password', 'password_confirmation', 'description']);

        $this->success('Партнёр успешно создан.', position: 'toast-center toast-top');
    }
};
?>

<div>
    <x-marytoast />

    <x-mary-card title="Создание партнёра" class="w-full lg:w-2/3 justify-self-center card card-border bg-base-100">
        <x-slot:menu>
            <x-mary-button label="Выйти" class="btn-secondary" wire:click="logout" />
        </x-slot:menu>

        <x-maryform wire:submit="savePartner">

            <x-mary-input label="ФИО" wire:model="fio" />
            <x-marydatetime label="Дата рождения" wire:model="date_of_birth" />
            <x-mary-input label="Email" wire:model="email" placeholder="partner@mail.com" />
            <x-mary-input label="Никнейм" wire:model="nickname" />
            <x-mary-input label="Телефон" wire:model="phone" prefix="+" />
            <x-marypassword label="Пароль" wire:model="password" />
            <x-marypassword label="Подтверждение пароля" wire:model="password_confirmation" />
            <x-marymarkdown wire:model="description" label="Описание" :config="['toolbar' => ['bold', 'italic', '|', 'preview'], 'uploadImage' => false]" />

            <x-slot:actions>
                <x-mary-button label="Создать партнёра" class="btn-primary" type="submit" />
            </x-slot:actions>
        </x-maryform>
    </x-mary-card>
</div>
