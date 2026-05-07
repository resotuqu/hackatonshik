<?php

namespace App\Livewire\Pages\Admin;

use App\Models\Hackaton;
use App\Models\ListAnalyticsEvent;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Mary\Traits\Toast;

#[Layout('layouts::app', ['title' => 'Админ-панель'])]
class Index extends Component
{
    use Toast;

    public string $fio = '';
    public string $date_of_birth = '';
    public string $email = '';
    public string $nickname = '';
    public string $phone = '';
    public string $password = '';
    public string $password_confirmation = '';
    public ?string $description = null;

    public function dashboardStats(): array
    {
        return [
            'users' => User::query()->count(),
            'hackatons' => Hackaton::query()->count(),
            'teams' => Team::query()->count(),
            'list_events' => ListAnalyticsEvent::query()->count(),
        ];
    }

    public function listEventBreakdown(): array
    {
        return ListAnalyticsEvent::query()
            ->selectRaw('event_name, count(*) as total')
            ->groupBy('event_name')
            ->orderByDesc('total')
            ->limit(5)
            ->get()
            ->map(fn ($row) => ['name' => (string) $row->event_name, 'total' => (int) $row->total])
            ->all();
    }

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

    public function render()
    {
        return view('pages.admin.index');
    }
}
