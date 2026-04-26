<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Computed;
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
    public bool $is_profile_public = true;
    public bool $show_email_on_profile = false;
    public bool $show_phone_on_profile = false;

    public function mount()
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();
        $this->fio = $user->fio;
        $this->nickname = $user->nickname;
        $this->role = $user->role == 'user'
            ? 'Участник'
            : ($user->role == 'partner' ? 'Партнёр' : ($user->role == 'judge' ? 'Судья' : 'Администратор'));
        $this->date_of_birth = $user->date_of_birth;
        $this->email = $user->email;
        $this->description = $user->description;
        $this->is_profile_public = (bool) $user->is_profile_public;
        $this->show_email_on_profile = (bool) $user->show_email_on_profile;
        $this->show_phone_on_profile = (bool) $user->show_phone_on_profile;
    }

    public function save(): void
    {
        $user = Auth::user();

        if (!$user) {
            $this->redirect('/login');
            return;
        }

        $emailChanged = $this->email !== $user->email;
        $isChangingPassword = $this->new_password !== '';
        $requiresPasswordConfirmation = $emailChanged || $isChangingPassword;

        $this->validate([
            'fio' => ['required', 'string', 'max:255', 'regex:/^[\p{L}\-]+(?:\s+[\p{L}\-]+){1,2}$/u'],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'description' => ['nullable', 'string', 'max:2000'],
            'is_profile_public' => ['boolean'],
            'show_email_on_profile' => ['boolean'],
            'show_phone_on_profile' => ['boolean'],
            'current_password' => [$requiresPasswordConfirmation ? 'required' : 'nullable', 'string'],
            'new_password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ], [
            'fio.required' => 'ФИО обязательно для заполнения.',
            'fio.regex' => 'Укажите ФИО в формате "Фамилия Имя" или "Фамилия Имя Отчество".',
            'date_of_birth.required' => 'Дата рождения обязательна.',
            'date_of_birth.date' => 'Введите корректную дату рождения.',
            'date_of_birth.before' => 'Дата рождения должна быть в прошлом.',
            'email.required' => 'Электронная почта обязательна.',
            'email.email' => 'Введите корректный email.',
            'email.unique' => 'Этот email уже используется.',
            'description.max' => 'Описание не должно превышать 2000 символов.',
            'current_password.required' => 'Для подтверждения введите текущий пароль.',
            'new_password.min' => 'Новый пароль должен содержать минимум 8 символов.',
            'new_password.confirmed' => 'Подтверждение нового пароля не совпадает.',
        ]);

        if ($requiresPasswordConfirmation && !Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', 'Текущий пароль указан неверно.');
            return;
        }

        $payload = [
            'fio' => $this->fio,
            'date_of_birth' => $this->date_of_birth,
            'email' => $this->email,
            'description' => $this->description,
            'is_profile_public' => $this->is_profile_public,
            'show_email_on_profile' => $this->show_email_on_profile,
            'show_phone_on_profile' => $this->show_phone_on_profile,
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

    #[Computed]
    public function certificates()
    {
        return Auth::user()?->certificates()->with('hackaton')->latest('issued_at')->get() ?? collect();
    }

    #[Computed]
    public function recentAnnouncements()
    {
        return Auth::user()?->notifications()->latest()->limit(5)->get() ?? collect();
    }
};
?>

<div class="mx-auto w-full max-w-6xl space-y-4">
    <x-marytoast />

    <div class="text-sm breadcrumbs">
        <ul>
            <li><a href="/">Главная</a></li>
            <li class="opacity-70">Профиль</li>
        </ul>
    </div>

    <x-mary-card title="Профиль" class="mx-auto w-full max-w-3xl card card-border bg-base-100">
        <x-maryform wire:submit="save">
            <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                <x-mary-input
                    label="ФИО"
                    wire:model="fio"
                    placeholder="Иванов Иван Иванович"
                    hint="Формат: Фамилия Имя или Фамилия Имя Отчество"
                />
                <x-mary-input label="Дата рождения" type="date" wire:model="date_of_birth" />
            </div>

            <x-mary-input label="Никнейм" :value="$nickname" readonly />
            <x-mary-input label="Роль" :value="$role" readonly />
            <x-mary-input label="Электронная почта" wire:model="email" placeholder="example@mail.com" />
            <div class="rounded-xl border border-base-300 p-3">
                <p class="text-sm font-medium">Подтверждение телефона</p>
                @if (auth()->user()?->phone_verified_at)
                    <p class="text-sm text-success">Телефон подтвержден: {{ auth()->user()->phone_verified_at->format('d.m.Y H:i') }}</p>
                @else
                    <p class="text-sm text-warning">Телефон пока не подтвержден.</p>
                    <a class="link link-primary text-sm" href="{{ route('phone.verify.notice') }}">Подтвердить через SMS</a>
                @endif
            </div>

            <x-marymarkdown wire:model="description" label="Описание" :config="$this->config" />
            <div class="rounded-xl border border-base-300 p-4 space-y-2">
                <p class="text-sm font-medium">Настройки публичного профиля</p>
                <x-marytoggle label="Профиль виден всем" wire:model="is_profile_public" />
                <x-marytoggle label="Показывать email в публичном профиле" wire:model="show_email_on_profile" />
                <x-marytoggle label="Показывать телефон в публичном профиле" wire:model="show_phone_on_profile" />
                <a class="link link-primary text-sm" href="{{ route('profile.public.show', ['user' => auth()->user()->nickname]) }}" target="_blank" rel="noopener">
                    Открыть публичный профиль
                </a>
            </div>

            <x-marypassword label="Текущий пароль (нужен только при смене почты или пароля)" wire:model="current_password" />
            <x-marypassword label="Новый пароль" wire:model="new_password" />
            <x-marypassword label="Подтверждение нового пароля" wire:model="new_password_confirmation" />

            <x-slot:actions>
                <x-mary-button label="Сохранить" class="btn-primary" type="submit" />
            </x-slot:actions>
        </x-maryform>
    </x-mary-card>

    <x-mary-card title="Мои сертификаты" class="mx-auto w-full max-w-3xl card card-border bg-base-100">
        @if($this->certificates->isEmpty())
            <p class="text-sm text-base-content/70">У вас пока нет загруженных сертификатов.</p>
        @else
            <div class="overflow-x-auto">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Хакатон</th>
                            <th>Сертификат</th>
                            <th>Дата</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($this->certificates as $certificate)
                            <tr>
                                <td>{{ $certificate->hackaton->title }}</td>
                                <td>{{ $certificate->title }}</td>
                                <td>{{ $certificate->issued_at?->format('d.m.Y') ?? '—' }}</td>
                                <td class="text-right">
                                    <a href="{{ route('certificates.download', $certificate) }}" class="btn btn-xs btn-outline">Скачать</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-mary-card>

    <x-mary-card title="Последние анонсы" class="mx-auto w-full max-w-3xl card card-border bg-base-100">
        @if($this->recentAnnouncements->isEmpty())
            <p class="text-sm text-base-content/70">Новых уведомлений пока нет.</p>
        @else
            <div class="space-y-2">
                @foreach($this->recentAnnouncements as $notification)
                    <div class="rounded-lg border border-base-300 p-3">
                        <p class="font-medium">{{ data_get($notification->data, 'title', 'Анонс') }}</p>
                        <p class="text-xs text-base-content/70">{{ $notification->created_at?->format('d.m.Y H:i') }}</p>
                        <a class="link link-primary text-sm" href="{{ data_get($notification->data, 'url', '/hackatons') }}">Открыть хакатон</a>
                    </div>
                @endforeach
            </div>
        @endif
    </x-mary-card>
</div>
