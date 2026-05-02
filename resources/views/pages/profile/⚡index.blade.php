<?php

use App\Services\ContactChangeService;
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
    public ?string $description = null;
    public string $current_password = '';
    public string $new_password = '';
    public string $new_password_confirmation = '';
    public bool $is_profile_public = true;
    public bool $show_email_on_profile = false;
    public bool $show_phone_on_profile = false;

    public bool $phoneChangeModal = false;

    public string $phoneChangeStep = 'phone';

    public string $new_phone = '';

    public string $phone_email_code = '';

    public string $phone_sms_code = '';

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
            $this->phoneChangeStep = $state['step'] === 1 ? 'email' : 'sms';
        } else {
            $this->new_phone = '';
            $this->phoneChangeStep = 'phone';
        }
        $this->phone_email_code = '';
        $this->phone_sms_code = '';
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
        $this->phone_sms_code = '';
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
        app(ContactChangeService::class)->verifyPhoneChangeEmailAndSendSms($user, $this->phone_email_code);
        $this->phoneChangeStep = 'sms';
        $this->phone_sms_code = '';
        $this->success('Код отправлен SMS на новый номер.', position: 'toast-center toast-top');
    }

    public function resendPhoneChangeSms(): void
    {
        $user = Auth::user();
        if (! $user) {
            return;
        }
        app(ContactChangeService::class)->resendPhoneChangeSms($user);
        $this->success('SMS отправлено повторно.', position: 'toast-center toast-top');
    }

    public function confirmPhoneSmsCode(): void
    {
        $this->validate([
            'phone_sms_code' => ['required', 'digits:6'],
        ], [
            'phone_sms_code.required' => 'Введите код из SMS.',
            'phone_sms_code.digits' => 'Код из 6 цифр.',
        ]);
        $user = Auth::user();
        if (! $user) {
            return;
        }
        app(ContactChangeService::class)->completePhoneChange($user, $this->phone_sms_code);
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

            <div class="grid grid-cols-1 gap-2 md:grid-cols-2 md:items-end">
                <x-mary-input label="Электронная почта" :value="auth()->user()->email" readonly />
                <x-mary-button label="Изменить email" class="btn-outline btn-primary" type="button" wire:click="openEmailChangeModal" />
            </div>

            <div class="grid grid-cols-1 gap-2 md:grid-cols-2 md:items-end">
                <x-mary-input label="Телефон" :value="auth()->user()->phone" readonly />
                <x-mary-button label="Изменить номер" class="btn-outline btn-primary" type="button" wire:click="openPhoneChangeModal" />
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

            <x-marypassword label="Текущий пароль (нужен только при смене пароля)" wire:model="current_password" />
            <x-marypassword label="Новый пароль" wire:model="new_password" />
            <x-marypassword label="Подтверждение нового пароля" wire:model="new_password_confirmation" />

            <x-slot:actions>
                <x-mary-button label="Сохранить" class="btn-primary" type="submit" />
            </x-slot:actions>
        </x-maryform>
    </x-mary-card>

    <x-mary-modal wire:model="phoneChangeModal" title="Смена номера телефона" class="backdrop-blur">
        <div class="space-y-4">
            @if ($phoneChangeStep === 'phone')
                <p class="text-sm text-base-content/80">Сначала придёт код на вашу текущую почту, затем SMS на новый номер.</p>
                <x-mary-input label="Новый номер телефона" wire:model="new_phone" hint="11–12 символов, как при регистрации" />
            @elseif ($phoneChangeStep === 'email')
                <p class="text-sm text-base-content/80">Введите код из письма, отправленного на <span class="font-medium">{{ auth()->user()->email }}</span>.</p>
                <x-mary-input label="Код из почты" wire:model="phone_email_code" maxlength="6" />
                <div class="flex flex-wrap gap-2">
                    <x-mary-button label="Подтвердить" class="btn-primary" type="button" wire:click="confirmPhoneEmailCode" />
                    <x-mary-button label="Отправить код снова" class="btn-ghost" type="button" wire:click="resendPhoneChangeEmailCode" />
                </div>
            @elseif ($phoneChangeStep === 'sms')
                <p class="text-sm text-base-content/80">Введите код из SMS, отправленного на <span class="font-medium">{{ $new_phone }}</span>.</p>
                <x-mary-input label="Код из SMS" wire:model="phone_sms_code" maxlength="6" />
                <div class="flex flex-wrap gap-2">
                    <x-mary-button label="Подтвердить" class="btn-primary" type="button" wire:click="confirmPhoneSmsCode" />
                    <x-mary-button label="Отправить SMS снова" class="btn-ghost" type="button" wire:click="resendPhoneChangeSms" />
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
