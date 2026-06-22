<div class="mx-auto w-full max-w-6xl space-y-8 pb-8">
    @php
        $hasFilledRole = collect($roles)->contains(
            fn($role) => filled($role['title'] ?? null) &&
                filled($role['description'] ?? null) &&
                filled($role['role'] ?? null),
        );
        $hasPhoto = !empty($photo) || filled($team->image_url ?? null);
        $progressLabels = ['Название', 'Описание', 'Обложка', 'Хакатон', 'Роль'];
        $progressSteps = [filled($title), filled($description), $hasPhoto, filled($hackaton_id), $hasFilledRole];
        $completedSteps = collect($progressSteps)->filter()->count();
        $totalSteps = count($progressSteps);
        $progressPercent = (int) round(($completedSteps / max($totalSteps, 1)) * 100);
    @endphp

    {{-- Breadcrumbs --}}
    <div class="text-sm breadcrumbs">
        <ul>
            <li><a href="/">Главная</a></li>
            <li><a href="/profile/teams">Мои команды</a></li>
            <li class="opacity-70">Редактирование команды</li>
        </ul>
    </div>

    {{-- Header --}}
    <div class="card border border-base-300 bg-base-100">
        <div class="flex flex-col gap-6 px-6 py-8 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-5">
                <div
                    class="flex h-14 w-14 items-center justify-center rounded-panel bg-base-200 text-3xl font-black tracking-tighter text-base-content ring-1 ring-base-300">
                    {{ $teamInitials }}
                </div>
                <div>
                    <h1 class="font-display text-3xl font-semibold tracking-tight">Редактирование команды</h1>
                    <p class="text-base-content/70">Обновите информацию — чтобы ваша команда выглядела профессионально
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-4 rounded-lg border border-base-300 bg-base-200 px-6 py-3">
                <span class="font-display text-4xl font-black tabular-nums text-base-content">{{ $teamInitials }}</span>
                <div class="max-w-52 truncate text-lg font-semibold">{{ $team->title }}</div>
            </div>
        </div>
    </div>

    {{-- Progress --}}
    <div class="card border border-base-300 bg-base-100 p-6">
        <div class="mb-6 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <x-app-icon icon="heroicons:chart-bar" class="h-6 w-6 text-base-content/70" />
                <div>
                    <p class="font-semibold">Прогресс заполнения</p>
                    <p class="text-sm text-base-content/70">Чем полнее карточка — тем заметнее команда в каталоге</p>
                </div>
            </div>
            <div class="text-sm font-semibold tabular-nums">
                <span class="text-base-content">{{ $completedSteps }}</span><span
                    class="text-base-content/40">/{{ $totalSteps }}</span>
            </div>
        </div>

        <div class="flex justify-between gap-2">
            @foreach ($progressLabels as $i => $label)
                @php $done = $progressSteps[$i] ?? false; @endphp
                <div class="flex flex-1 flex-col items-center gap-2 text-center">
                    <div
                        class="{{ $done ? 'bg-base-content text-base-100 ring-2 ring-base-300' : 'bg-base-200 text-base-content/40' }} flex h-9 w-9 items-center justify-center rounded-panel text-sm font-semibold transition-colors">
                        @if ($done)
                            <x-mary-icon name="o-check" class="h-5 w-5" />
                        @else
                            {{ $i + 1 }}
                        @endif
                    </div>
                    <span
                        class="text-xs font-medium uppercase tracking-widest text-base-content/70">{{ $label }}</span>
                </div>
            @endforeach
        </div>

        <div class="mt-6 h-2.5 w-full overflow-hidden rounded-full bg-base-200">
            <div class="h-full bg-base-content/40 transition-all duration-500" style="width: {{ $progressPercent }}%"></div>
        </div>
        <p class="mt-2 text-right font-mono text-xs text-base-content/50">{{ $progressPercent }}% заполнено</p>
    </div>

    <x-maryform wire:submit="save" class="space-y-8">
        <div class="grid grid-cols-1 gap-8 xl:grid-cols-2">

            {{-- Основная информация --}}
            <div class="card border border-base-300 bg-base-100 p-7">
                <div class="flex items-center gap-3 border-b pb-5">
                    <x-app-icon icon="heroicons:identification" class="h-6 w-6 text-base-content/70" />
                    <div>
                        <h2 class="text-xl font-semibold">Основная информация</h2>
                        <p class="text-sm text-base-content/70">Как вас увидят участники и организаторы</p>
                    </div>
                </div>

                <div class="mt-6 space-y-6">
                    <x-mary-input wire:model="title" label="Название команды" placeholder="Например, Team Phoenix" />

                    <div class="[&_.CodeMirror]:min-h-[14rem]">
                        <x-marymarkdown wire:model="description" label="Описание команды" :config="$config" />
                    </div>
                </div>
            </div>

            {{-- Обложка и хакатон --}}
            <div class="card border border-base-300 bg-base-100 p-7">
                <div class="flex items-center gap-3 border-b pb-5">
                    <x-app-icon icon="heroicons:photo" class="h-6 w-6 text-base-content/70" />
                    <div>
                        <h2 class="text-xl font-semibold">Обложка и хакатон</h2>
                        <p class="text-sm text-base-content/70">Яркая обложка помогает выделиться</p>
                    </div>
                </div>

                <div class="cover-upload-root mt-6 space-y-6">
                    @if ($photo)
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-start">
                            <img src="{{ $photo->temporaryUrl() }}" alt="Превью обложки"
                                class="aspect-video w-full max-h-72 rounded-panel object-cover ring-1 ring-base-300">
                            <x-mary-button type="button" wire:click="removePhoto" label="Отменить замену"
                                class="btn-ghost btn-sm text-error" />
                        </div>
                    @elseif (!empty($team->image_url))
                        <img src="{{ asset('storage/' . $team->image_url) }}" alt="Текущая обложка"
                            class="aspect-video w-full max-h-72 rounded-panel object-cover ring-1 ring-base-300">
                    @endif

                    <x-maryfile wire:model="photo" accept="image/png, image/jpeg, image/webp" label="Загрузить обложку"
                        hint="PNG, JPEG, WebP • до 4 МБ" />

                    <div class="divider text-xs">Хакатон</div>
                    <x-maryselect label="Хакатон" wire:model="hackaton_id" :options="$this->hackatons" />
                </div>
            </div>
        </div>

        {{-- Социальные ссылки --}}
        <div class="card border border-base-300 bg-base-100 p-7">
            <div class="flex items-center justify-between border-b pb-5">
                <div class="flex items-center gap-3">
                    <x-app-icon icon="heroicons:link" class="h-6 w-6 text-base-content/70" />
                    <div>
                        <h2 class="text-xl font-semibold">Социальные ссылки</h2>
                        <p class="text-sm text-base-content/70">Контакты для участников</p>
                    </div>
                </div>
                <x-mary-button type="button" wire:click="addSocialLink" label="Добавить ссылку" icon="o-plus" />
            </div>

            <div class="mt-6">
                <p class="mb-3 text-xs font-semibold uppercase tracking-widest text-base-content/50">Быстро добавить</p>
                <div class="flex flex-wrap gap-2">
                    @foreach ($this->socialPresets as $preset)
                        <button type="button" wire:click="addSocialPreset('{{ $preset['key'] }}')"
                            class="btn btn-sm btn-outline">
                            <x-app-icon icon="{{ $preset['icon'] }}" class="h-4 w-4" />
                            {{ $preset['label'] }}
                        </button>
                    @endforeach
                </div>
            </div>

            @if (empty($socialLinks))
                <div
                    class="mt-8 rounded-panel border border-dashed border-base-300 py-12 text-center text-sm text-base-content/70">
                    Пока нет ссылок
                </div>
            @endif

            <div class="mt-6 space-y-4">
                @foreach ($socialLinks as $index => $socialLink)
                    <x-mary-card wire:key="socialLink-{{ $socialLink['id'] }}">
                        <div class="flex items-start gap-4">
                            <div class="flex h-12 w-12 items-center justify-center rounded-panel bg-base-200">
                                <x-app-icon icon="{{ $this->socialLinkIcon($socialLink) }}" class="h-6 w-6" />
                            </div>
                            <div class="flex-1 space-y-4">
                                <div class="flex justify-between">
                                    <x-marybadge value="Ссылка #{{ $index + 1 }}" />
                                    <x-mary-button type="button" wire:click="removeSocialLink({{ $index }})"
                                        label="Удалить" class="btn-ghost btn-xs text-error" icon="o-x-mark" />
                                </div>
                                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                    <x-mary-input
                                        wire:model.live.debounce.400ms="socialLinks.{{ $index }}.name"
                                        label="Название" />
                                    <x-mary-input wire:model.live.debounce.400ms="socialLinks.{{ $index }}.url"
                                        label="Ссылка" placeholder="https://" />
                                </div>
                            </div>
                        </div>
                    </x-mary-card>
                @endforeach
            </div>
        </div>

        {{-- Роли в команде --}}
        <div class="card border border-base-300 bg-base-100 p-7">
            <div class="flex items-center justify-between border-b pb-5">
                <div class="flex items-center gap-3">
                    <x-app-icon icon="heroicons:user-group" class="h-6 w-6 text-base-content/70" />
                    <div>
                        <h2 class="text-xl font-semibold">Роли в команде</h2>
                        <p class="text-sm text-base-content/70">Управляйте вакансиями и своей ролью капитана</p>
                    </div>
                </div>
                <x-mary-button type="button" wire:click="addRole" label="Добавить роль" icon="o-plus" />
            </div>

            <div class="mt-6">
                <x-team-role-form field-prefix="captainRole" heading="Моя роль в команде"
                    description="Эта роль закреплена за владельцем команды" badge="Вы — капитан" :highlight="true"
                    locked-label="Закреплено за вами" :popular-role-titles="$this->popularRoleTitles" :roles-data="$this->rolesData" :skills-data="$this->skillsData"
                    :config="$this->config" quick-fill-method="fillCaptainRoleTitle" />
            </div>

            <div class="divider my-8">Вакансии команды</div>

            @if (empty($roles))
                <div
                    class="rounded-panel border border-dashed border-base-300 py-12 text-center text-sm text-base-content/70">
                    Добавьте роли, если ищете участников
                </div>
            @endif

            <div class="space-y-8">
                @foreach ($roles as $index => $role)
                    <x-team-role-form wire:key="role-{{ $role['id'] }}" field-prefix="roles.{{ $index }}"
                        heading="Роль #{{ $index + 1 }}" description="Отредактируйте роль и навыки"
                        :locked-label="$role['is_occupied'] ?? false ? 'Роль занята' : null" :remove-action="$role['is_occupied'] ?? false ? null : 'removeRole(' . $index . ')'" :popular-role-titles="$this->popularRoleTitles" :roles-data="$this->rolesData" :skills-data="$this->skillsData"
                        :config="$this->config" quick-fill-method="fillRoleTitle" :quick-fill-index="$index" />
                @endforeach
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex flex-col-reverse gap-4 sm:flex-row sm:items-center sm:justify-between">
            <x-mary-button link="/profile/teams" label="Отмена" class="btn-ghost" icon="o-x-mark" />
            <x-mary-button type="submit" label="Сохранить изменения" class="btn-primary" spinner="save"
                icon="o-check" />
        </div>
    </x-maryform>
</div>
