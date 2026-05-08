<?php

namespace App\Livewire\Pages\Auth;

use App\Actions\Fortify\PasswordValidationRules;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Mary\Traits\Toast;

#[Layout('layouts::app', ['title' => 'Регистрация'])]
class Register extends Component
{
    use PasswordValidationRules, Toast;

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
                'password' => [...$this->passwordRules(), 'confirmed'],
                'password_confirmation' => ['required'],
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
            'password' => [...$this->passwordRules(), 'confirmed'],
            'password_confirmation' => ['required'],
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
        } catch (ValidationException $e) {
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

        $user->sendEmailVerificationNotification();

        Auth::login($user);
        session()->regenerate();

        $this->success('Регистрация успешна. Проверьте почту и перейдите по ссылке для подтверждения e-mail.', position: 'toast-center toast-top');

        return $this->redirect(route('verification.notice'));
    }

    public function render()
    {
        return view('pages.auth.register');
    }
}
