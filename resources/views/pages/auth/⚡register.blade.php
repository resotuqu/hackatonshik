<?php

use App\Actions\Fortify\PasswordValidationRules;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\Attributes\Layout;

new #[Layout('layouts::app', ['title' => 'Регистрация'])]
class extends Component {

    use \Mary\Traits\Toast;
    use PasswordValidationRules;

    public int $step = 1;

    public const int TOTAL_STEPS = 4;

    public $fio = '';

    public $date_of_birth = '';

    public $email = '';

    public $nickname = '';

    public $phone = '';

    public $password = '';

    public $password_confirmation = '';

    /**
     * @return array<string, list<\Illuminate\Contracts\Validation\Rule|string>|string>
     */
    protected function rulesForStep(int $step): array
    {
        return match ($step) {
            1 => [
                'fio' => ['required', 'string', 'max:255'],
                'date_of_birth' => ['required', 'date', 'before:now'],
            ],
            2 => [
                'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)],
                'nickname' => ['required', 'string', 'max:255', Rule::unique(User::class)],
            ],
            3 => [
                'password' => $this->passwordRules(),
            ],
            4 => [
                'phone' => ['required', 'string', 'min:11', 'max:12', Rule::unique(User::class)],
            ],
            default => [],
        };
    }

    /**
     * @return array<string, list<\Illuminate\Contracts\Validation\Rule|string>|string>
     */
    protected function allRules(): array
    {
        return [
            'fio' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['required', 'date', 'before:now'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)],
            'nickname' => ['required', 'string', 'max:255', Rule::unique(User::class)],
            'password' => $this->passwordRules(),
            'phone' => ['required', 'string', 'min:11', 'max:12', Rule::unique(User::class)],
        ];
    }

    public function nextStep(): void
    {
        if ($this->step >= self::TOTAL_STEPS) {
            return;
        }

        $this->validate($this->rulesForStep($this->step));

        $this->step++;
    }

    public function previousStep(): void
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    public function save()
    {
        if ($this->step < self::TOTAL_STEPS) {
            $this->nextStep();

            return;
        }

        try {
            $this->validate($this->allRules());
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->error('Ошибка заполнения полей !', position: 'toast-center toast-top');
            throw $e;
        }

        $user = User::create([
            'fio' => $this->fio,
            'email' => $this->email,
            'nickname' => $this->nickname,
            'phone' => $this->phone,
            'date_of_birth' => $this->date_of_birth,
            'password' => $this->password,
        ]);

        Auth::login($user);
        session()->regenerate();

        $this->success('Успешная регистрация. Подтвердите телефон по SMS.', position: 'toast-center toast-top');

        return $this->redirect(route('phone.verify.notice'));
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

        <x-maryform
            wire:submit.prevent="{{ $step < 4 ? 'nextStep' : 'save' }}"
            class="card border border-base-200 bg-base-100 p-4 shadow-sm sm:p-6 lg:col-span-3"
        >
            <x-mary-header title="Регистрация" separator />

            <ul class="steps steps-horizontal mb-6 w-full max-w-full flex-wrap justify-start gap-y-2 text-[0.65rem] sm:text-xs">
                <li class="step {{ $step >= 1 ? 'step-primary' : '' }}">Личные данные</li>
                <li class="step {{ $step >= 2 ? 'step-primary' : '' }}">Аккаунт</li>
                <li class="step {{ $step >= 3 ? 'step-primary' : '' }}">Пароль</li>
                <li class="step {{ $step >= 4 ? 'step-primary' : '' }}">Телефон</li>
            </ul>

            @if ($step === 1)
                <x-mary-input label="Фамилия, Имя, Отчество" wire:model="fio" placeholder="Владимир" hint="Введите ваше фио" />
                <x-marydatetime label="Дата рождения" hint="Введите вашу дату рождения" wire:model="date_of_birth" />
            @endif

            @if ($step === 2)
                <x-mary-input label="Адрес электронной почты" wire:model="email" placeholder="example@mail.com"
                    hint="Введите вашу электронную почту" />
                <x-mary-input label="Псевдоним" wire:model="nickname" placeholder="vova_vlad_123" hint="Введите ваш псевдоним" />
            @endif

            @if ($step === 3)
                <x-marypassword label="Пароль" wire:model="password" />
                <x-marypassword label="Подтверждение пароля" wire:model="password_confirmation" />
            @endif

            @if ($step === 4)
                <x-mary-input label="Контактный номер телефона" wire:model="phone" prefix="+" />
            @endif

            <x-slot:actions class="flex w-full flex-col gap-2 sm:flex-row sm:justify-end">
                @if ($step > 1)
                    <x-marybutton class="btn-outline w-full sm:w-auto" label="Назад" type="button" wire:click="previousStep" />
                @endif
                @if ($step < 4)
                    <x-marybutton class="btn-primary w-full sm:min-w-40" label="Далее" type="submit" />
                @else
                    <x-marybutton class="btn-primary w-full sm:min-w-40" label="Зарегистрироваться" type="submit" />
                @endif
            </x-slot:actions>
            <a href="/auth/vk/redirect" class="btn btn-outline mt-2 w-full">
                Войти или зарегистрироваться через VK
            </a>
        </x-maryform>
    </div>
</div>
