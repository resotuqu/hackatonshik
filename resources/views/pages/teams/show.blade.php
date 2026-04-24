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
    @endphp

    <div class="mx-auto w-full max-w-7xl space-y-6">
        <div class="text-sm breadcrumbs">
            <ul>
                <li><a href="/">Главная</a></li>
                <li><a href="/teams">Команды</a></li>
                <li class="opacity-70">{{ $team->title }}</li>
            </ul>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 card bg-base-100 border border-base-200 shadow-sm">
                <figure class="aspect-video bg-base-200">
                    @if ($teamImage)
                        <img src="{{ $teamImage }}" class="h-full w-full object-cover" alt="{{ $team->title }}">
                    @else
                        <div class="flex h-full w-full items-center justify-center text-base-content/60">Изображение команды отсутствует</div>
                    @endif
                </figure>
                <div class="card-body">
                    <h1 class="card-title text-3xl">{{ $team->title }}</h1>
                    <div class="prose max-w-none prose-sm sm:prose-base">
                        {!! \Illuminate\Support\Str::markdown($team->description ?? 'Описание отсутствует.') !!}
                    </div>
                </div>
            </div>

            <div class="card bg-base-100 border border-base-200 shadow-sm">
                <div class="card-body space-y-3">
                    <h2 class="card-title text-lg">Информация о команде</h2>
                    <p class="text-sm">Капитан: <span class="font-medium">{{ $team->user->nickname ?? $team->user->name ?? $team->user->email }}</span></p>
                    @if ($team->hackaton)
                        <p class="text-sm">Хакатон: <a class="link link-primary" href="{{ route('hackatons.show', $team->hackaton) }}">{{ $team->hackaton->title }}</a></p>
                    @endif
                    <p class="text-sm">Ролей: <span class="font-medium">{{ $team->roles->count() }}</span></p>
                    <p class="text-sm">Открыто ролей: <span class="font-medium">{{ $openRoles->count() }}</span></p>
                    @if ($team->socialLinks->isNotEmpty())
                        <div class="divider my-1"></div>
                        <p class="text-sm font-medium">Социальные ссылки</p>
                        <ul class="space-y-1 text-sm">
                            @foreach ($team->socialLinks as $link)
                                <li>
                                    <a href="{{ $link->url }}" target="_blank" rel="noopener noreferrer" class="link link-hover">
                                        {{ $link->name }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>

        <div class="card bg-base-100 border border-base-200 shadow-sm">
            <div class="card-body">
                <h2 class="card-title text-xl">Открытые роли и заявки</h2>
                @forelse ($openRoles as $role)
                    <div class="rounded-xl border border-base-300 p-4 space-y-3">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div>
                                <p class="font-semibold">{{ $role->title }}</p>
                                @if ($role->role)
                                    <p class="text-sm text-base-content/70">Категория: {{ $role->role->name }}</p>
                                @endif
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
                                                    <button class="btn btn-xs btn-ghost">Отменить заявку</button>
                                                </form>
                                            @endif
                                        </div>
                                    @else
                                        <x-application-modal type="team" :id="$role->id" title="Подать заявку на роль"
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
                                    <span class="badge badge-outline">{{ $skill->name }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @empty
                    <p class="text-base-content/60">Сейчас нет открытых ролей.</p>
                @endforelse

                @guest
                    <div class="alert mt-4">
                        <span>Чтобы подать заявку на роль, <a class="link link-primary" href="/login">войдите в аккаунт</a>.</span>
                    </div>
                @endguest
            </div>
        </div>

        @if ($team->user_id === auth()->id())
            <div class="card bg-base-100 border border-base-200 shadow-sm">
                <div class="card-body">
                    <h2 class="card-title text-xl">Участники команды</h2>

                    @if ($occupiedRoles->isEmpty())
                        <p class="text-base-content/60">В команде пока нет участников.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Участник</th>
                                        <th>Роль</th>
                                        <th>Email</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($occupiedRoles as $role)
                                        <tr>
                                            <td>{{ $role->user->fio ?? $role->user->nickname ?? $role->user->email }}</td>
                                            <td>{{ $role->title }}</td>
                                            <td>{{ $role->user->email ?? '—' }}</td>
                                            <td>
                                                <form method="POST"
                                                    action="{{ route('teams.participants.destroy', ['team' => $team, 'teamRole' => $role]) }}"
                                                    onsubmit="return confirm('Удалить участника из команды?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-error btn-xs">Удалить из команды</button>
                                                </form>
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

        @if ($team->user_id === auth()->id())
            <div class="card bg-base-100 border border-base-200 shadow-sm">
                <div class="card-body">
                    <h2 class="card-title text-xl">Заявки на вступление</h2>

                    @if ($team->applications->isEmpty())
                        <p class="text-base-content/60">Пока нет заявок.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="table">
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
                                                        <button class="btn btn-success btn-xs">Принять</button>
                                                    </form>

                                                    <form method="POST" action="{{ route('team.applications.update', $app) }}"
                                                        class="inline-flex ml-2"
                                                        onsubmit="return confirm('Отклонить заявку? Пользователь останется без роли.');">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="status" value="rejected">
                                                        <button class="btn btn-error btn-xs">Отклонить</button>
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
