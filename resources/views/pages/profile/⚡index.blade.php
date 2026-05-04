<?php

use App\Enums\ApplicationStatus;
use App\Models\TeamApplication;
use App\Services\ContactChangeService;
use App\Support\PresetAvatar;
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
        'toolbar' => ['heading', 'bold', 'italic', '|', 'preview', 'side-by-side'],
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

    public ?string $selected_preset_path = null;

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

    public bool $personalEditModal = false;

    /** @var ''|'fio'|'date_of_birth' */
    public string $personalEditField = '';

    public string $personalDraft = '';

    public bool $passwordChangeModal = false;

    private function normalizedProfileDescription(mixed $raw): ?string
    {
        if (! is_string($raw) || trim($raw) === '') {
            return null;
        }

        return str_replace("\r\n", "\n", $raw);
    }

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
        $this->description = $this->normalizedProfileDescription($user->description);
        $this->is_profile_public = (bool) $user->is_profile_public;
        $this->show_email_on_profile = (bool) $user->show_email_on_profile;
        $this->show_phone_on_profile = (bool) $user->show_phone_on_profile;
        $this->avatar_path = $user->avatar_path;
        if ($user->avatar_path && PresetAvatar::isAllowedPath($user->avatar_path)) {
            $this->selected_preset_path = $user->avatar_path;
        }
    }

    public function selectPreset(string $path): void
    {
        if (! PresetAvatar::isAllowedPath($path)) {
            return;
        }
        $user = Auth::user();
        if (! $user) {
            return;
        }
        $this->avatar = null;
        $this->resetErrorBag('avatar');
        $this->selected_preset_path = $path;
        $dbPath = PresetAvatar::storagePathForDb($path);
        $user->update(['avatar_path' => $dbPath]);
        $this->avatar_path = $dbPath;
        $this->success('Аватар обновлён.', position: 'toast-center toast-top');
    }

    public function updatedAvatar(): void
    {
        $this->selected_preset_path = null;
        if (! $this->avatar) {
            return;
        }
        $this->persistUploadedAvatar();
    }

    public function persistUploadedAvatar(): void
    {
        $user = Auth::user();
        if (! $user || ! $this->avatar) {
            return;
        }

        $this->validate([
            'avatar' => ['required', 'image', 'max:3072'],
        ], [
            'avatar.image' => 'Аватар должен быть изображением.',
            'avatar.max' => 'Размер аватара не должен превышать 3 МБ.',
        ]);

        $stored = $this->avatar->store('avatars', 'public');
        $user->update(['avatar_path' => $stored]);
        $this->avatar_path = $stored;
        $this->avatar = null;
        $this->success('Аватар обновлён.', position: 'toast-center toast-top');
    }

    public function openPersonalEdit(string $field): void
    {
        if ($field !== 'fio' && $field !== 'date_of_birth') {
            return;
        }
        $this->resetErrorBag();
        $this->personalEditField = $field;
        $this->personalDraft = $field === 'fio' ? $this->fio : $this->date_of_birth;
        $this->personalEditModal = true;
    }

    public function closePersonalEdit(): void
    {
        $this->personalEditModal = false;
        $this->personalEditField = '';
        $this->personalDraft = '';
        $this->resetErrorBag();
    }

    public function savePersonalFromModal(): void
    {
        if ($this->personalEditField === 'fio') {
            $this->validate([
                'personalDraft' => ['required', 'string', 'max:255', 'regex:/^[\p{L}\-]+(?:\s+[\p{L}\-]+){1,2}$/u'],
            ], [
                'personalDraft.required' => 'ФИО обязательно для заполнения.',
                'personalDraft.regex' => 'Укажите ФИО в формате "Фамилия Имя" или "Фамилия Имя Отчество".',
            ]);
            $user = Auth::user();
            if (! $user) {
                return;
            }
            $user->update(['fio' => $this->personalDraft]);
            $this->fio = $this->personalDraft;
        } elseif ($this->personalEditField === 'date_of_birth') {
            $this->validate([
                'personalDraft' => ['required', 'date', 'before:today'],
            ], [
                'personalDraft.required' => 'Дата рождения обязательна.',
                'personalDraft.date' => 'Введите корректную дату рождения.',
                'personalDraft.before' => 'Дата рождения должна быть в прошлом.',
            ]);
            $user = Auth::user();
            if (! $user) {
                return;
            }
            $user->update(['date_of_birth' => $this->personalDraft]);
            $this->date_of_birth = $this->personalDraft;
        } else {
            return;
        }

        $this->closePersonalEdit();
        $this->success('Данные сохранены.', position: 'toast-center toast-top');
    }

    public function persistDescription(): void
    {
        $user = Auth::user();
        if (! $user) {
            return;
        }
        $this->validate([
            'description' => ['nullable', 'string', 'max:2000'],
        ], [
            'description.max' => 'Описание не должно превышать 2000 символов.',
        ]);

        $normalized = $this->normalizedProfileDescription($this->description);

        $this->description = $normalized;
        $user->update(['description' => $normalized]);
    }

    public function updatedDescription(): void
    {
        $this->persistDescription();
        $this->skipRender();
    }

    public function persistPrivacyToggles(): void
    {
        $user = Auth::user();
        if (! $user) {
            return;
        }
        $user->update([
            'is_profile_public' => $this->is_profile_public,
            'show_email_on_profile' => $this->show_email_on_profile,
            'show_phone_on_profile' => $this->show_phone_on_profile,
        ]);
    }

    public function updatedIsProfilePublic(): void
    {
        $this->persistPrivacyToggles();
    }

    public function updatedShowEmailOnProfile(): void
    {
        $this->persistPrivacyToggles();
    }

    public function updatedShowPhoneOnProfile(): void
    {
        $this->persistPrivacyToggles();
    }

    public function openPasswordChangeModal(): void
    {
        $this->resetErrorBag();
        $this->current_password = '';
        $this->new_password = '';
        $this->new_password_confirmation = '';
        $this->passwordChangeModal = true;
    }

    public function closePasswordChangeModal(): void
    {
        $this->passwordChangeModal = false;
        $this->current_password = '';
        $this->new_password = '';
        $this->new_password_confirmation = '';
        $this->resetErrorBag();
    }

    public function savePasswordFromModal(): void
    {
        $user = Auth::user();
        if (! $user) {
            return;
        }

        $this->validate([
            'current_password' => ['required', 'string'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'current_password.required' => 'Введите текущий пароль.',
            'new_password.required' => 'Введите новый пароль.',
            'new_password.min' => 'Новый пароль должен содержать минимум 8 символов.',
            'new_password.confirmed' => 'Подтверждение нового пароля не совпадает.',
        ]);

        if (! Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', 'Текущий пароль указан неверно.');

            return;
        }

        $user->update(['password' => $this->new_password]);
        $this->closePasswordChangeModal();
        $this->success('Пароль обновлён.', position: 'toast-center toast-top');
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

    #[Computed]
    public function presetAvatarPacks(): array
    {
        return PresetAvatar::activePacksWithPresets();
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
        : ($selected_preset_path
            ? asset('storage/'.$selected_preset_path)
            : ($avatar_path
                ? asset('storage/'.$avatar_path)
                : 'https://ui-avatars.com/api/?name='.urlencode($authUser->fio).'&background=random'));
    $completeness = $this->profileCompletenessPercent;
    $tips = [];
    if (! filled($authUser->fio)) { $tips[] = 'Укажите ФИО'; }
    if (! filled($authUser->date_of_birth)) { $tips[] = 'Заполните дату рождения'; }
    if (! filled($authUser->avatar_path)) { $tips[] = 'Добавьте аватар'; }
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
            <div class="space-y-6">
                {{-- Avatar card --}}
                <section class="card border border-base-300 bg-base-100">
                    <div class="card-body gap-4">
                        <h2 class="card-title text-base">
                            <x-app-icon icon="heroicons:photo" class="h-5 w-5 text-primary" />
                            Аватар профиля
                        </h2>
                        <p class="text-sm text-base-content/70">Выберите готовый аватар (по пакам) или загрузите своё изображение.</p>
                        @if (! empty($this->presetAvatarPacks))
                            <div class="space-y-6">
                                @foreach ($this->presetAvatarPacks as $pack)
                                    <div>
                                        <h3 class="mb-2 text-sm font-semibold text-base-content/80">{{ $pack['name'] }}</h3>
                                        <div class="grid grid-cols-3 gap-3 sm:grid-cols-6" role="list">
                                            @foreach ($pack['presets'] as $preset)
                                                @php
                                                    $pPath = $preset['path'];
                                                    $isActive = $selected_preset_path === $pPath
                                                        || ($selected_preset_path === null && $avatar_path === $pPath);
                                                @endphp
                                                <button
                                                    type="button"
                                                    wire:click="selectPreset({{ json_encode($pPath) }})"
                                                    class="group relative aspect-square overflow-hidden rounded-2xl border-2 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-primary {{ $isActive ? 'border-primary ring-2 ring-primary/30' : 'border-base-300 hover:border-primary/50' }}"
                                                    title="Аватар"
                                                    aria-pressed="{{ $isActive ? 'true' : 'false' }}"
                                                >
                                                    <img
                                                        src="{{ $preset['url'] }}"
                                                        alt=""
                                                        class="h-full w-full object-cover"
                                                        loading="lazy"
                                                    />
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="flex items-center gap-3 py-1">
                                <span class="h-px flex-1 bg-base-300"></span>
                                <span class="text-xs font-medium uppercase tracking-wide text-base-content/50">или файл</span>
                                <span class="h-px flex-1 bg-base-300"></span>
                            </div>
                        @endif
                        <div class="flex flex-col items-start gap-4 rounded-2xl border border-dashed border-base-300 p-4 transition hover:border-primary/50 sm:flex-row sm:items-center">
                            <div class="avatar">
                                <div class="w-24 rounded-full ring-1 ring-base-300">
                                    <img src="{{ $avatarUrl }}" alt="Текущий аватар" />
                                </div>
                            </div>
                            <div class="w-full flex-1">
                                <x-avatar-cropper-modal property="avatar" :multiple="false" hint="PNG/JPEG/WebP до 3 МБ" />
                            </div>
                        </div>
                        @error('avatar')
                            <p class="text-sm text-error">{{ $message }}</p>
                        @enderror
                    </div>
                </section>

                {{-- Personal data --}}
                <section class="card border border-base-300 bg-base-100">
                    <div class="card-body gap-4">
                        <h2 class="card-title text-base">
                            <x-app-icon icon="heroicons:identification" class="h-5 w-5 text-primary" />
                            Личные данные
                        </h2>
                        <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                            <div class="flex items-start justify-between gap-3 rounded-2xl border border-base-300 bg-base-200/30 px-4 py-3">
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-medium text-base-content/60">ФИО</p>
                                    <p class="mt-0.5 font-medium text-base-content">{{ $fio !== '' ? $fio : '—' }}</p>
                                    <p class="mt-1 text-xs text-base-content/50">Формат: Фамилия Имя или Фамилия Имя Отчество</p>
                                </div>
                                <button type="button" wire:click="openPersonalEdit('fio')" class="btn btn-ghost btn-square btn-sm shrink-0" title="Изменить ФИО">
                                    <x-app-icon icon="heroicons:pencil-square" class="h-5 w-5" />
                                </button>
                            </div>
                            <div class="flex items-start justify-between gap-3 rounded-2xl border border-base-300 bg-base-200/30 px-4 py-3">
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-medium text-base-content/60">Дата рождения</p>
                                    <p class="mt-0.5 font-medium text-base-content">{{ $date_of_birth !== '' ? $date_of_birth : '—' }}</p>
                                </div>
                                <button type="button" wire:click="openPersonalEdit('date_of_birth')" class="btn btn-ghost btn-square btn-sm shrink-0" title="Изменить дату рождения">
                                    <x-app-icon icon="heroicons:pencil-square" class="h-5 w-5" />
                                </button>
                            </div>
                            <div class="flex items-start justify-between gap-3 rounded-2xl border border-base-300 bg-base-200/30 px-4 py-3">
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-medium text-base-content/60">Никнейм</p>
                                    <p class="mt-0.5 font-medium text-base-content">{{ '@'.$nickname }}</p>
                                </div>
                                <span class="btn btn-ghost btn-square btn-sm shrink-0 cursor-default border-0 bg-transparent" title="Никнейм нельзя изменить">
                                    <x-app-icon icon="heroicons:lock-closed" class="h-5 w-5 text-base-content/40" />
                                </span>
                            </div>
                            <div class="flex items-start justify-between gap-3 rounded-2xl border border-base-300 bg-base-200/30 px-4 py-3">
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-medium text-base-content/60">Роль</p>
                                    <p class="mt-0.5 font-medium text-base-content">{{ $role }}</p>
                                </div>
                                <span class="btn btn-ghost btn-square btn-sm shrink-0 cursor-default border-0 bg-transparent" title="Роль назначается системой">
                                    <x-app-icon icon="heroicons:lock-closed" class="h-5 w-5 text-base-content/40" />
                                </span>
                            </div>
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
                            <div class="form-control w-full">
                                <label class="label cursor-default py-0 pb-1">
                                    <span class="label-text">Электронная почта</span>
                                </label>
                                <div class="flex flex-row items-center gap-3">
                                    <input
                                        type="text"
                                        readonly
                                        class="input input-bordered w-full min-w-0 flex-1 cursor-default bg-base-200/40"
                                        value="{{ $authUser->email }}"
                                    />
                                    <button type="button" wire:click="openEmailChangeModal" class="btn btn-ghost btn-square btn-sm shrink-0 border border-base-300 md:btn-md" title="Изменить email">
                                        <x-app-icon icon="heroicons:pencil-square" class="h-5 w-5" />
                                    </button>
                                </div>
                            </div>

                            <div class="form-control w-full">
                                <label class="label cursor-default py-0 pb-1">
                                    <span class="label-text">Телефон</span>
                                </label>
                                <div class="flex flex-row items-center gap-3">
                                    <input
                                        type="text"
                                        readonly
                                        class="input input-bordered w-full min-w-0 flex-1 cursor-default bg-base-200/40"
                                        value="{{ $authUser->phone }}"
                                    />
                                    <button type="button" wire:click="openPhoneChangeModal" class="btn btn-ghost btn-square btn-sm shrink-0 border border-base-300 md:btn-md" title="Изменить номер">
                                        <x-app-icon icon="heroicons:pencil-square" class="h-5 w-5" />
                                    </button>
                                </div>
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
                            <x-marymarkdown wire:model.live.debounce.1500ms="description" :config="$this->config" />
                        </div>
                        @error('description')
                            <p class="text-sm text-error">{{ $message }}</p>
                        @enderror
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
                            <x-marytoggle label="Профиль виден всем" wire:model.live="is_profile_public" />
                            <x-marytoggle label="Показывать email в публичном профиле" wire:model.live="show_email_on_profile" />
                            <x-marytoggle label="Показывать телефон в публичном профиле" wire:model.live="show_phone_on_profile" />
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
                            Смена пароля выполняется в отдельном окне — потребуется текущий пароль.
                        </p>
                        <button type="button" wire:click="openPasswordChangeModal" class="btn btn-ghost btn-square btn-sm border border-base-300 md:btn-md" title="Изменить пароль">
                            <x-app-icon icon="heroicons:pencil-square" class="h-5 w-5" />
                        </button>
                    </div>
                </section>
            </div>
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
                        <p
                            class="mt-3 line-clamp-3 text-xs text-base-content/75"
                            x-data="{ placeholder: 'Описание пока не заполнено.' }"
                            x-text="(($wire.description ?? '').trim() ? $wire.description : placeholder)"
                        >
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

    <x-mary-modal wire:model="personalEditModal" title="Редактирование" class="backdrop-blur">
        <div class="space-y-4">
            @if ($personalEditField === 'fio')
                <x-mary-input
                    label="ФИО"
                    wire:model="personalDraft"
                    placeholder="Иванов Иван Иванович"
                    hint="Формат: Фамилия Имя или Фамилия Имя Отчество"
                />
            @elseif ($personalEditField === 'date_of_birth')
                <x-mary-input label="Дата рождения" type="date" wire:model="personalDraft" />
            @endif
            @error('personalDraft')
                <p class="text-sm text-error">{{ $message }}</p>
            @enderror
            <div class="flex flex-wrap justify-end gap-2">
                <x-mary-button label="Отмена" class="btn-ghost" type="button" wire:click="closePersonalEdit" />
                <x-mary-button label="Сохранить" class="btn-primary" type="button" wire:click="savePersonalFromModal" />
            </div>
        </div>
    </x-mary-modal>

    <x-mary-modal wire:model="passwordChangeModal" title="Смена пароля" class="backdrop-blur">
        <div class="space-y-4">
            <x-marypassword label="Текущий пароль" wire:model="current_password" />
            <x-marypassword label="Новый пароль" wire:model="new_password" />
            <x-marypassword label="Подтверждение нового пароля" wire:model="new_password_confirmation" />
            <div class="flex flex-wrap justify-end gap-2">
                <x-mary-button label="Отмена" class="btn-ghost" type="button" wire:click="closePasswordChangeModal" />
                <x-mary-button label="Сохранить пароль" class="btn-primary" type="button" wire:click="savePasswordFromModal" />
            </div>
        </div>
    </x-mary-modal>

</div>
