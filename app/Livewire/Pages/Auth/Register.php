<?php

namespace App\Livewire\Pages\Auth;

use App\Actions\Auth\RegisterUserWithApplication;
use App\Actions\Fortify\PasswordValidationRules;
use App\Models\User;
use App\Services\OAuth\OAuthPhoneResolver;
use App\Support\OrganizerApplicationRules;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Mary\Traits\Toast;

#[Layout('layouts::app', ['title' => 'Регистрация', 'compactMain' => true])]
class Register extends Component
{
    use PasswordValidationRules, Toast;

    public int $step = 1;

    public const int TOTAL_STEPS = 4;

    public string $accountType = 'user';

    public $fio = '';

    public $date_of_birth = '';

    public $email = '';

    public $nickname = '';

    public $phone = '';

    public $password = '';

    public $password_confirmation = '';

    public bool $pd_consent = false;

    public string $organizerEntityType = 'individual';

    public string $organizerCompanyName = '';

    public string $organizerNote = '';

    public function mount(): void
    {
        if (request()->query('type') === 'partner') {
            $this->accountType = 'partner';
        }
    }

    /**
     * @return array<string, list<\Illuminate\Contracts\Validation\Rule|string>|string>
     */
    protected function rulesForStep(int $step): array
    {
        $rules = match ($step) {
            1 => [
                'accountType' => ['required', 'in:user,partner'],
                'fio' => ['required', 'string', 'max:255'],
                'date_of_birth' => ['required', 'date', 'before:now'],
            ],
            2 => [
                'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)],
                'nickname' => ['required', 'string', 'max:255', Rule::unique(User::class)],
            ],
            3 => [
                'password' => [...$this->passwordRules(), 'confirmed'],
                'password_confirmation' => ['required'],
            ],
            4 => [
                'phone' => ['required', 'string', 'min:10', 'max:20', Rule::unique(User::class)],
                'pd_consent' => ['accepted'],
            ],
            default => [],
        };

        if ($step === 1 && $this->accountType === 'partner') {
            $rules = array_merge(
                $rules,
                OrganizerApplicationRules::forFields(entityType: $this->organizerEntityType),
            );
        }

        return $rules;
    }

    /**
     * @return array<string, list<\Illuminate\Contracts\Validation\Rule|string>|string>
     */
    protected function allRules(): array
    {
        $rules = [
            'fio' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['required', 'date', 'before:now'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)],
            'nickname' => ['required', 'string', 'max:255', Rule::unique(User::class)],
            'password' => [...$this->passwordRules(), 'confirmed'],
            'password_confirmation' => ['required'],
            'phone' => ['required', 'string', 'min:10', 'max:20', Rule::unique(User::class)],
            'pd_consent' => ['accepted'],
        ];

        if ($this->accountType === 'partner') {
            $rules = array_merge(
                $rules,
                OrganizerApplicationRules::forFields(entityType: $this->organizerEntityType),
            );
        }

        return $rules;
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

    public function save(OAuthPhoneResolver $phoneResolver)
    {
        if ($this->step < self::TOTAL_STEPS) {
            $this->nextStep();

            return;
        }

        $normalizedPhone = $phoneResolver->normalize($this->phone);

        if ($normalizedPhone === null) {
            throw ValidationException::withMessages([
                'phone' => [__('ui.auth.register.phone_invalid')],
            ]);
        }

        $this->phone = $normalizedPhone;

        try {
            $this->validate($this->allRules());
        } catch (ValidationException $e) {
            $this->error('Ошибка заполнения полей !', position: 'toast-center toast-top');
            throw $e;
        }

        $user = app(RegisterUserWithApplication::class)->create([
            'fio' => $this->fio,
            'email' => $this->email,
            'nickname' => $this->nickname,
            'phone' => $this->phone,
            'date_of_birth' => $this->date_of_birth,
            'password' => $this->password,
            'account_type' => $this->accountType,
            'organizer_entity_type' => $this->organizerEntityType,
            'organizer_company_name' => $this->organizerCompanyName,
            'organizer_note' => $this->organizerNote,
        ]);

        Auth::login($user);
        session()->regenerate();

        if ($this->accountType === 'partner') {
            $this->success('Регистрация успешна. Заявка на роль организатора отправлена на модерацию.', position: 'toast-center toast-top');
        } else {
            $this->success('Регистрация успешна. Проверьте почту и перейдите по ссылке для подтверждения e-mail.', position: 'toast-center toast-top');
        }

        return $this->redirect(route('verification.notice'));
    }

    public function render()
    {
        return view('pages.auth.register');
    }
}
