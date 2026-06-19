    @php
        $publicRoleLabel = $profileUser->role === 'user'
            ? 'Участник'
            : ($profileUser->role === 'partner' ? 'Партнёр' : ($profileUser->role === 'judge' ? 'Судья' : 'Администратор'));
        $avatarUrl = $profileUser->avatar_path
            ? asset('storage/'.$profileUser->avatar_path)
            : 'https://ui-avatars.com/api/?name='.urlencode($profileUser->publicName()).'&background=random';
        $teamsCount = $profileUser->teams->count();
        $hackatonsCount = $profileUser->hackatons->count();

        // Skills grouped by team role
        $roleSkillGroups = $profileUser->teamRoles
            ->filter(fn ($role) => $role->skills->isNotEmpty())
            ->map(fn ($role) => [
                'title' => $role->title,
                'skills' => $role->skills->pluck('name'),
            ]);

        // Personal skills (only if user enabled it)
        $personalSkills = $profileUser->show_skills_on_profile
            ? $profileUser->skills->pluck('name')
            : collect();

        // All skills (flat, for SEO / summary stat)
        $allSkills = $profileUser->teamRoles
            ->flatMap(fn ($role) => $role->skills->pluck('name'))
            ->merge($personalSkills)
            ->unique()
            ->values();

        $publicProfileUrl = route('profile.public.show', ['user' => $profileUser->nickname]);

        $bioSource = filled($profileUser->description)
            ? strip_tags(\App\Support\SafeMarkdown::toHtml($profileUser->description))
            : sprintf('%s — %s на платформе «Хакатонщик».', $profileUser->publicName(), $publicRoleLabel);
        $bioSource = preg_replace('/\s+/u', ' ', $bioSource ?? '') ?? '';
        $seoDescription = trim(mb_substr($bioSource, 0, 180, 'UTF-8'));
    @endphp

    @section('title', $profileUser->publicName())
    @section('meta_description', $seoDescription)
    @section('canonical_url', $publicProfileUrl)
    @section('og_image', $avatarUrl)

    <div class="mx-auto w-full max-w-6xl space-y-6">
        <nav class="text-sm breadcrumbs">
            <ul>
                <li><a href="/">Главная</a></li>
                <li class="opacity-70">Публичный профиль</li>
            </ul>
        </nav>

        {{-- HERO --}}
        <section class="ui-page-hero">
            <div class="relative flex flex-col gap-6 md:flex-row md:items-start md:justify-between">
                <div class="flex flex-col items-start gap-5 sm:flex-row sm:items-center">
                    <div class="avatar">
                        <div class="w-32 rounded-full ring-2 ring-secondary/40 ring-offset-2 ring-offset-base-100 sm:w-36">
                            <img src="{{ $avatarUrl }}" alt="Аватар {{ $profileUser->publicName() }}" />
                        </div>
                    </div>
                    <div class="space-y-2">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="badge badge-primary badge-outline">{{ $publicRoleLabel }}</span>
                            @if ($profileUser->hasVerifiedContactChannels())
                                <span class="badge badge-success badge-outline gap-1">
                                    <x-app-icon icon="heroicons:shield-check" class="h-3.5 w-3.5" />
                                    Профиль подтверждён
                                </span>
                            @endif
                            @if ($profileUser->open_to_teams)
                                <span class="badge badge-accent badge-outline gap-1">
                                    <x-app-icon icon="heroicons:user-plus" class="h-3.5 w-3.5" />
                                    Открыт к командам
                                </span>
                            @endif
                        </div>
                        <h1 class="ui-heading-display text-3xl font-semibold lg:text-4xl">
                            {{ $profileUser->publicName() }}
                        </h1>
                        <p class="text-base text-base-content/70">{{ '@'.$profileUser->nickname }}</p>
                        <p class="text-sm text-base-content/60">
                            На платформе с <x-datetime :value="$profileUser->created_at" mode="date" class="font-medium text-base-content/75" />
                        </p>
                        <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-sm text-base-content/75">
                            <span class="inline-flex items-center gap-1.5">
                                <span class="font-semibold text-secondary">{{ $hackatonsCount }}</span>
                                хакатонов
                            </span>
                            <span class="text-base-content/30">·</span>
                            <span class="inline-flex items-center gap-1.5">
                                <span class="font-semibold text-secondary">{{ $teamsCount }}</span>
                                команд
                            </span>
                            @if ($allSkills->isNotEmpty())
                                <span class="text-base-content/30">·</span>
                                <span class="inline-flex items-center gap-1.5">
                                    <span class="font-semibold text-secondary">{{ $allSkills->count() }}</span>
                                    навыков
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2 sm:justify-end">
                    <button
                        class="btn btn-sm btn-outline btn-secondary gap-2"
                        type="button"
                        onclick="if (navigator.share) { navigator.share({ title: @json($profileUser->publicName()), url: @json($publicProfileUrl) }); } else { navigator.clipboard.writeText(@json($publicProfileUrl)); const t = this.querySelector('[data-share-label]'); if (t) t.textContent = 'Ссылка скопирована'; }"
                    >
                        <x-app-icon icon="heroicons:share" class="h-4 w-4 shrink-0" />
                        <span data-share-label>Поделиться</span>
                    </button>
                </div>
            </div>
        </section>

        @if ($profileUser->show_email_on_profile || ($profileUser->show_phone_on_profile && filled($profileUser->phone)))
            <section class="card border border-base-300 bg-base-100">
                <div class="card-body gap-4">
                    <h2 class="card-title text-base">
                        <x-app-icon icon="heroicons:envelope" class="h-5 w-5 text-primary" />
                        Контакты
                    </h2>
                    <div class="space-y-4">
                        @if ($profileUser->show_email_on_profile)
                            <div class="form-control w-full">
                                <label class="label cursor-default py-0 pb-1">
                                    <span class="label-text">Электронная почта</span>
                                </label>
                                <a href="mailto:{{ $profileUser->email }}" class="link link-primary break-all text-base font-medium">
                                    {{ $profileUser->email }}
                                </a>
                            </div>
                        @endif
                        @if ($profileUser->show_phone_on_profile && filled($profileUser->phone))
                            <div class="form-control w-full">
                                <label class="label cursor-default py-0 pb-1">
                                    <span class="label-text">Телефон</span>
                                </label>
                                <a href="tel:{{ $profileUser->phone }}" class="link link-primary text-base font-medium">
                                    {{ $profileUser->phone }}
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </section>
        @endif

        @if (filled($profileUser->description))
            <section class="card border border-base-300 bg-base-100">
                <div class="card-body gap-4">
                    <h2 class="card-title text-base">
                        <x-app-icon icon="heroicons:document-text" class="h-5 w-5 text-primary" />
                        О себе
                    </h2>
                    <div class="markdown-body">
                        {!! \App\Support\SafeMarkdown::toHtml($profileUser->description) !!}
                    </div>
                </div>
            </section>
        @endif

        {{-- SKILLS --}}
        <section class="card border border-base-300 bg-base-100">
            <div class="card-body gap-4">
                <h2 class="card-title text-base">
                    <x-app-icon icon="heroicons:sparkles" class="h-5 w-5 text-primary" />
                    Навыки и роли
                </h2>
                @if ($roleSkillGroups->isEmpty() && $personalSkills->isEmpty())
                    <x-empty-state
                        embedded
                        title="Навыки не указаны"
                        description="Пользователь пока не добавил навыки в профиль."
                        icon="heroicons:sparkles"
                    />
                @else
                    <div class="space-y-4">
                        @foreach ($roleSkillGroups as $group)
                            <div>
                                <p class="mb-2 text-xs font-medium uppercase tracking-wide text-base-content/50">
                                    {{ $group['title'] }}
                                </p>
                                <div class="flex flex-wrap gap-2">
                                    @foreach ($group['skills'] as $skill)
                                        <span class="badge badge-primary badge-outline">{{ $skill }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                        @if ($personalSkills->isNotEmpty())
                            <div>
                                <p class="mb-2 text-xs font-medium uppercase tracking-wide text-base-content/50">
                                    Личные навыки
                                </p>
                                <div class="flex flex-wrap gap-2">
                                    @foreach ($personalSkills as $skill)
                                        <span class="badge badge-ghost">{{ $skill }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </section>

        {{-- STATS --}}
        <section class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <article class="card border border-base-300 bg-base-100">
                <div class="card-body gap-2">
                    <p class="flex items-center gap-2 text-sm text-base-content/70">
                        <x-app-icon icon="heroicons:user-group" class="h-4 w-4 text-secondary" />
                        Команды
                    </p>
                    <p class="text-3xl font-semibold text-secondary">{{ $teamsCount }}</p>
                </div>
            </article>
            <article class="card border border-base-300 bg-base-100">
                <div class="card-body gap-2">
                    <p class="flex items-center gap-2 text-sm text-base-content/70">
                        <x-app-icon icon="heroicons:flag" class="h-4 w-4 text-secondary" />
                        Хакатоны
                    </p>
                    <p class="text-3xl font-semibold text-secondary">{{ $hackatonsCount + $participatedHackatons->count() }}</p>
                </div>
            </article>
            <article class="card border border-base-300 bg-base-100">
                <div class="card-body gap-2">
                    <p class="flex items-center gap-2 text-sm text-base-content/70">
                        <x-app-icon icon="heroicons:scale" class="h-4 w-4 text-secondary" />
                        Назначения судьей
                    </p>
                    <p class="text-3xl font-semibold text-secondary">{{ (int) ($profileUser->judge_assignments_count ?? 0) }}</p>
                </div>
            </article>
        </section>

        {{-- TEAMS --}}
        <section class="card border border-base-300 bg-base-100">
            <div class="card-body gap-4">
                <h2 class="card-title text-base">
                    <x-app-icon icon="heroicons:user-group" class="h-5 w-5 text-primary" />
                    Последние команды
                </h2>
                @if ($profileUser->teams->isEmpty())
                    <x-empty-state
                        embedded
                        title="Команд нет"
                        description="Публичных команд у пользователя пока не отображается."
                        icon="heroicons:user-group"
                    />
                @else
                    <div class="flex flex-wrap gap-2">
                        @foreach ($profileUser->teams as $team)
                            <a href="{{ route('teams.show', $team) }}" class="badge badge-lg badge-outline">{{ $team->title }}</a>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>

        {{-- HACKATON HISTORY --}}
        @if ($profileUser->hackatons->isNotEmpty() || $participatedHackatons->isNotEmpty())
            <section class="card border border-base-300 bg-base-100">
                <div class="card-body gap-4">
                    <h2 class="card-title text-base">
                        <x-app-icon icon="heroicons:flag" class="h-5 w-5 text-primary" />
                        История хакатонов
                    </h2>

                    @php
                        $showSectionLabels = $profileUser->hackatons->isNotEmpty() && $participatedHackatons->isNotEmpty();
                    @endphp

                    @if ($profileUser->hackatons->isNotEmpty())
                        <div>
                            @if ($showSectionLabels)
                                <p class="mb-3 text-xs font-medium uppercase tracking-wide text-base-content/50">Организатор</p>
                            @endif
                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                @foreach ($profileUser->hackatons as $hackaton)
                                    <a href="{{ route('hackatons.show', $hackaton) }}" class="rounded-xl border border-base-300 p-3 transition hover:border-primary">
                                        <div class="mb-1 flex items-start justify-between gap-2">
                                            <p class="font-medium leading-snug">{{ $hackaton->title }}</p>
                                            <span class="badge badge-xs {{ $hackaton->status->badgeClass() }} shrink-0">
                                                {{ $hackaton->status->label() }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-base-content/60">
                                            {{ \Illuminate\Support\Carbon::parse($hackaton->start_at)->format('d.m.Y') }}
                                            –
                                            {{ \Illuminate\Support\Carbon::parse($hackaton->end_at)->format('d.m.Y') }}
                                        </p>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if ($participatedHackatons->isNotEmpty())
                        <div>
                            @if ($showSectionLabels)
                                <p class="mb-3 text-xs font-medium uppercase tracking-wide text-base-content/50">Участник</p>
                            @endif
                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                @foreach ($participatedHackatons as $hackaton)
                                    <a href="{{ route('hackatons.show', $hackaton) }}" class="rounded-xl border border-base-300 p-3 transition hover:border-primary">
                                        <div class="mb-1 flex items-start justify-between gap-2">
                                            <p class="font-medium leading-snug">{{ $hackaton->title }}</p>
                                            <span class="badge badge-xs {{ $hackaton->status->badgeClass() }} shrink-0">
                                                {{ $hackaton->status->label() }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-base-content/60">
                                            {{ \Illuminate\Support\Carbon::parse($hackaton->start_at)->format('d.m.Y') }}
                                            –
                                            {{ \Illuminate\Support\Carbon::parse($hackaton->end_at)->format('d.m.Y') }}
                                        </p>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </section>
        @else
            <section class="card border border-base-300 bg-base-100">
                <div class="card-body gap-4">
                    <h2 class="card-title text-base">
                        <x-app-icon icon="heroicons:flag" class="h-5 w-5 text-primary" />
                        История хакатонов
                    </h2>
                    <x-empty-state
                        embedded
                        title="Хакатонов нет"
                        description="Пользователь ещё не участвовал в хакатонах на платформе."
                        icon="heroicons:flag"
                    />
                </div>
            </section>
        @endif

        {{-- CERTIFICATES --}}
        <section class="card border border-base-300 bg-base-100">
            <div class="card-body gap-4">
                <h2 class="card-title text-base">
                    <x-app-icon icon="heroicons:academic-cap" class="h-5 w-5 text-primary" />
                    Сертификаты
                </h2>
                @if ($profileUser->certificates->isEmpty())
                    <x-empty-state
                        embedded
                        title="Сертификатов нет"
                        description="Сертификаты пока не опубликованы в профиле."
                        icon="heroicons:academic-cap"
                    />
                @else
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        @foreach ($profileUser->certificates as $certificate)
                            <div class="rounded-xl border border-base-300 p-3">
                                <p class="font-medium">{{ $certificate->title }}</p>
                                <p class="text-xs text-base-content/70">{{ $certificate->hackaton->title }}</p>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>
    </div>
