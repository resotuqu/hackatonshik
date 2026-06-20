<div class="mx-auto w-full max-w-6xl space-y-6">

    <nav class="text-sm breadcrumbs">
        <ul>
            <li><a href="/">Главная</a></li>
            <li class="opacity-70">Профиль</li>
        </ul>
    </nav>

    <x-profile-nav-tabs active="personal" />

    {{-- HEADER --}}
    <section class="ui-page-header">
        <div class="pb-5">
            <div class="flex flex-col gap-5 md:flex-row md:items-start md:justify-between">
                <div class="flex flex-col items-start gap-5 sm:flex-row sm:items-center">
                    <div class="avatar">
                        <div class="w-24 rounded-full ring-2 ring-base-300 ring-offset-2 ring-offset-base-100 sm:w-28">
                            <img src="{{ $this->currentAvatarUrl }}" alt="Аватар пользователя" />
                        </div>
                    </div>
                    <div class="space-y-2">
                        <span class="badge badge-neutral badge-outline">{{ $role }}</span>
                        <h1 class="font-display text-3xl font-semibold tracking-tight lg:text-4xl">
                            {{ $fio ?: 'Без имени' }}
                        </h1>
                        <p class="text-base text-base-content/70">{{ '@'.$nickname }}</p>
                        <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-sm text-base-content/75">
                            <span class="inline-flex items-center gap-1.5">
                                <span class="font-semibold text-base-content">{{ $this->joinedHackatonsCount }}</span>
                                хакатонов
                            </span>
                            <span class="text-base-content/30">·</span>
                            <span class="inline-flex items-center gap-1.5">
                                <span class="font-semibold text-base-content">{{ $this->joinedTeamsCount }}</span>
                                команд
                            </span>
                        </div>
                    </div>
                </div>

                <div class="flex flex-row items-center gap-4 md:flex-col md:items-end">
                    <div class="radial-progress text-base-content/40" style="--value:{{ $this->profileCompletenessPercent }};--size:4.5rem;--thickness:5px" role="progressbar" aria-valuenow="{{ $this->profileCompletenessPercent }}" aria-valuemin="0" aria-valuemax="100">
                        <span class="text-sm font-semibold text-base-content">{{ $this->profileCompletenessPercent }}%</span>
                    </div>
                    <a href="{{ route('profile.public.show', ['user' => auth()->user()->nickname]) }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline">
                        <x-app-icon icon="heroicons:eye" class="h-4 w-4" />
                        Посмотреть как другие
                    </a>
                </div>
            </div>

            <div class="mt-4 space-y-1.5">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-base-content/60">Заполненность профиля</span>
                    <span class="font-medium text-base-content">{{ $this->profileCompletenessPercent }}%</span>
                </div>
                <progress class="progress w-full" value="{{ $this->profileCompletenessPercent }}" max="100"></progress>
            </div>
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
                                    <img src="{{ $this->currentAvatarUrl }}" alt="Текущий аватар" />
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
                                        value="{{ auth()->user()->email }}"
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
                                        value="{{ auth()->user()->phone }}"
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

                {{-- Team matching --}}
                <section class="card border border-base-300 bg-base-100">
                    <div class="card-body gap-4">
                        <h2 class="card-title text-base">
                            <x-app-icon icon="heroicons:user-plus" class="h-5 w-5 text-primary" />
                            Поиск команды
                        </h2>
                        <p class="text-sm text-base-content/70">
                            Укажите навыки — мы порекомендуем команды с открытыми ролями, где нужны такие же компетенции.
                        </p>
                        <x-marytoggle label="Ищу команду" wire:model.live="open_to_teams" />
                        <x-marytoggle label="Показывать навыки в публичном профиле" wire:model.live="show_skills_on_profile" />
                        <div class="form-control w-full">
                            <span class="label py-0 pb-1"><span class="label-text text-xs font-medium uppercase tracking-wide text-base-content/60">Мои навыки</span></span>
                            <x-marychoices-offline
                                wire:model.live="skill_ids"
                                :options="$this->skillsData"
                                placeholder="Выберите навыки…"
                                clearable
                                searchable
                            />
                        </div>
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
                        <x-app-icon icon="heroicons:shield-check" class="h-5 w-5 text-base-content/60" />
                        Верификация
                    </h2>

                    <div class="flex items-start gap-3 rounded-xl border border-base-300 p-3">
                        @if (auth()->user()->email_verified_at)
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-success/15 text-success">
                                <x-app-icon icon="heroicons:check-circle" class="h-5 w-5" />
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium">Email подтверждён</p>
                                <p class="truncate text-xs text-base-content/70">{{ auth()->user()->email }}</p>
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
                        @if (auth()->user()->phone_verified_at)
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-success/15 text-success">
                                <x-app-icon icon="heroicons:check-circle" class="h-5 w-5" />
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium">Телефон подтверждён</p>
                                <p class="truncate text-xs text-base-content/70">{{ auth()->user()->phone }}</p>
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
            <section class="card border border-base-300 bg-base-100">
                <div class="card-body gap-4">
                    <h2 class="card-title text-base">
                        <x-app-icon icon="heroicons:user-circle" class="h-5 w-5 text-base-content/60" />
                        Как видят другие
                    </h2>
                    <div class="rounded-2xl border border-base-300 bg-base-100 p-4">
                        <div class="flex items-center gap-3">
                            <div class="avatar">
                                <div class="w-14 rounded-full ring-1 ring-base-300">
                                    <img src="{{ $this->currentAvatarUrl }}" alt="Превью аватара" />
                                </div>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="truncate font-semibold">{{ $fio ?: '—' }}</p>
                                <p class="truncate text-xs text-base-content/70">{{ '@'.$nickname }}</p>
                                <span class="badge badge-neutral badge-outline badge-sm mt-1">{{ $role }}</span>
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
                    <a href="{{ route('profile.public.show', ['user' => auth()->user()->nickname]) }}" target="_blank" rel="noopener" class="btn btn-block btn-sm btn-outline">
                        <x-app-icon icon="heroicons:arrow-top-right-on-square" class="h-4 w-4" />
                        Открыть публичную страницу
                    </a>
                    <a href="{{ route('profile.watches') }}" class="btn btn-block btn-sm btn-outline" wire:navigate>
                        <x-app-icon icon="heroicons:bookmark" class="h-4 w-4" />
                        Мои закладки
                    </a>
                </div>
            </section>

            {{-- Tips --}}
            @if (! empty($this->missingProfileTips))
                <section class="card border border-base-300 bg-base-100">
                    <div class="card-body gap-3">
                        <h2 class="card-title text-base">
                            <x-app-icon icon="heroicons:sparkles" class="h-5 w-5 text-base-content/60" />
                            Что добавить
                        </h2>
                        <ul class="space-y-2 text-sm">
                            @foreach ($this->missingProfileTips as $tip)
                                <li class="flex items-start gap-2">
                                    <x-app-icon icon="heroicons:plus-circle" class="mt-0.5 h-4 w-4 shrink-0 text-base-content/50" />
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
