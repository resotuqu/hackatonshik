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
        <section class="ui-page-header">
            <div class="flex flex-col gap-5 pb-5 md:flex-row md:items-start md:justify-between">
                <div class="flex flex-col items-start gap-5 sm:flex-row sm:items-center">
                    <div class="avatar shrink-0">
                        <div class="w-24 rounded-full ring-2 ring-base-300 ring-offset-2 ring-offset-base-100 sm:w-28">
                            <img src="{{ $avatarUrl }}" alt="Аватар {{ $profileUser->publicName() }}" />
                        </div>
                    </div>
                    <div class="space-y-1.5">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="badge badge-neutral badge-outline">{{ $publicRoleLabel }}</span>
                            @if ($profileUser->hasVerifiedContactChannels())
                                <span class="badge badge-success badge-outline gap-1">
                                    <x-app-icon icon="heroicons:shield-check" class="h-3.5 w-3.5" />
                                    Подтверждён
                                </span>
                            @endif
                            @if ($profileUser->open_to_teams)
                                <span class="badge badge-outline border-base-300 gap-1">
                                    <x-app-icon icon="heroicons:user-plus" class="h-3.5 w-3.5" />
                                    Открыт к командам
                                </span>
                            @endif
                        </div>
                        <h1 class="ui-heading-display text-2xl font-semibold sm:text-3xl">
                            {{ $profileUser->publicName() }}
                        </h1>
                        <p class="text-sm text-base-content/70">{{ '@'.$profileUser->nickname }}</p>
                        <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-base-content/70">
                            <span class="tabular-nums"><strong class="font-semibold text-base-content">{{ $hackatonsCount + $participatedHackatons->count() }}</strong> хакатонов</span>
                            <span class="tabular-nums"><strong class="font-semibold text-base-content">{{ $teamsCount }}</strong> команд</span>
                            @if ($allSkills->isNotEmpty())
                                <span class="tabular-nums"><strong class="font-semibold text-base-content">{{ $allSkills->count() }}</strong> навыков</span>
                            @endif
                            <span class="text-base-content/50">с <x-datetime :value="$profileUser->created_at" mode="date" class="font-medium" /></span>
                        </div>
                    </div>
                </div>

                <div class="shrink-0">
                    <button
                        class="btn btn-sm btn-outline gap-2"
                        type="button"
                        onclick="if (navigator.share) { navigator.share({ title: @json($profileUser->publicName()), url: @json($publicProfileUrl) }); } else { navigator.clipboard.writeText(@json($publicProfileUrl)); const t = this.querySelector('[data-share-label]'); if (t) t.textContent = 'Ссылка скопирована'; }"
                    >
                        <x-app-icon icon="heroicons:share" class="h-4 w-4 shrink-0" />
                        <span data-share-label>Поделиться</span>
                    </button>
                </div>
            </div>
        </section>

        {{-- Two-column layout --}}
        <div class="grid grid-cols-1 items-start gap-6 lg:grid-cols-3">

            {{-- Main column --}}
            <div class="space-y-6 lg:col-span-2">

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
                                                <span class="badge badge-outline border-base-300 text-base-content/80">{{ $skill }}</span>
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
                                            <a href="{{ route('hackatons.show', $hackaton) }}" class="rounded-xl border border-base-300 p-3 transition hover:border-primary/50">
                                                <div class="mb-1 flex items-start justify-between gap-2">
                                                    <p class="font-medium leading-snug">{{ $hackaton->title }}</p>
                                                    <span class="badge badge-xs {{ $hackaton->status->badgeClass() }} shrink-0">
                                                        {{ $hackaton->status->label() }}
                                                    </span>
                                                </div>
                                                <p class="text-xs text-base-content/50">
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
                                            <a href="{{ route('hackatons.show', $hackaton) }}" class="rounded-xl border border-base-300 p-3 transition hover:border-primary/50">
                                                <div class="mb-1 flex items-start justify-between gap-2">
                                                    <p class="font-medium leading-snug">{{ $hackaton->title }}</p>
                                                    <span class="badge badge-xs {{ $hackaton->status->badgeClass() }} shrink-0">
                                                        {{ $hackaton->status->label() }}
                                                    </span>
                                                </div>
                                                <p class="text-xs text-base-content/50">
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
            </div>

            {{-- Sidebar --}}
            <aside class="space-y-4 lg:sticky lg:top-20">

                {{-- Stats --}}
                <div class="card border border-base-300 bg-base-100">
                    <div class="card-body gap-3 p-4">
                        <dl class="grid grid-cols-3 divide-x divide-base-300 text-center">
                            <div class="px-3">
                                <dt class="text-xs text-base-content/50">Хакатоны</dt>
                                <dd class="mt-1 text-2xl font-semibold tabular-nums">{{ $hackatonsCount + $participatedHackatons->count() }}</dd>
                            </div>
                            <div class="px-3">
                                <dt class="text-xs text-base-content/50">Команды</dt>
                                <dd class="mt-1 text-2xl font-semibold tabular-nums">{{ $teamsCount }}</dd>
                            </div>
                            <div class="px-3">
                                <dt class="text-xs text-base-content/50">Навыки</dt>
                                <dd class="mt-1 text-2xl font-semibold tabular-nums">{{ $allSkills->count() }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                {{-- Contacts --}}
                @if ($profileUser->show_email_on_profile || ($profileUser->show_phone_on_profile && filled($profileUser->phone)))
                    <section class="card border border-base-300 bg-base-100">
                        <div class="card-body gap-3 p-4">
                            <h2 class="flex items-center gap-2 text-sm font-semibold">
                                <x-app-icon icon="heroicons:envelope" class="h-4 w-4 text-primary" />
                                Контакты
                            </h2>
                            @if ($profileUser->show_email_on_profile)
                                <a href="mailto:{{ $profileUser->email }}" class="link link-primary break-all text-sm font-medium">
                                    {{ $profileUser->email }}
                                </a>
                            @endif
                            @if ($profileUser->show_phone_on_profile && filled($profileUser->phone))
                                <a href="tel:{{ $profileUser->phone }}" class="link link-primary text-sm font-medium">
                                    {{ $profileUser->phone }}
                                </a>
                            @endif
                        </div>
                    </section>
                @endif

                {{-- Teams --}}
                <section class="card border border-base-300 bg-base-100">
                    <div class="card-body gap-3 p-4">
                        <h2 class="flex items-center gap-2 text-sm font-semibold">
                            <x-app-icon icon="heroicons:user-group" class="h-4 w-4 text-primary" />
                            Команды
                        </h2>
                        @if ($profileUser->teams->isEmpty())
                            <p class="text-sm text-base-content/50">Команд пока нет</p>
                        @else
                            <div class="flex flex-wrap gap-2">
                                @foreach ($profileUser->teams as $team)
                                    <a href="{{ route('teams.show', $team) }}" class="badge badge-outline border-base-300 text-base-content/80 hover:border-primary/50">{{ $team->title }}</a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </section>

                {{-- Certificates --}}
                <section class="card border border-base-300 bg-base-100">
                    <div class="card-body gap-3 p-4">
                        <h2 class="flex items-center gap-2 text-sm font-semibold">
                            <x-app-icon icon="heroicons:academic-cap" class="h-4 w-4 text-primary" />
                            Сертификаты
                        </h2>
                        @if ($profileUser->certificates->isEmpty())
                            <p class="text-sm text-base-content/50">Сертификатов пока нет</p>
                        @else
                            <div class="space-y-2">
                                @foreach ($profileUser->certificates as $certificate)
                                    <div class="rounded-lg border border-base-300 px-3 py-2">
                                        <p class="text-sm font-medium">{{ $certificate->title }}</p>
                                        <p class="text-xs text-base-content/70">{{ $certificate->hackaton->title }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </section>

            </aside>
        </div>
    </div>
