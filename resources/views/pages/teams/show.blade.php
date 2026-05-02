@extends('layouts.app')

@section('title', $team->title)

@section('slot')
    @php
        $teamImage = filled($team->image_url)
            ? (str_starts_with($team->image_url, 'http') ? $team->image_url : asset('storage/' . $team->image_url))
            : null;
        $openRoles = $team->roles->whereNull('user_id');
        $occupiedRoles = $team->roles->whereNotNull('user_id');
        $myApplicationsByRole = auth()->check()
            ? $team->applications->where('user_id', auth()->id())->keyBy('team_role_id')
            : collect();
        $isTeamMember =
            auth()->check() &&
            ($team->user_id === auth()->id() ||
                $occupiedRoles->contains(fn ($r) => (int) $r->user_id === (int) auth()->id()));
        $canHeroApply =
            auth()->check() && !auth()->user()->isOrganizer() && !$isTeamMember && $openRoles->isNotEmpty();
        $sectionCard = 'card bg-base-100 border border-base-200 shadow-sm';
        $heroApplyBtn = 'btn btn-outline btn-primary';
        $modalTriggerClass = 'btn btn-sm btn-outline btn-primary';
    @endphp

    <div class="mx-auto w-full max-w-7xl space-y-6">
        <nav class="flex flex-wrap items-center gap-1 text-sm" aria-label="Навигация по хлебным крошкам">
            <a href="/" class="link link-hover text-base-content/80">Главная</a>
            <x-app-icon icon="heroicons:chevron-right-20-solid" class="h-4 w-4 shrink-0 text-base-content/40" />
            <a href="/teams" class="link link-hover text-base-content/80">Команды</a>
            <x-app-icon icon="heroicons:chevron-right-20-solid" class="h-4 w-4 shrink-0 text-base-content/40" />
            <span class="max-w-[min(100%,12rem)] truncate text-base-content/50 sm:max-w-md" title="{{ $team->title }}">{{ $team->title }}</span>
        </nav>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="card overflow-hidden rounded-2xl border border-base-200 bg-base-100 shadow-sm lg:col-span-2">
                <figure class="aspect-video w-full bg-base-200">
                    @if ($teamImage)
                        <img src="{{ $teamImage }}" class="h-full w-full object-cover" alt="{{ $team->title }}">
                    @else
                        <div class="flex h-full min-h-40 flex-col items-center justify-center gap-2 px-4 text-center text-base-content/50">
                            <x-app-icon icon="heroicons:photo" class="h-16 w-16 opacity-40" />
                            <span class="text-sm">Превью не загружено</span>
                        </div>
                    @endif
                </figure>
                <div class="card-body">
                    <h1 class="card-title text-3xl">{{ $team->title }}</h1>
                    <div class="flex flex-wrap items-center gap-3">
                        @if ($canHeroApply)
                            <a href="#team-open-roles" class="{{ $heroApplyBtn }}">Подать заявку</a>
                        @endif
                        @guest
                            <a href="/login" class="btn btn-ghost btn-sm border border-base-300/80">Войти</a>
                        @endguest
                    </div>
                    <div class="prose prose-sm mt-4 max-w-none sm:prose-base">
                        {!! \Illuminate\Support\Str::markdown($team->description ?? 'Описание отсутствует.') !!}
                    </div>
                </div>
            </div>

            {{-- Информация о команде: compact + mary-stat --}}
            <div class="{{ $sectionCard }}">
                <div class="card-body gap-3 p-4 sm:p-5">
                    <h2 class="card-title text-base">О команде</h2>
                    <div class="grid grid-cols-2 gap-2">
                        <div class="flex flex-col gap-1 rounded-xl border border-base-300/50 bg-base-200/30 p-3">
                            <div class="flex items-center gap-2 text-base-content/70">
                                <x-app-icon icon="heroicons:user-group" class="h-5 w-5 shrink-0 text-primary" />
                                <span class="text-xs font-medium uppercase tracking-wide">Ролей</span>
                            </div>
                            <span class="text-xl font-semibold tabular-nums">{{ $team->roles->count() }}</span>
                        </div>
                        <div class="flex flex-col gap-1 rounded-xl border border-base-300/50 bg-base-200/30 p-3">
                            <div class="flex items-center gap-2 text-base-content/70">
                                <x-app-icon icon="heroicons:megaphone" class="h-5 w-5 shrink-0 text-primary" />
                                <span class="text-xs font-medium uppercase tracking-wide">Открыто</span>
                            </div>
                            <span class="text-xl font-semibold tabular-nums">{{ $openRoles->count() }}</span>
                        </div>
                    </div>
                    <div class="flex gap-2 text-sm">
                        <x-app-icon icon="heroicons:user-circle" class="h-5 w-5 shrink-0 text-primary/80" />
                        <div>
                            <span class="text-base-content/60">Капитан</span>
                            <p class="font-medium leading-tight">{{ $team->user->nickname ?? $team->user->name ?? $team->user->email }}</p>
                        </div>
                    </div>
                    @if ($team->hackaton)
                        <div class="flex gap-2 text-sm">
                            <x-app-icon icon="heroicons:map-pin" class="h-5 w-5 shrink-0 text-primary/80" />
                            <div>
                                <span class="text-base-content/60">Хакатон</span>
                                <p class="leading-tight">
                                    <a class="link link-primary font-medium" href="{{ route('hackatons.show', $team->hackaton) }}">{{ $team->hackaton->title }}</a>
                                </p>
                            </div>
                        </div>
                    @endif
                    @if ($team->socialLinks->isNotEmpty())
                        <div class="divider my-0"></div>
                        <p class="text-xs font-medium uppercase tracking-wide text-base-content/50">Ссылки</p>
                        <ul class="space-y-1.5 text-sm">
                            @foreach ($team->socialLinks as $link)
                                <li>
                                    <a href="{{ $link->url }}" target="_blank" rel="noopener noreferrer" class="link link-hover link-primary">
                                        {{ $link->name }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>

        <div id="team-open-roles" class="scroll-mt-24">
            <div class="{{ $sectionCard }}">
                <div class="card-body">
                    <h2 class="card-title text-xl">Открытые роли</h2>
                    @forelse ($openRoles as $role)
                        <div
                            class="mt-4 space-y-3 rounded-2xl border border-base-300/60 bg-base-100/50 p-4 shadow-sm first:mt-0 sm:p-5">
                            <div class="flex flex-wrap items-start justify-between gap-3">
                                <div class="min-w-0 space-y-2">
                                    <p class="text-lg font-semibold leading-tight">{{ $role->title }}</p>
                                    <div class="flex flex-wrap items-center gap-2">
                                        @if ($role->role)
                                            <x-marybadge :value="$role->role->name" class="badge-primary badge-soft" />
                                        @endif
                                    </div>
                                </div>
                                @auth
                                    @if ($team->user_id !== auth()->id())
                                        @php
                                            $myApplication = $myApplicationsByRole->get($role->id);
                                        @endphp

                                        @if (auth()->user()->isOrganizer())
                                            <span class="badge badge-warning">Организаторы не могут откликаться</span>
                                        @elseif ($myApplication)
                                            <div class="flex flex-col items-end gap-2">
                                                <span class="badge badge-{{ $myApplication->status->isAccepted() ? 'success' : ($myApplication->status->isRejected() ? 'error' : 'warning') }}">
                                                    Заявка: {{ $myApplication->status->label() }}
                                                </span>
                                                @if ($myApplication->status->isPending())
                                                    <form method="POST" action="{{ route('team.applications.destroy', $myApplication) }}"
                                                        onsubmit="return confirm('Отменить поданную заявку?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="btn btn-xs btn-ghost" type="submit">Отменить заявку</button>
                                                    </form>
                                                @endif
                                            </div>
                                        @else
                                            <x-application-modal
                                                type="team"
                                                :id="$role->id"
                                                title="Подать заявку на роль"
                                                trigger-label="Откликнуться"
                                                :trigger-class="$modalTriggerClass"
                                                action="{{ route('team.applications.store') }}" />
                                        @endif
                                    @endif
                                @endauth
                            </div>
                            <div class="prose max-w-none prose-sm">
                                {!! \Illuminate\Support\Str::markdown($role->description ?? 'Описание роли отсутствует.') !!}
                            </div>
                            @if ($role->skills->isNotEmpty())
                                <div class="flex flex-wrap gap-2">
                                    @foreach ($role->skills as $skill)
                                        <x-marybadge :value="$skill->name" class="badge-outline badge-sm" />
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="mt-4 flex flex-col items-center justify-center gap-3 rounded-2xl border border-dashed border-base-300/80 bg-base-200/20 py-12 text-center">
                            <x-app-icon icon="heroicons:archive-box" class="h-14 w-14 text-base-content/30" />
                            <p class="text-base-content/60">Сейчас нет открытых ролей.</p>
                        </div>
                    @endforelse

                    @guest
                        <div class="alert mt-4 border border-base-300/60 bg-base-200/30">
                            <span>Чтобы откликнуться на роль, <a class="link link-primary" href="/login">войдите в аккаунт</a>.</span>
                        </div>
                    @endguest
                </div>
            </div>
        </div>

        {{-- Участники: публичный grid --}}
        <div class="{{ $sectionCard }}">
            <div class="card-body">
                <h2 class="card-title text-xl">Участники</h2>
                @if ($occupiedRoles->isEmpty())
                    <div class="mt-2 flex flex-col items-center justify-center gap-3 rounded-2xl border border-dashed border-base-300/80 bg-base-200/20 py-12 text-center">
                        <x-app-icon icon="heroicons:user-group" class="h-14 w-14 text-base-content/30" />
                        <p class="text-base-content/60">В команде пока никого нет.</p>
                    </div>
                @else
                    <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($occupiedRoles as $role)
                            @php
                                $u = $role->user;
                                $displayName = $u->fio ?? $u->nickname ?? $u->email;
                                $initials = filled($u->fio)
                                    ? $u->initials()
                                    : mb_strtoupper(mb_substr((string) ($u->nickname ?? $u->email), 0, 2));
                            @endphp
                            <div
                                class="group relative flex flex-col gap-3 rounded-2xl border border-base-300/50 bg-base-100/60 p-4 shadow-sm transition duration-200 hover:scale-[1.02] hover:border-base-300 hover:shadow-md">
                                <div class="flex items-start gap-3">
                                    <div class="avatar placeholder">
                                        <div class="w-14 rounded-full bg-neutral text-neutral-content ring-2 ring-base-100 transition group-hover:ring-base-300">
                                            <span class="text-lg">{{ $initials ?: '?' }}</span>
                                        </div>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate font-semibold leading-snug" title="{{ $displayName }}">{{ $displayName }}</p>
                                        <p class="text-sm text-base-content/65">{{ $role->title }}</p>
                                    </div>
                                </div>
                                @if (auth()->check() && $team->user_id === auth()->id())
                                    <form method="POST" action="{{ route('teams.participants.destroy', ['team' => $team, 'teamRole' => $role]) }}"
                                        class="mt-auto border-t border-base-200/80 pt-3"
                                        onsubmit="return confirm('Удалить участника из команды?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-error btn-xs btn-block" type="submit">Удалить из команды</button>
                                    </form>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        @if ($team->user_id === auth()->id())
            <div class="{{ $sectionCard }}">
                <div class="card-body">
                    <h2 class="card-title text-xl">Заявки на вступление</h2>

                    @if ($team->applications->isEmpty())
                        <div class="mt-2 flex flex-col items-center justify-center gap-3 rounded-2xl border border-dashed border-base-300/80 bg-base-200/20 py-10 text-center">
                            <x-app-icon icon="heroicons:document-text" class="h-12 w-12 text-base-content/30" />
                            <p class="text-base-content/60">Пока нет заявок.</p>
                        </div>
                    @else
                        <div class="mt-2 overflow-x-auto">
                            <table class="table table-zebra">
                                <thead>
                                    <tr>
                                        <th>Пользователь</th>
                                        <th>Роль</th>
                                        <th>Сообщение</th>
                                        <th>Отправлена</th>
                                        <th>Статус</th>
                                        <th>Рассмотрел</th>
                                        <th>Рассмотрена</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($team->applications as $app)
                                        <tr>
                                            <td>{{ $app->user->fio ?? $app->user->nickname ?? $app->user->email }}</td>
                                            <td>{{ $app->teamRole->title }}</td>
                                            <td class="max-w-xs truncate">{{ $app->message }}</td>
                                            <td>{{ $app->created_at?->format('d.m.Y H:i') ?? '—' }}</td>
                                            <td>
                                                <span
                                                    class="badge badge-{{ $app->status->isAccepted() ? 'success' : ($app->status->isRejected() ? 'error' : 'warning') }}">
                                                    {{ $app->status->label() }}
                                                </span>
                                            </td>
                                            <td>{{ $app->reviewer?->nickname ?? $app->reviewer?->name ?? '—' }}</td>
                                            <td>{{ $app->reviewed_at?->format('d.m.Y H:i') ?? '—' }}</td>
                                            <td>
                                                @if ($app->status->isPending())
                                                    <form method="POST" action="{{ route('team.applications.update', $app) }}" class="inline-flex">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="status" value="accepted">
                                                        <button class="btn btn-success btn-xs" type="submit">Принять</button>
                                                    </form>

                                                    <form method="POST" action="{{ route('team.applications.update', $app) }}"
                                                        class="ml-2 inline-flex"
                                                        onsubmit="return confirm('Отклонить заявку? Пользователь останется без роли.');">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="status" value="rejected">
                                                        <button class="btn btn-error btn-xs" type="submit">Отклонить</button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
@endsection
