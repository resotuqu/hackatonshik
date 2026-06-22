<?php

namespace App\Livewire\Pages\Profile;

use App\Enums\ApplicationStatus;
use App\Enums\UserRole;
use App\Models\Skill;
use App\Models\TeamApplication;
use App\Services\ContactChangeService;
use App\Support\PresetAvatar;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;

class Index extends Component
{
    use Toast, WithFileUploads;

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

    public bool $open_to_teams = false;

    public bool $show_skills_on_profile = false;

    /** @var list<int> */
    public array $skill_ids = [];

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

    public bool $deleteAccountModal = false;

    public string $delete_confirm_password = '';

    private function normalizedProfileDescription(mixed $raw): ?string
    {
        if (! is_string($raw) || trim($raw) === '') {
            return null;
        }

        return str_replace("\r\n", "\n", $raw);
    }

    public function mount()
    {
        if (! Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();
        $this->fio = $user->fio;
        $this->nickname = $user->nickname;
        $this->role = match ($user->role) {
            UserRole::USER => 'Участник',
            UserRole::PARTNER => 'Партнёр',
            UserRole::JUDGE => 'Судья',
            default => 'Администратор',
        };
        $this->date_of_birth = $user->date_of_birth;
        $this->description = $this->normalizedProfileDescription($user->description);
        $this->is_profile_public = (bool) $user->is_profile_public;
        $this->show_email_on_profile = (bool) $user->show_email_on_profile;
        $this->show_phone_on_profile = (bool) $user->show_phone_on_profile;
        $this->open_to_teams = (bool) $user->open_to_teams;
        $this->show_skills_on_profile = (bool) $user->show_skills_on_profile;
        $this->skill_ids = $user->skills()->pluck('skills.id')->map(fn ($id) => (int) $id)->all();
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

    public function persistTeamMatchingPreferences(): void
    {
        $user = Auth::user();
        if (! $user) {
            return;
        }

        $this->validate([
            'skill_ids' => ['array'],
            'skill_ids.*' => ['integer', 'exists:skills,id'],
        ]);

        $user->update([
            'open_to_teams' => $this->open_to_teams,
            'show_skills_on_profile' => $this->show_skills_on_profile,
        ]);
        $user->skills()->sync($this->skill_ids);
    }

    #[Computed]
    public function skillsData()
    {
        return Skill::query()->orderBy('name')->get(['id', 'name']);
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

    public function updatedOpenToTeams(): void
    {
        $this->persistTeamMatchingPreferences();
    }

    public function updatedShowSkillsOnProfile(): void
    {
        $this->persistTeamMatchingPreferences();
    }

    public function updatedSkillIds(): void
    {
        $this->persistTeamMatchingPreferences();
        $this->skipRender();
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

    #[Computed]
    public function currentAvatarUrl(): string
    {
        $authUser = Auth::user();

        if ($this->avatar) {
            return $this->avatar->temporaryUrl();
        }
        if ($this->selected_preset_path) {
            return asset('storage/'.$this->selected_preset_path);
        }
        if ($this->avatar_path) {
            return asset('storage/'.$this->avatar_path);
        }

        return 'https://ui-avatars.com/api/?name='.urlencode($authUser->fio).'&background=random';
    }

    #[Computed]
    public function missingProfileTips(): array
    {
        $authUser = Auth::user();
        $tips = [];

        if (! filled($authUser->fio)) {
            $tips[] = 'Укажите ФИО';
        }
        if (! filled($authUser->date_of_birth)) {
            $tips[] = 'Заполните дату рождения';
        }
        if (! filled($authUser->avatar_path)) {
            $tips[] = 'Добавьте аватар';
        }
        if (! filled($authUser->description)) {
            $tips[] = 'Добавьте описание о себе';
        }
        if (is_null($authUser->email_verified_at)) {
            $tips[] = 'Подтвердите электронную почту';
        }
        if (is_null($authUser->phone_verified_at)) {
            $tips[] = 'Подтвердите номер телефона';
        }

        return $tips;
    }

    public function deleteAccount(): void
    {
        $user = Auth::user();
        if (! $user) {
            return;
        }

        $this->validate([
            'delete_confirm_password' => ['required'],
        ], [
            'delete_confirm_password.required' => 'Введите пароль для подтверждения.',
        ]);

        if (! Hash::check($this->delete_confirm_password, $user->password)) {
            $this->addError('delete_confirm_password', 'Неверный пароль.');

            return;
        }

        Auth::logout();
        $user->delete();

        session()->invalidate();
        session()->regenerateToken();

        $this->redirect('/', navigate: true);
    }

    #[Layout('layouts::app', ['title' => 'Профиль'])]
    public function render()
    {
        return view('pages.profile.index');
    }
}
