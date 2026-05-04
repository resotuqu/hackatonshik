@extends('layouts.app')

@section('title', $profileUser->fio.' - Публичный профиль')
@section('meta_description', 'Публичный профиль участника '.$profileUser->fio.' на платформе Хакатонщик.')
@section('canonical_url', route('profile.public.show', ['user' => $profileUser->nickname]))
@section('og_title', $profileUser->fio.' - Публичный профиль')
@section('og_description', 'Профиль с командами, хакатонами и активностью пользователя '.$profileUser->fio.'.')
@section('og_image', $profileUser->avatar_path ? asset('storage/'.$profileUser->avatar_path) : url('/logo.svg'))

@section('slot')
    @php
        $publicRoleLabel = $profileUser->role === 'user'
            ? 'Участник'
            : ($profileUser->role === 'partner' ? 'Партнёр' : ($profileUser->role === 'judge' ? 'Судья' : 'Администратор'));
        $avatarUrl = $profileUser->avatar_path
            ? asset('storage/'.$profileUser->avatar_path)
            : 'https://ui-avatars.com/api/?name='.urlencode($profileUser->fio).'&background=random';
        $teamsCount = $profileUser->teams->count();
        $hackatonsCount = $profileUser->hackatons->count();
        $skills = $profileUser->teamRoles->flatMap(fn ($role) => $role->skills->pluck('name'))->unique()->values();
        $publicProfileUrl = route('profile.public.show', ['user' => $profileUser->nickname]);
    @endphp

    <div class="mx-auto w-full max-w-6xl space-y-6">
        <nav class="text-sm breadcrumbs">
            <ul>
                <li><a href="/">Главная</a></li>
                <li class="opacity-70">Публичный профиль</li>
            </ul>
        </nav>

        {{-- HERO --}}
        <section class="relative overflow-hidden rounded-3xl border border-base-300 bg-linear-to-br from-base-100 via-base-100 to-primary/15 p-6 shadow-sm lg:p-8">
            <div class="pointer-events-none absolute -top-20 -right-16 h-56 w-56 rounded-full bg-secondary/10 blur-3xl"></div>
            <div class="pointer-events-none absolute -bottom-24 -left-16 h-64 w-64 rounded-full bg-primary/10 blur-3xl"></div>

            <div class="relative flex flex-col gap-6 md:flex-row md:items-start md:justify-between">
                <div class="flex flex-col items-start gap-5 sm:flex-row sm:items-center">
                    <div class="avatar">
                        <div class="w-32 rounded-full ring-2 ring-secondary/40 ring-offset-2 ring-offset-base-100 sm:w-36">
                            <img src="{{ $avatarUrl }}" alt="Аватар {{ $profileUser->fio }}" />
                        </div>
                    </div>
                    <div class="space-y-2">
                        <span class="badge badge-primary badge-outline">{{ $publicRoleLabel }}</span>
                        <h1 class="font-display text-3xl font-semibold tracking-tight lg:text-4xl">
                            {{ $profileUser->fio }}
                        </h1>
                        <p class="text-base text-base-content/70">{{ '@'.$profileUser->nickname }}</p>
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
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2 sm:justify-end">
                    <button
                        class="btn btn-sm btn-outline btn-secondary gap-2"
                        type="button"
                        onclick="if (navigator.share) { navigator.share({ title: @json($profileUser->fio), url: @json($publicProfileUrl) }); } else { navigator.clipboard.writeText(@json($publicProfileUrl)); const t = this.querySelector('[data-share-label]'); if (t) t.textContent = 'Ссылка скопирована'; }"
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
                    <div class="prose prose-sm max-w-none text-base-content">
                        {!! \Illuminate\Support\Str::markdown($profileUser->description) !!}
                    </div>
                </div>
            </section>
        @endif

        <section class="card border border-base-300 bg-base-100">
            <div class="card-body gap-4">
                <h2 class="card-title text-base">
                    <x-app-icon icon="heroicons:sparkles" class="h-5 w-5 text-primary" />
                    Навыки и роли
                </h2>
                @if ($skills->isEmpty())
                    <p class="text-sm text-base-content/70">Пользователь пока не добавил навыки.</p>
                @else
                    <div class="flex flex-wrap gap-2">
                        @foreach ($skills as $skill)
                            <span class="badge badge-ghost">{{ $skill }}</span>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>

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
                    <p class="text-3xl font-semibold text-secondary">{{ $hackatonsCount }}</p>
                </div>
            </article>
            <article class="card border border-base-300 bg-base-100">
                <div class="card-body gap-2">
                    <p class="flex items-center gap-2 text-sm text-base-content/70">
                        <x-app-icon icon="heroicons:scale" class="h-4 w-4 text-secondary" />
                        Назначения судьей
                    </p>
                    <p class="text-3xl font-semibold text-secondary">{{ $profileUser->judgeAssignments->count() }}</p>
                </div>
            </article>
        </section>

        <section class="card border border-base-300 bg-base-100">
            <div class="card-body gap-4">
                <h2 class="card-title text-base">
                    <x-app-icon icon="heroicons:user-group" class="h-5 w-5 text-primary" />
                    Последние команды
                </h2>
                @if ($profileUser->teams->isEmpty())
                    <p class="text-sm text-base-content/70">Публичных команд пока нет.</p>
                @else
                    <div class="flex flex-wrap gap-2">
                        @foreach ($profileUser->teams as $team)
                            <a href="{{ route('teams.show', $team) }}" class="badge badge-lg badge-outline">{{ $team->title }}</a>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>

        <section class="card border border-base-300 bg-base-100">
            <div class="card-body gap-4">
                <h2 class="card-title text-base">
                    <x-app-icon icon="heroicons:flag" class="h-5 w-5 text-primary" />
                    Последние хакатоны
                </h2>
                @if ($profileUser->hackatons->isEmpty())
                    <p class="text-sm text-base-content/70">Пока нет созданных хакатонов.</p>
                @else
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        @foreach ($profileUser->hackatons as $hackaton)
                            <a href="{{ route('hackatons.show', $hackaton) }}" class="rounded-xl border border-base-300 p-3 transition hover:border-primary">
                                <p class="font-medium">{{ $hackaton->title }}</p>
                                <p class="text-xs text-base-content/70">
                                    {{ \Illuminate\Support\Carbon::parse($hackaton->start_at)->format('d.m.Y') }}
                                    -
                                    {{ \Illuminate\Support\Carbon::parse($hackaton->end_at)->format('d.m.Y') }}
                                </p>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>

        <section class="card border border-base-300 bg-base-100">
            <div class="card-body gap-4">
                <h2 class="card-title text-base">
                    <x-app-icon icon="heroicons:academic-cap" class="h-5 w-5 text-primary" />
                    Сертификаты
                </h2>
                @if ($profileUser->certificates->isEmpty())
                    <p class="text-sm text-base-content/70">Сертификаты пока не опубликованы.</p>
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
@endsection
