@extends('layouts.app')

@section('title', $profileUser->fio.' - Публичный профиль')
@section('meta_description', 'Публичный профиль участника '.$profileUser->fio.' на платформе Хакатонщик.')
@section('canonical_url', route('profile.public.show', ['user' => $profileUser->nickname]))
@section('og_title', $profileUser->fio.' - Публичный профиль')
@section('og_description', 'Профиль с командами, хакатонами и активностью пользователя '.$profileUser->fio.'.')

@section('slot')
    <div class="mx-auto w-full max-w-6xl space-y-6">
        <section class="rounded-3xl border border-base-300 bg-gradient-to-br from-base-100 via-base-100 to-primary/10 p-6 shadow-sm">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div class="space-y-2">
                    <span class="badge badge-primary badge-outline">{{ strtoupper($profileUser->role) }}</span>
                    <h1 class="text-3xl font-semibold tracking-tight">{{ $profileUser->fio }}</h1>
                    <p class="text-base-content/70">{{ '@'.$profileUser->nickname }}</p>
                    @if (filled($profileUser->description))
                        <p class="max-w-2xl text-sm text-base-content/80">{{ $profileUser->description }}</p>
                    @endif
                </div>
                <div class="flex flex-wrap gap-2">
                    @if ($profileUser->show_email_on_profile)
                        <a href="mailto:{{ $profileUser->email }}" class="btn btn-sm btn-primary">Email</a>
                    @endif
                    @if ($profileUser->show_phone_on_profile && filled($profileUser->phone))
                        <a href="tel:{{ $profileUser->phone }}" class="btn btn-sm btn-outline">Телефон</a>
                    @endif
                </div>
            </div>
        </section>

        <section class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <article class="card border border-base-300 bg-base-100 shadow-sm">
                <div class="card-body">
                    <p class="text-sm text-base-content/70">Команды</p>
                    <p class="text-3xl font-semibold">{{ $profileUser->teams->count() }}</p>
                </div>
            </article>
            <article class="card border border-base-300 bg-base-100 shadow-sm">
                <div class="card-body">
                    <p class="text-sm text-base-content/70">Хакатоны</p>
                    <p class="text-3xl font-semibold">{{ $profileUser->hackatons->count() }}</p>
                </div>
            </article>
            <article class="card border border-base-300 bg-base-100 shadow-sm">
                <div class="card-body">
                    <p class="text-sm text-base-content/70">Назначения судьей</p>
                    <p class="text-3xl font-semibold">{{ $profileUser->judgeAssignments->count() }}</p>
                </div>
            </article>
        </section>

        <section class="card border border-base-300 bg-base-100 shadow-sm">
            <div class="card-body">
                <h2 class="card-title">Последние команды</h2>
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

        <section class="card border border-base-300 bg-base-100 shadow-sm">
            <div class="card-body">
                <h2 class="card-title">Последние хакатоны</h2>
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
    </div>
@endsection
