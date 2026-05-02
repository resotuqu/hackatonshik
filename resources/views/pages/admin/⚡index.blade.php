<?php

use App\Models\Hackaton;
use App\Models\ListAnalyticsEvent;
use App\Models\Team;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

new #[Layout('layouts::app', ['title' => 'Админ-панель'])]
class extends Component {
    use \Mary\Traits\Toast;

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
};
?>

<div>
    <x-marytoast />
    @php
        $roadmapItems = collect(config('product_backlog.hackatonshik', []))->sortBy('priority')->values();
        $dashboardStats = $this->dashboardStats();
        $listEventBreakdown = $this->listEventBreakdown();
        $maxEventCount = max(array_column($listEventBreakdown, 'total') ?: [1]);
    @endphp

    <x-mary-card title="Дашборд метрик" class="mb-6 w-full lg:w-2/3 justify-self-center card card-border bg-base-100">
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
            <div class="rounded-xl border border-base-300 p-3">
                <p class="text-xs text-base-content/70">Пользователи</p>
                <p class="text-2xl font-semibold">{{ $dashboardStats['users'] }}</p>
            </div>
            <div class="rounded-xl border border-base-300 p-3">
                <p class="text-xs text-base-content/70">Хакатоны</p>
                <p class="text-2xl font-semibold">{{ $dashboardStats['hackatons'] }}</p>
            </div>
            <div class="rounded-xl border border-base-300 p-3">
                <p class="text-xs text-base-content/70">Команды</p>
                <p class="text-2xl font-semibold">{{ $dashboardStats['teams'] }}</p>
            </div>
            <div class="rounded-xl border border-base-300 p-3">
                <p class="text-xs text-base-content/70">Analytics events</p>
                <p class="text-2xl font-semibold">{{ $dashboardStats['list_events'] }}</p>
            </div>
        </div>

        <div class="mt-4 space-y-2">
            <p class="text-sm font-medium">Топ действий в списках</p>
            @foreach ($listEventBreakdown as $event)
                <div class="space-y-1">
                    <div class="flex items-center justify-between text-xs">
                        <span>{{ $event['name'] }}</span>
                        <span>{{ $event['total'] }}</span>
                    </div>
                    <progress class="progress progress-primary w-full" value="{{ $event['total'] }}" max="{{ $maxEventCount }}"></progress>
                </div>
            @endforeach
        </div>
        <div class="mt-4 flex flex-wrap gap-2">
            <a class="btn btn-sm btn-outline" href="/hackatons">Хакатоны</a>
            <a class="btn btn-sm btn-outline" href="/teams">Команды</a>
            <a class="btn btn-sm btn-outline" href="/profile">Пользователи</a>
        </div>
    </x-mary-card>

    <x-mary-card title="Создание партнёра" class="w-full lg:w-2/3 justify-self-center card card-border bg-base-100">
        <x-slot:menu>
            <x-mary-button label="Выйти" class="btn-secondary" wire:click="logout" />
        </x-slot:menu>

        <x-maryform wire:submit="savePartner">

            <x-mary-input label="ФИО" wire:model="fio" />
            <x-marydatetime label="Дата рождения" wire:model="date_of_birth" />
            <x-mary-input label="Email" wire:model="email" placeholder="partner@mail.com" />
            <x-mary-input label="Никнейм" wire:model="nickname" />
            <x-mary-input label="Телефон" wire:model="phone" prefix="+" />
            <x-marypassword label="Пароль" wire:model="password" />
            <x-marypassword label="Подтверждение пароля" wire:model="password_confirmation" />
            <x-marymarkdown wire:model="description" label="Описание" :config="['toolbar' => ['bold', 'italic', '|', 'preview'], 'uploadImage' => false]" />

            <x-slot:actions>
                <x-mary-button label="Создать партнёра" class="btn-primary" type="submit" />
            </x-slot:actions>
        </x-maryform>
    </x-mary-card>

    <x-mary-card title="Приоритеты бэклога" class="mt-6 w-full lg:w-2/3 justify-self-center card card-border bg-base-100">
        <div class="space-y-2">
            @forelse($roadmapItems as $item)
                <div class="rounded-xl border border-base-300 px-4 py-3 flex flex-wrap items-center justify-between gap-2">
                    <div>
                        <p class="font-medium">P{{ $item['priority'] }}. {{ $item['title'] }}</p>
                        <p class="text-xs text-base-content/70">Ключ: {{ $item['key'] }}</p>
                    </div>
                    <x-marybadge class="{{ $item['status'] === 'in_progress' ? 'badge-info' : 'badge-outline' }}" value="{{ $item['status'] }}" />
                </div>
            @empty
                <p class="text-sm text-base-content/70">Бэклог пока не настроен.</p>
            @endforelse
        </div>
    </x-mary-card>
</div>
