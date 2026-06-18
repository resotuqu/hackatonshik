<div>
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
            <a class="btn btn-sm btn-primary" href="/admin/avatar-presets">Аватарки (паки)</a>
            <a class="btn btn-sm btn-primary" href="{{ route('admin.news') }}">Новости</a>
            <a class="btn btn-sm btn-primary" href="{{ route('admin.users') }}">Пользователи</a>
            <a class="btn btn-sm btn-outline" href="/hackatons">Хакатоны</a>
            <a class="btn btn-sm btn-outline" href="/teams">Команды</a>
            <a class="btn btn-sm btn-outline" href="{{ route('profile') }}">Мой профиль</a>
        </div>
    </x-mary-card>

    <x-mary-card title="Публикация шаблонов хакатонов" class="mt-6 w-full lg:w-2/3 justify-self-center card card-border bg-base-100">
        <div class="space-y-3">
            @forelse($this->templatesForAdmin() as $template)
                <div class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-base-300 px-4 py-3" wire:key="admin-template-{{ $template->id }}">
                    <div>
                        <p class="font-medium">{{ $template->title }}</p>
                        <p class="text-xs text-base-content/70">{{ $template->slug }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        @if($template->is_public)
                            <span class="badge badge-success badge-outline">Публичный</span>
                            <button type="button" class="btn btn-xs btn-ghost" wire:click="unpublishTemplate({{ $template->id }})">Скрыть</button>
                        @else
                            <span class="badge badge-ghost">Черновик</span>
                            <button type="button" class="btn btn-xs btn-primary" wire:click="publishTemplate({{ $template->id }})">Опубликовать</button>
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-sm text-base-content/60">Шаблоны не найдены. Запустите сиды в local/testing.</p>
            @endforelse
        </div>
    </x-mary-card>

    <x-mary-card title="Экспериментальные функции" class="mt-6 w-full lg:w-2/3 justify-self-center card card-border bg-base-100">
        @php
            $platformSettings = \App\Models\PlatformSetting::allSettings();
        @endphp

        @if($platformSettings->isEmpty())
            <p class="text-sm text-base-content/60">Нет настроенных функций.</p>
        @else
            <div class="space-y-3">
                @foreach($platformSettings as $setting)
                    @php $isOn = (bool) $setting->value; @endphp
                    <div class="flex flex-wrap items-start justify-between gap-3 rounded-xl border border-base-300 px-4 py-3" wire:key="pf-{{ $setting->key }}">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2">
                                <p class="font-medium">{{ $setting->label }}</p>
                                @if($isOn)
                                    <span class="badge badge-success badge-xs badge-outline">вкл</span>
                                @else
                                    <span class="badge badge-ghost badge-xs">выкл</span>
                                @endif
                            </div>
                            @if($setting->description)
                                <p class="mt-0.5 text-xs text-base-content/60">{{ $setting->description }}</p>
                            @endif
                            <p class="mt-1 font-mono text-[10px] text-base-content/30">{{ $setting->key }}</p>
                        </div>
                        <button
                            type="button"
                            class="btn btn-sm shrink-0 {{ $isOn ? 'btn-error btn-outline' : 'btn-success' }}"
                            wire:click="togglePlatformFeature('{{ $setting->key }}')"
                            wire:confirm="{{ $isOn ? 'Отключить функцию? Новые загрузки больших файлов будут заблокированы, но ранее загруженные файлы останутся.' : 'Включить функцию?' }}"
                        >
                            {{ $isOn ? 'Отключить' : 'Включить' }}
                        </button>
                    </div>
                @endforeach
            </div>
        @endif
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
