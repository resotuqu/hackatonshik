<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::app', ['title' => 'Профиль'])]
class extends Component {
    use \Mary\Traits\Toast;

    public array $config = [
        'toolbar' => ['heading', 'bold', 'italic', '|', 'preview'],
        'uploadImage' => false,
    ];

    public string $fio = '';
    public string $nickname = '';
    public string $role = '';
    public string $date_of_birth = '';
    public string $email = '';
    public ?string $description = null;
    public string $current_password = '';
    public string $new_password = '';
    public string $new_password_confirmation = '';

    public function mount()
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();
        $this->fio = $user->fio;
        $this->nickname = $user->nickname;
        $this->role = $user->role == 'user' ? 'Участник' : ($user->role == 'partner' ? 'Партнёр' : 'Администратор');
        $this->date_of_birth = $user->date_of_birth;
        $this->email = $user->email;
        $this->description = $user->description;
    }

    public function save(): void
    {
        $user = Auth::user();

        if (!$user) {
            $this->redirect('/login');
            return;
        }

        $this->validate([
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'description' => ['nullable', 'string', 'max:2000'],
            'current_password' => ['required', 'string'],
            'new_password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ], [
            'email.required' => 'Электронная почта обязательна.',
            'email.email' => 'Введите корректный email.',
            'email.unique' => 'Этот email уже используется.',
            'description.max' => 'Описание не должно превышать 2000 символов.',
            'current_password.required' => 'Для подтверждения введите текущий пароль.',
            'new_password.min' => 'Новый пароль должен содержать минимум 8 символов.',
            'new_password.confirmed' => 'Подтверждение нового пароля не совпадает.',
        ]);

        if (!Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', 'Текущий пароль указан неверно.');
            return;
        }

        $payload = [
            'email' => $this->email,
            'description' => $this->description,
        ];

        if ($this->new_password !== '') {
            $payload['password'] = $this->new_password;
        }

        $user->update($payload);

        $this->current_password = '';
        $this->new_password = '';
        $this->new_password_confirmation = '';

        $this->success('Профиль успешно обновлён.', position: 'toast-center toast-top');
    }
};
?>

<div>
    <x-marytoast />

    <x-mary-card title="Данные профиля" class="w-full lg:w-1/2 justify-self-center card card-border bg-base-100 mb-4">
        <div class="grid grid-cols-1 gap-3">
            <x-mary-input label="ФИО" :value="$fio" readonly />
            <x-mary-input label="Никнейм" :value="$nickname" readonly />
            <x-mary-input label="Роль" :value="$role" readonly />
            <x-mary-input label="Дата рождения" :value="$date_of_birth" readonly />
        </div>
    </x-mary-card>

    <x-mary-card title="Профиль" class="w-full lg:w-1/2 justify-self-center card card-border bg-base-100">
        <x-maryform wire:submit="save">
            <x-mary-input label="Электронная почта" wire:model="email" placeholder="example@mail.com" />

            <x-marymarkdown wire:model="description" label="Описание" :config="$this->config" />

            <x-marypassword label="Текущий пароль (для подтверждения)" wire:model="current_password" />
            <x-marypassword label="Новый пароль" wire:model="new_password" />
            <x-marypassword label="Подтверждение нового пароля" wire:model="new_password_confirmation" />

            <x-slot:actions>
                <x-mary-button label="Сохранить" class="btn-primary" type="submit" />
            </x-slot:actions>
        </x-maryform>
    </x-mary-card>
</div>
