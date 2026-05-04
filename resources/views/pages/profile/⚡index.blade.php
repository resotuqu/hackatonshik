<?php

use App\Enums\ApplicationStatus;
use App\Models\TeamApplication;
use App\Services\ContactChangeService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Layout('layouts::app', ['title' => 'Профиль'])]
class extends Component {
    use \Mary\Traits\Toast, WithFileUploads;

    public array $config = [
        'toolbar' => ['heading', 'bold', 'italic', '|', 'preview'],
        'uploadImage' => false,
    ];

    public string $fio = '';
    public string $nickname = '';
    public string $role = '';
    public string $date_of_birth = '';
    public ?string $description = null;
    public string $current_password = '';
    public string $new_password = '';
    public string $new_password_confirmation = '';
    public bool $is_profile_public = true;
    public bool $show_email_on_profile = false;
    public bool $show_phone_on_profile = false;
    public $avatar = null;
    public ?string $avatar_path = null;

    public bool $phoneChangeModal = false;

    public string $phoneChangeStep = 'phone';

    public string $new_phone = '';

    public string $phone_email_code = '';

    public string $phone_call_code = '';

    public bool $emailChangeModal = false;

    public string $emailChangeStep = 'email';

    public string $new_email = '';

    public string $email_old_code = '';

    public string $email_new_code = '';

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
        $this->description = $user->description;
        $this->is_profile_public = (bool) $user->is_profile_public;
        $this->show_email_on_profile = (bool) $user->show_email_on_profile;
        $this->show_phone_on_profile = (bool) $user->show_phone_on_profile;
        $this->avatar_path = $user->avatar_path;
    }

    public function openPhoneChangeModal(): void
    {
        $this->resetErrorBag();
        $user = Auth::user();
        if (! $user) {
            return;
        }
        $state = app(ContactChangeService::class)->phoneChangeState($user);
        if ($state) {
            $this->new_phone = $state['new_phone'];
            $this->phoneChangeStep = $state['step'] === 1 ? 'email' : 'call';
        } else {
            $this->new_phone = '';
            $this->phoneChangeStep = 'phone';
        }
        $this->phone_email_code = '';
        $this->phone_call_code = '';
        $this->phoneChangeModal = true;
    }

    public function closePhoneChangeModal(): void
    {
        $user = Auth::user();
        if ($user) {
            app(ContactChangeService::class)->cancelPhoneChange($user);
        }
        $this->new_phone = '';
        $this->phone_email_code = '';
        $this->phone_call_code = '';
        $this->phoneChangeStep = 'phone';
        $this->phoneChangeModal = false;
    }

    public function sendPhoneChangeEmailCode(): void
    {
        $this->validate([
            'new_phone' => ['required', 'string', 'min:11', 'max:12'],
        ], [
            'new_phone.required' => 'Укажите новый номер.',
            'new_phone.min' => 'Номер укажите в формате 11–12 цифр, как при регистрации.',
            'new_phone.max' => 'Номер укажите в формате 11–12 цифр, как при регистрации.',
        ]);
        $user = Auth::user();
        if (! $user) {
            return;
        }
        app(ContactChangeService::class)->startPhoneChange($user, $this->new_phone);
        $this->phoneChangeStep = 'email';
        $this->phone_email_code = '';
        $this->success('Код подтверждения отправлен на вашу почту.', position: 'toast-center toast-top');
    }

    public function resendPhoneChangeEmailCode(): void
    {
        $user = Auth::user();
        if (! $user) {
            return;
        }
        app(ContactChangeService::class)->resendPhoneChangeEmailCode($user);
        $this->success('Код отправлен повторно.', position: 'toast-center toast-top');
    }

    public function confirmPhoneEmailCode(): void
    {
        $this->validate([
            'phone_email_code' => ['required', 'digits:6'],
        ], [
            'phone_email_code.required' => 'Введите код из письма.',
            'phone_email_code.digits' => 'Код из 6 цифр.',
        ]);
        $user = Auth::user();
        if (! $user) {
            return;
        }
        app(ContactChangeService::class)->verifyPhoneChangeEmailAndSendCall($user, $this->phone_email_code);
        $this->phoneChangeStep = 'call';
        $this->phone_call_code = '';
        $this->success('Сейчас поступит звонок на новый номер.', position: 'toast-center toast-top');
    }

    public function resendPhoneChangeCall(): void
    {
        $user = Auth::user();
        if (! $user) {
            return;
        }
        app(ContactChangeService::class)->resendPhoneChangeCall($user);
        $this->success('Звонок инициирован повторно.', position: 'toast-center toast-top');
    }

    public function confirmPhoneCallCode(): void
    {
        $this->validate([
            'phone_call_code' => ['required', 'digits:4'],
        ], [
            'phone_call_code.required' => 'Введите код из звонка.',
            'phone_call_code.digits' => 'Код из 4 цифр.',
        ]);
        $user = Auth::user();
        if (! $user) {
            return;
        }
        app(ContactChangeService::class)->completePhoneChange($user, $this->phone_call_code);
        auth()->user()?->refresh();
        $this->closePhoneChangeModal();
        $this->success('Номер телефона обновлён.', position: 'toast-center toast-top');
    }

    public function openEmailChangeModal(): void
    {
        $this->resetErrorBag();
        $user = Auth::user();
        if (! $user) {
            return;
        }
        $state = app(ContactChangeService::class)->emailChangeState($user);
        if ($state) {
            $this->new_email = $state['new_email'];
            $this->emailChangeStep = $state['step'] === 1 ? 'old' : 'new';
        } else {
            $this->new_email = '';
            $this->emailChangeStep = 'email';
        }
        $this->email_old_code = '';
        $this->email_new_code = '';
        $this->emailChangeModal = true;
    }

    public function closeEmailChangeModal(): void
    {
        $user = Auth::user();
        if ($user) {
            app(ContactChangeService::class)->cancelEmailChange($user);
        }
        $this->new_email = '';
        $this->email_old_code = '';
        $this->email_new_code = '';
        $this->emailChangeStep = 'email';
        $this->emailChangeModal = false;
    }

    public function sendEmailChangeFirstCode(): void
    {
        $this->validate([
            'new_email' => ['required', 'email', 'max:255'],
        ], [
            'new_email.required' => 'Укажите новый email.',
            'new_email.email' => 'Введите корректный email.',
        ]);
        $user = Auth::user();
        if (! $user) {
            return;
        }
        app(ContactChangeService::class)->startEmailChange($user, $this->new_email);
        $this->emailChangeStep = 'old';
        $this->email_old_code = '';
        $this->success('Код отправлен на текущую почту.', position: 'toast-center toast-top');
    }

    public function resendEmailChangeOldCode(): void
    {
        $user = Auth::user();
        if (! $user) {
            return;
        }
        app(ContactChangeService::class)->resendEmailChangeOldCode($user);
        $this->success('Код отправлен повторно.', position: 'toast-center toast-top');
    }

    public function confirmEmailOldCode(): void
    {
        $this->validate([
            'email_old_code' => ['required', 'digits:6'],
        ], [
            'email_old_code.required' => 'Введите код из письма.',
            'email_old_code.digits' => 'Код из 6 цифр.',
        ]);
        $user = Auth::user();
        if (! $user) {
            return;
        }
        app(ContactChangeService::class)->verifyEmailChangeOldAndSendToNew($user, $this->email_old_code);
        $this->emailChangeStep = 'new';
        $this->email_new_code = '';
        $this->success('Код отправлен на новый адрес.', position: 'toast-center toast-top');
    }

    public function resendEmailChangeNewCode(): void
    {
        $user = Auth::user();
        if (! $user) {
            return;
        }
        app(ContactChangeService::class)->resendEmailChangeNewCode($user);
        $this->success('Код отправлен повторно.', position: 'toast-center toast-top');
    }

    public function completeEmailChange(): void
    {
        $this->validate([
            'email_new_code' => ['required', 'digits:6'],
        ], [
            'email_new_code.required' => 'Введите код из письма.',
            'email_new_code.digits' => 'Код из 6 цифр.',
        ]);
        $user = Auth::user();
        if (! $user) {
            return;
        }
        app(ContactChangeService::class)->completeEmailChange($user, $this->email_new_code);
        auth()->user()?->refresh();
        $this->new_email = '';
        $this->email_old_code = '';
        $this->email_new_code = '';
        $this->emailChangeStep = 'email';
        $this->emailChangeModal = false;
        $this->success('Адрес электронной почты обновлён.', position: 'toast-center toast-top');
    }

    public function save(): void
    {
        $user = Auth::user();

        if (!$user) {
            $this->redirect('/login');
            return;
        }

        $isChangingPassword = $this->new_password !== '';
        $requiresPasswordConfirmation = $isChangingPassword;

        $this->validate([
            'fio' => ['required', 'string', 'max:255', 'regex:/^[\p{L}\-]+(?:\s+[\p{L}\-]+){1,2}$/u'],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'description' => ['nullable', 'string', 'max:2000'],
            'is_profile_public' => ['boolean'],
            'show_email_on_profile' => ['boolean'],
            'show_phone_on_profile' => ['boolean'],
            'current_password' => [$requiresPasswordConfirmation ? 'required' : 'nullable', 'string'],
            'new_password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'avatar' => ['nullable', 'image', 'max:3072'],
        ], [
            'fio.required' => 'ФИО обязательно для заполнения.',
            'fio.regex' => 'Укажите ФИО в формате "Фамилия Имя" или "Фамилия Имя Отчество".',
            'date_of_birth.required' => 'Дата рождения обязательна.',
            'date_of_birth.date' => 'Введите корректную дату рождения.',
            'date_of_birth.before' => 'Дата рождения должна быть в прошлом.',
            'description.max' => 'Описание не должно превышать 2000 символов.',
            'current_password.required' => 'Для смены пароля введите текущий пароль.',
            'new_password.min' => 'Новый пароль должен содержать минимум 8 символов.',
            'new_password.confirmed' => 'Подтверждение нового пароля не совпадает.',
            'avatar.image' => 'Аватар должен быть изображением.',
            'avatar.max' => 'Размер аватара не должен превышать 3 МБ.',
        ]);

        if ($requiresPasswordConfirmation && !Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', 'Текущий пароль указан неверно.');
            return;
        }

        $payload = [
            'fio' => $this->fio,
            'date_of_birth' => $this->date_of_birth,
            'description' => $this->description,
            'is_profile_public' => $this->is_profile_public,
            'show_email_on_profile' => $this->show_email_on_profile,
            'show_phone_on_profile' => $this->show_phone_on_profile,
        ];

        if ($this->new_password !== '') {
            $payload['password'] = $this->new_password;
        }

        if ($this->avatar) {
            $payload['avatar_path'] = $this->avatar->store('avatars', 'public');
            $this->avatar_path = $payload['avatar_path'];
        }

        $user->update($payload);

        $this->current_password = '';
        $this->new_password = '';
        $this->new_password_confirmation = '';
        $this->avatar = null;

        $this->success('Профиль успешно обновлён.', position: 'toast-center toast-top');
    }

    #[Computed]
    public function profileCompletenessPercent(): int
    {
        $user = Auth::user();
        if (! $user) {
            return 0;
        }

        $checks = [
            filled($user->fio),
            filled($user->date_of_birth),
            filled($user->avatar_path),
            filled($user->description),
            ! is_null($user->email_verified_at),
            ! is_null($user->phone_verified_at),
        ];

        return (int) round((array_sum(array_map('intval', $checks)) / count($checks)) * 100);
    }

    #[Computed]
    public function joinedTeamsCount(): int
    {
        $user = Auth::user();
        if (! $user) {
            return 0;
        }

        return TeamApplication::query()
            ->where('team_applications.user_id', $user->id)
            ->where('team_applications.status', ApplicationStatus::ACCEPTED)
            ->join('team_roles', 'team_applications.team_role_id', '=', 'team_roles.id')
            ->distinct('team_roles.team_id')
            ->count('team_roles.team_id');
    }

    #[Computed]
    public function joinedHackatonsCount(): int
    {
        $user = Auth::user();
        if (! $user) {
            return 0;
        }

        return TeamApplication::query()
            ->where('team_applications.user_id', $user->id)
            ->where('team_applications.status', ApplicationStatus::ACCEPTED)
            ->join('team_roles', 'team_applications.team_role_id', '=', 'team_roles.id')
            ->join('teams', 'team_roles.team_id', '=', 'teams.id')
            ->whereNotNull('teams.hackaton_id')
            ->distinct('teams.hackaton_id')
            ->count('teams.hackaton_id');
    }
};
?>

@php
    $authUser = auth()->user();
    $avatarUrl = $avatar
        ? $avatar->temporaryUrl()
        : ($avatar_path
            ? asset('storage/'.$avatar_path)
            : 'https://ui-avatars.com/api/?name='.urlencode($authUser->fio).'&background=random');
    $completeness = $this->profileCompletenessPercent;
    $tips = [];
    if (! filled($authUser->fio)) { $tips[] = 'Укажите ФИО'; }
    if (! filled($authUser->date_of_birth)) { $tips[] = 'Заполните дату рождения'; }
    if (! filled($authUser->avatar_path)) { $tips[] = 'Загрузите аватар'; }
    if (! filled($authUser->description)) { $tips[] = 'Добавьте описание о себе'; }
    if (is_null($authUser->email_verified_at)) { $tips[] = 'Подтвердите электронную почту'; }
    if (is_null($authUser->phone_verified_at)) { $tips[] = 'Подтвердите номер телефона'; }
@endphp

<div class="mx-auto w-full max-w-6xl space-y-6">
    <x-marytoast />

    <nav class="text-sm breadcrumbs">
        <ul>
            <li><a href="/">Главная</a></li>
            <li class="opacity-70">Профиль</li>
        </ul>
    </nav>

    <div class="tabs tabs-boxed w-full overflow-x-auto">
        <a class="tab tab-active">Личные данные</a>
        <a class="tab" href="/profile/teams">Мои команды</a>
        <a class="tab" href="/profile/hackatons">Мои хакатоны</a>
        <a class="tab" href="/profile/certificates">Сертификаты</a>
    </div>

    {{-- HERO --}}
    <section class="relative overflow-hidden rounded-3xl border border-base-300 bg-linear-to-br from-base-100 via-base-100 to-primary/15 p-6 shadow-sm lg:p-8">
        <div class="pointer-events-none absolute -top-20 -right-16 h-56 w-56 rounded-full bg-secondary/10 blur-3xl"></div>
        <div class="pointer-events-none absolute -bottom-24 -left-16 h-64 w-64 rounded-full bg-primary/10 blur-3xl"></div>

        <div class="relative flex flex-col gap-6 md:flex-row md:items-start md:justify-between">
            <div class="flex flex-col items-start gap-5 sm:flex-row sm:items-center">
                <div class="avatar">
                    <div class="w-32 rounded-full ring-2 ring-secondary/40 ring-offset-2 ring-offset-base-100 sm:w-36">
                        <img src="{{ $avatarUrl }}" alt="Аватар пользователя" />
                    </div>
                </div>
                <div class="space-y-2">
                    <span class="badge badge-primary badge-outline">{{ $role }}</span>
                    <h1 class="font-display text-3xl font-semibold tracking-tight lg:text-4xl">
                        {{ $fio ?: 'Без имени' }}
                    </h1>
                    <p class="text-base text-base-content/70">{{ '@'.$nickname }}</p>
                    <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-sm text-base-content/75">
                        <span class="inline-flex items-center gap-1.5">
                            <span class="font-semibold text-secondary">{{ $this->joinedHackatonsCount }}</span>
                            хакатонов
                        </span>
                        <span class="text-base-content/30">·</span>
                        <span class="inline-flex items-center gap-1.5">
                            <span class="font-semibold text-secondary">{{ $this->joinedTeamsCount }}</span>
                            команд
                        </span>
                    </div>
                </div>
            </div>

            <div class="flex flex-col items-stretch gap-3 sm:flex-row sm:items-center sm:gap-5 md:flex-col md:items-end">
                <div class="radial-progress text-secondary" style="--value:{{ $completeness }};--size:5rem;--thickness:6px" role="progressbar" aria-valuenow="{{ $completeness }}" aria-valuemin="0" aria-valuemax="100">
                    <span class="text-sm font-semibold text-base-content">{{ $completeness }}%</span>
                </div>
                <a href="{{ route('profile.public.show', ['user' => $authUser->nickname]) }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline btn-secondary">
                    <x-app-icon icon="heroicons:eye" class="h-4 w-4" />
                    Посмотреть как другие
                </a>
            </div>
        </div>

        <div class="relative mt-6 space-y-2">
            <div class="flex items-center justify-between text-sm">
                <span class="text-base-content/70">Заполненность профиля</span>
                <span class="font-medium text-secondary">{{ $completeness }}%</span>
            </div>
            <progress class="progress progress-secondary w-full" value="{{ $completeness }}" max="100"></progress>
        </div>
    </section>

    {{-- 2-col grid --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2">
            <x-maryform wire:submit="save" class="space-y-6">
                {{-- Avatar card --}}
                <section class="card border border-base-300 bg-base-100">
                    <div class="card-body gap-4">
                        <h2 class="card-title text-base">
                            <x-app-icon icon="heroicons:photo" class="h-5 w-5 text-primary" />
                            Аватар профиля
                        </h2>
                        <div class="flex flex-col items-start gap-4 rounded-2xl border border-dashed border-base-300 p-4 transition hover:border-primary/50 sm:flex-row sm:items-center">
                            <div class="avatar">
                                <div class="w-24 rounded-full ring-1 ring-base-300">
                                    <img src="{{ $avatarUrl }}" alt="Текущий аватар" />
                                </div>
                            </div>
                            <div class="w-full flex-1">
                                <x-mary-input type="file" wire:model="avatar" accept="image/*" hint="Изображение до 3 МБ" />
                            </div>
                        </div>
                    </div>
                </section>

                {{-- Personal data --}}
                <section class="card border border-base-300 bg-base-100">
                    <div class="card-body gap-4">
                        <h2 class="card-title text-base">
                            <x-app-icon icon="heroicons:identification" class="h-5 w-5 text-primary" />
                            Личные данные
                        </h2>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <x-mary-input
                                label="ФИО"
                                wire:model="fio"
                                placeholder="Иванов Иван Иванович"
                                hint="Формат: Фамилия Имя или Фамилия Имя Отчество"
                            />
                            <x-mary-input label="Дата рождения" type="date" wire:model="date_of_birth" />
                            <x-mary-input label="Никнейм" :value="$nickname" readonly />
                            <x-mary-input label="Роль" :value="$role" readonly />
                        </div>
                    </div>
                </section>

                {{-- Contacts --}}
                <section class="card border border-base-300 bg-base-100">
                    <div class="card-body gap-4">
                        <h2 class="card-title text-base">
                            <x-app-icon icon="heroicons:envelope" class="h-5 w-5 text-primary" />
                            Контакты
                        </h2>

                        <div class="space-y-5">
                            <div class="grid grid-cols-1 gap-3 md:grid-cols-[1fr_auto] md:items-end">
                                <div>
                                    <x-mary-input label="Электронная почта" :value="$authUser->email" readonly />
                                    <div class="mt-2">
                                        @if ($authUser->email_verified_at)
                                            <span class="badge badge-success badge-sm gap-1">
                                                <x-app-icon icon="heroicons:check-badge" class="h-3.5 w-3.5" />
                                                Подтверждён
                                            </span>
                                        @else
                                            <span class="badge badge-warning badge-sm gap-1">
                                                <x-app-icon icon="heroicons:exclamation-triangle" class="h-3.5 w-3.5" />
                                                Не подтверждён
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <x-mary-button label="Изменить email" class="btn-outline btn-primary" type="button" wire:click="openEmailChangeModal" />
                            </div>

                            <div class="grid grid-cols-1 gap-3 md:grid-cols-[1fr_auto] md:items-end">
                                <div>
                                    <x-mary-input label="Телефон" :value="$authUser->phone" readonly />
                                    <div class="mt-2">
                                        @if ($authUser->phone_verified_at)
                                            <span class="badge badge-success badge-sm gap-1">
                                                <x-app-icon icon="heroicons:check-badge" class="h-3.5 w-3.5" />
                                                Подтверждён
                                            </span>
                                        @else
                                            <span class="badge badge-warning badge-sm gap-1">
                                                <x-app-icon icon="heroicons:exclamation-triangle" class="h-3.5 w-3.5" />
                                                Не подтверждён
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <x-mary-button label="Изменить номер" class="btn-outline btn-primary" type="button" wire:click="openPhoneChangeModal" />
                            </div>
                        </div>
                    </div>
                </section>

                {{-- Description --}}
                <section class="card border border-base-300 bg-base-100">
                    <div class="card-body gap-4">
                        <h2 class="card-title text-base">
                            <x-app-icon icon="heroicons:document-text" class="h-5 w-5 text-primary" />
                            О себе
                        </h2>
                        <p class="text-sm text-base-content/70">
                            Расскажите о навыках, интересах и опыте — этот текст увидят на вашем публичном профиле.
                        </p>
                        <div class="rounded-2xl border border-base-300 bg-base-200/40 p-1">
                            <x-marymarkdown wire:model="description" :config="$this->config" />
                        </div>
                    </div>
                </section>

                {{-- Privacy --}}
                <section class="card border border-base-300 bg-base-100">
                    <div class="card-body gap-4">
                        <h2 class="card-title text-base">
                            <x-app-icon icon="heroicons:eye" class="h-5 w-5 text-primary" />
                            Публичный профиль
                        </h2>
                        <div class="space-y-3">
                            <x-marytoggle label="Профиль виден всем" wire:model="is_profile_public" />
                            <x-marytoggle label="Показывать email в публичном профиле" wire:model="show_email_on_profile" />
                            <x-marytoggle label="Показывать телефон в публичном профиле" wire:model="show_phone_on_profile" />
                        </div>
                        <div class="rounded-xl bg-primary/10 p-4 ring-1 ring-primary/20">
                            <p class="font-medium text-primary">Живое превью приватности</p>
                            <p class="mt-1 text-sm text-base-content/80">
                                Профиль: <span class="font-medium">{{ $is_profile_public ? 'публичный' : 'скрытый' }}</span>,
                                email: <span class="font-medium">{{ $show_email_on_profile ? 'виден' : 'скрыт' }}</span>,
                                телефон: <span class="font-medium">{{ $show_phone_on_profile ? 'виден' : 'скрыт' }}</span>.
                            </p>
                        </div>
                    </div>
                </section>

                {{-- Security --}}
                <section class="card border border-base-300 bg-base-100">
                    <div class="card-body gap-4">
                        <h2 class="card-title text-base">
                            <x-app-icon icon="heroicons:lock-closed" class="h-5 w-5 text-primary" />
                            Безопасность
                        </h2>
                        <p class="text-sm text-base-content/70">
                            Заполните только при смене пароля. В остальных случаях оставьте поля пустыми.
                        </p>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div class="md:col-span-2">
                                <x-marypassword label="Текущий пароль" wire:model="current_password" />
                            </div>
                            <x-marypassword label="Новый пароль" wire:model="new_password" />
                            <x-marypassword label="Подтверждение нового пароля" wire:model="new_password_confirmation" />
                        </div>
                    </div>
                </section>

                {{-- Desktop save --}}
                <div class="hidden justify-end gap-3 pt-2 lg:flex">
                    <x-mary-button label="Сохранить изменения" class="btn-primary btn-lg" type="submit" />
                </div>

                {{-- Mobile sticky save --}}
                <div class="sticky bottom-0 z-30 -mx-4 flex gap-2 border-t border-base-300 bg-base-100/95 px-4 py-3 backdrop-blur sm:-mx-6 sm:px-6 lg:hidden">
                    <x-mary-button label="Сохранить изменения" class="btn-primary w-full" type="submit" />
                </div>
            </x-maryform>
        </div>

        {{-- RIGHT sidebar --}}
        <aside class="space-y-6 lg:sticky lg:top-6 lg:self-start">
            {{-- Verification --}}
            <section class="card border border-base-300 bg-base-100">
                <div class="card-body gap-3">
                    <h2 class="card-title text-base">
                        <x-app-icon icon="heroicons:shield-check" class="h-5 w-5 text-secondary" />
                        Верификация
                    </h2>

                    <div class="flex items-start gap-3 rounded-xl border border-base-300 p-3">
                        @if ($authUser->email_verified_at)
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-success/15 text-success">
                                <x-app-icon icon="heroicons:check-circle" class="h-5 w-5" />
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium">Email подтверждён</p>
                                <p class="truncate text-xs text-base-content/70">{{ $authUser->email }}</p>
                            </div>
                        @else
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-warning/15 text-warning">
                                <x-app-icon icon="heroicons:exclamation-triangle" class="h-5 w-5" />
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium">Email не подтверждён</p>
                                <a href="{{ route('verification.notice') }}" class="link link-primary text-xs">Подтвердить почту</a>
                            </div>
                        @endif
                    </div>

                    <div class="flex items-start gap-3 rounded-xl border border-base-300 p-3">
                        @if ($authUser->phone_verified_at)
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-success/15 text-success">
                                <x-app-icon icon="heroicons:check-circle" class="h-5 w-5" />
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium">Телефон подтверждён</p>
                                <p class="truncate text-xs text-base-content/70">{{ $authUser->phone }}</p>
                            </div>
                        @else
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-warning/15 text-warning">
                                <x-app-icon icon="heroicons:exclamation-triangle" class="h-5 w-5" />
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium">Телефон не подтверждён</p>
                                <a href="{{ route('phone.verify.notice') }}" class="link link-primary text-xs">Подтвердить номер</a>
                            </div>
                        @endif
                    </div>
                </div>
            </section>

            {{-- Public preview --}}
            <section class="card border border-base-300 bg-linear-to-br from-base-100 to-primary/5">
                <div class="card-body gap-4">
                    <h2 class="card-title text-base">
                        <x-app-icon icon="heroicons:user-circle" class="h-5 w-5 text-secondary" />
                        Как видят другие
                    </h2>
                    <div class="rounded-2xl border border-base-300 bg-base-100 p-4">
                        <div class="flex items-center gap-3">
                            <div class="avatar">
                                <div class="w-14 rounded-full ring-1 ring-secondary/30">
                                    <img src="{{ $avatarUrl }}" alt="Превью аватара" />
                                </div>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="truncate font-semibold">{{ $fio ?: '—' }}</p>
                                <p class="truncate text-xs text-base-content/70">{{ '@'.$nickname }}</p>
                                <span class="badge badge-primary badge-outline badge-sm mt-1">{{ $role }}</span>
                            </div>
                        </div>
                        <p class="mt-3 line-clamp-3 text-xs text-base-content/75">
                            {{ $description ?: 'Описание пока не заполнено.' }}
                        </p>
                    </div>
                    <a href="{{ route('profile.public.show', ['user' => $authUser->nickname]) }}" target="_blank" rel="noopener" class="btn btn-block btn-sm btn-outline">
                        <x-app-icon icon="heroicons:arrow-top-right-on-square" class="h-4 w-4" />
                        Открыть публичную страницу
                    </a>
                </div>
            </section>

            {{-- Tips --}}
            @if (! empty($tips))
                <section class="card border border-secondary/20 bg-secondary/5">
                    <div class="card-body gap-3">
                        <h2 class="card-title text-base">
                            <x-app-icon icon="heroicons:sparkles" class="h-5 w-5 text-secondary" />
                            Что добавить
                        </h2>
                        <ul class="space-y-2 text-sm">
                            @foreach ($tips as $tip)
                                <li class="flex items-start gap-2">
                                    <x-app-icon icon="heroicons:plus-circle" class="mt-0.5 h-4 w-4 shrink-0 text-secondary" />
                                    <span>{{ $tip }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </section>
            @endif
        </aside>
    </div>

    <x-mary-modal wire:model="phoneChangeModal" title="Смена номера телефона" class="backdrop-blur">
        <div class="space-y-4">
            @if ($phoneChangeStep === 'phone')
                <p class="text-sm text-base-content/80">Сначала придёт код на вашу текущую почту, затем мы позвоним на новый номер.</p>
                <x-mary-input label="Новый номер телефона" wire:model="new_phone" hint="11–12 символов, как при регистрации" />
            @elseif ($phoneChangeStep === 'email')
                <p class="text-sm text-base-content/80">Введите код из письма, отправленного на <span class="font-medium">{{ auth()->user()->email }}</span>.</p>
                <x-mary-input label="Код из почты" wire:model="phone_email_code" maxlength="6" />
                <div class="flex flex-wrap gap-2">
                    <x-mary-button label="Подтвердить" class="btn-primary" type="button" wire:click="confirmPhoneEmailCode" />
                    <x-mary-button label="Отправить код снова" class="btn-ghost" type="button" wire:click="resendPhoneChangeEmailCode" />
                </div>
            @elseif ($phoneChangeStep === 'call')
                <p class="text-sm text-base-content/80">Ответьте на звонок и введите 4 цифры, которые проговорит ассистент. Звонок поступит на <span class="font-medium">{{ $new_phone }}</span>.</p>
                <x-mary-input label="Код из звонка" wire:model="phone_call_code" maxlength="4" />
                <div class="flex flex-wrap gap-2">
                    <x-mary-button label="Подтвердить" class="btn-primary" type="button" wire:click="confirmPhoneCallCode" />
                    <x-mary-button label="Позвонить снова" class="btn-ghost" type="button" wire:click="resendPhoneChangeCall" />
                </div>
            @endif

            @if ($phoneChangeStep === 'phone')
                <div class="flex flex-wrap gap-2 justify-end">
                    <x-mary-button label="Отмена" class="btn-ghost" type="button" wire:click="closePhoneChangeModal" />
                    <x-mary-button label="Отправить код на почту" class="btn-primary" type="button" wire:click="sendPhoneChangeEmailCode" />
                </div>
            @else
                <div class="flex justify-end">
                    <x-mary-button label="Отмена" class="btn-ghost" type="button" wire:click="closePhoneChangeModal" />
                </div>
            @endif
        </div>
    </x-mary-modal>

    <x-mary-modal wire:model="emailChangeModal" title="Смена электронной почты" class="backdrop-blur">
        <div class="space-y-4">
            @if ($emailChangeStep === 'email')
                <p class="text-sm text-base-content/80">Сначала придёт код на текущую почту, затем — на новый адрес.</p>
                <x-mary-input label="Новый email" wire:model="new_email" type="email" />
                <div class="flex flex-wrap gap-2 justify-end">
                    <x-mary-button label="Отмена" class="btn-ghost" type="button" wire:click="closeEmailChangeModal" />
                    <x-mary-button label="Отправить код на текущую почту" class="btn-primary" type="button" wire:click="sendEmailChangeFirstCode" />
                </div>
            @elseif ($emailChangeStep === 'old')
                <p class="text-sm text-base-content/80">Введите код из письма на <span class="font-medium">{{ auth()->user()->email }}</span>.</p>
                <x-mary-input label="Код" wire:model="email_old_code" maxlength="6" />
                <div class="flex flex-wrap gap-2">
                    <x-mary-button label="Далее" class="btn-primary" type="button" wire:click="confirmEmailOldCode" />
                    <x-mary-button label="Отправить код снова" class="btn-ghost" type="button" wire:click="resendEmailChangeOldCode" />
                </div>
                <div class="flex justify-end">
                    <x-mary-button label="Отмена" class="btn-ghost" type="button" wire:click="closeEmailChangeModal" />
                </div>
            @elseif ($emailChangeStep === 'new')
                <p class="text-sm text-base-content/80">Введите код из письма на новый адрес <span class="font-medium">{{ $new_email }}</span>.</p>
                <x-mary-input label="Код" wire:model="email_new_code" maxlength="6" />
                <div class="flex flex-wrap gap-2">
                    <x-mary-button label="Подтвердить смену" class="btn-primary" type="button" wire:click="completeEmailChange" />
                    <x-mary-button label="Отправить код снова" class="btn-ghost" type="button" wire:click="resendEmailChangeNewCode" />
                </div>
                <div class="flex justify-end">
                    <x-mary-button label="Отмена" class="btn-ghost" type="button" wire:click="closeEmailChangeModal" />
                </div>
            @endif
        </div>
    </x-mary-modal>

</div>
