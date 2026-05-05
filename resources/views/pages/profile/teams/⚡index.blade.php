<?php

use App\Enums\ApplicationStatus;
use App\Models\Team;
use App\Models\TeamApplication;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Mary\Traits\Toast;

new #[Layout('layouts::app', ['title' => 'Мои команды'])]
    class extends Component
    {
        use Toast;

        #[Computed]
        public function teams()
        {
            return Team::query()
                ->where('user_id', Auth::id())
                ->with(['hackaton', 'roles'])
                ->withCount([
                    'roles as roles_count',
                    'roles as empty_roles_count' => fn ($q) => $q->whereNull('user_id'),
                ])
                ->get();
        }

        /**
         * @return Collection<int, TeamApplication>
         */
        #[Computed]
        public function pendingTeamRoleApplications()
        {
            return TeamApplication::query()
                ->where('user_id', Auth::id())
                ->where('status', ApplicationStatus::PENDING)
                ->with(['teamRole.team.hackaton', 'teamRole.role'])
                ->latest()
                ->get();
        }

        public $deleteTeamModal = false;

        public $deleteTeamId = null;

        public $deleteTeamTitle = null;

        public function showDeleteTeamModal($team_id): void
        {
            $team = Team::query()->where('id', $team_id)->where('user_id', Auth::id())->first();
            if (! $team) {
                return;
            }
            $this->deleteTeamId = $team->id;
            $this->deleteTeamTitle = $team->title;
            $this->deleteTeamModal = true;
        }

        public function deleteTeam(): void
        {
            if (! $this->deleteTeamId) {
                return;
            }
            $team = Team::query()->where('id', $this->deleteTeamId)->where('user_id', Auth::id())->first();
            if ($team) {
                $team->delete();
            }
            $this->deleteTeamId = null;
            $this->deleteTeamTitle = null;
            $this->deleteTeamModal = false;
        }

        public function editTeam($id)
        {
            return redirect('/teams/'.$id.'/edit');
        }

        public function updatedDeleteTeamModal(mixed $value): void
        {
            if (! $value) {
                $this->deleteTeamId = null;
                $this->deleteTeamTitle = null;
            }
        }
    };
?>

<div class="mx-auto max-w-7xl space-y-8">
    <div wire:loading class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3" aria-busy="true" aria-label="Загрузка команд">
        @foreach (range(1, 6) as $_)
            <x-team-card-skeleton />
        @endforeach
    </div>

    <div wire:loading.remove>
    <nav class="text-sm breadcrumbs" aria-label="Навигация по разделам">
        <ul>
            <li><a href="/" class="link link-hover focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 focus-visible:ring-offset-base-100">Главная</a></li>
            <li><a href="/profile" class="link link-hover focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 focus-visible:ring-offset-base-100">Профиль</a></li>
            <li class="opacity-70">Мои команды</li>
        </ul>
    </nav>

    <section class="ui-page-hero" aria-labelledby="profile-teams-hero-heading">
        <div class="pointer-events-none absolute inset-0 opacity-60" aria-hidden="true">
            <div class="absolute -top-24 -right-16 h-64 w-64 rounded-full bg-secondary/30 blur-3xl"></div>
            <div class="absolute -bottom-24 -left-16 h-72 w-72 rounded-full bg-primary/25 blur-3xl"></div>
            <div class="absolute top-1/2 left-1/3 h-40 w-40 -translate-y-1/2 rounded-full bg-accent/20 blur-3xl"></div>
        </div>

        <div class="relative flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
            <div class="min-w-0 space-y-3">
                <div class="inline-flex items-center gap-2 rounded-full border border-secondary/30 bg-secondary/10 px-3 py-1 text-xs font-bold uppercase tracking-widest text-secondary">
                    <x-app-icon icon="heroicons:user-group" class="h-3.5 w-3.5" />
                    Мои команды
                </div>
                <h1 id="profile-teams-hero-heading" class="ui-heading-display text-3xl font-black sm:text-4xl lg:text-5xl">
                    <span class="bg-linear-to-r from-secondary via-accent to-primary bg-clip-text text-transparent">Ваши команды</span>
                </h1>
                <p class="max-w-2xl text-base text-base-content/70">
                    Управляйте составом, вакансиями и заявками — всё в одном месте.
                </p>
            </div>
            <div class="flex shrink-0 flex-col gap-3 sm:flex-row sm:items-center">
                <a
                    href="/teams/create"
                    wire:navigate
                    class="ui-cta-primary"
                >
                    <x-app-icon icon="heroicons:plus-circle" class="h-5 w-5" />
                    Создать команду
                </a>
            </div>
        </div>
    </section>

    <section
        id="pending-team-role-applications"
        class="ui-surface-soft-muted px-5 py-6 sm:px-7 sm:py-7"
        aria-labelledby="pending-apps-heading"
    >
        <div class="pointer-events-none absolute inset-0 opacity-40" aria-hidden="true">
            <div class="absolute -right-8 top-0 h-32 w-32 rounded-full bg-primary/20 blur-2xl"></div>
            <div class="absolute -left-4 bottom-0 h-28 w-28 rounded-full bg-accent/15 blur-2xl"></div>
        </div>
        <div class="relative">
            <h2 id="pending-apps-heading" class="ui-heading-display text-lg font-bold sm:text-xl">
                Заявки на роли в командах
            </h2>
            @if ($this->pendingTeamRoleApplications->isEmpty())
                <p class="mt-3 text-sm text-base-content/70">Нет заявок на рассмотрении.</p>
            @else
                <ul class="mt-4 space-y-3">
                    @foreach ($this->pendingTeamRoleApplications as $application)
                        @php
                            $appTeam = $application->teamRole?->team;
                            $role = $application->teamRole?->role;
                            $hackatonTitle = $appTeam?->hackaton?->title;
                        @endphp
                        <li class="ui-surface-card bg-base-100/80 p-4 text-sm backdrop-blur-sm">
                            <p>
                                <span class="text-base-content/60">Команда:</span>
                                @if ($appTeam)
                                    <a
                                        href="{{ url('/teams/'.$appTeam->id) }}"
                                        wire:navigate
                                        class="link link-primary font-semibold focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 focus-visible:ring-offset-base-100"
                                    >{{ $appTeam->title }}</a>
                                @else
                                    <span class="font-medium">—</span>
                                @endif
                            </p>
                            @if ($role)
                                <p class="mt-1.5"><span class="text-base-content/60">Роль:</span> {{ $role->name }}</p>
                            @elseif ($application->teamRole?->title)
                                <p class="mt-1.5"><span class="text-base-content/60">Роль:</span> {{ $application->teamRole->title }}</p>
                            @endif
                            @if ($hackatonTitle)
                                <p class="mt-1.5"><span class="text-base-content/60">Хакатон:</span> {{ $hackatonTitle }}</p>
                            @endif
                            <p class="mt-3">
                                <span class="badge badge-warning badge-sm border-0 font-semibold">{{ $application->status->label() }}</span>
                            </p>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </section>

    <x-mary-modal wire:model="deleteTeamModal" title="Удаление команды" class="backdrop-blur">
        <p class="text-base-content/90">
            @if ($deleteTeamTitle)
                Вы действительно хотите удалить команду «<span class="font-semibold text-base-content">{{ $deleteTeamTitle }}</span>»? Это действие необратимо.
            @else
                Вы действительно хотите удалить команду? Это действие необратимо.
            @endif
        </p>

        <x-slot:actions>
            <x-mary-button class="btn-error" label="Удалить" wire:click="deleteTeam" />
            <x-mary-button class="btn-ghost" label="Отмена" @click="$wire.deleteTeamModal = false" />
        </x-slot:actions>
    </x-mary-modal>

    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($this->teams as $team)
            @php
                $titleId = 'my-team-card-title-'.$team->id;
                $hackaton = $team->hackaton;
                $openSlots = (int) ($team->empty_roles_count ?? 0);
            @endphp
            <article
                wire:key="my-team-{{ $team->id }}"
                class="ui-surface-card ui-surface-card--hover ui-surface-card--team group/card flex h-full flex-col motion-safe:animate-card-enter"
                style="animation-delay: {{ ($loop->index % 9) * 40 }}ms;"
                aria-labelledby="{{ $titleId }}"
            >
                <x-team-cover
                    :title="$team->title"
                    :cover-url="$team->coverImagePublicUrl()"
                    :initials="$team->initialsForCover()"
                    :show-recruiting-badge="$openSlots > 0"
                    :hackaton-title="$hackaton?->title"
                    :show-brand-strip="true"
                />

                <div class="flex min-h-0 flex-1 flex-col gap-4 p-4 sm:p-5">
                    <h2 id="{{ $titleId }}" class="ui-heading-display text-2xl font-black leading-tight sm:text-3xl">
                        {{ $team->title }}
                    </h2>

                    @if ($hackaton)
                        <div class="ui-stat-tile rounded-2xl p-4">
                            <div class="flex items-start gap-3">
                                <span class="mt-0.5 flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-primary/15 text-primary">
                                    <x-app-icon icon="heroicons:calendar-days" class="h-5 w-5" />
                                </span>
                                <div class="min-w-0 space-y-1">
                                    <p class="text-xs font-bold uppercase tracking-wide text-base-content/50">Хакатон</p>
                                    <p class="ui-heading-display text-base font-bold leading-snug text-base-content sm:text-lg">
                                        {{ $hackaton->title }}
                                    </p>
                                    <p class="text-sm tabular-nums text-base-content/70">
                                        {{ \Illuminate\Support\Carbon::parse($hackaton->start_at)->format('d.m.Y H:i') }}
                                        <span class="mx-1 text-base-content/40" aria-hidden="true">→</span>
                                        {{ \Illuminate\Support\Carbon::parse($hackaton->end_at)->format('d.m.Y H:i') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="grid grid-cols-2 gap-3 rounded-2xl border border-primary/20 bg-primary/5 p-3">
                        <div class="text-center sm:text-left">
                            <p class="ui-heading-display text-2xl font-black tabular-nums text-primary sm:text-3xl">{{ (int) ($team->roles_count ?? $team->roles->count()) }}</p>
                            <p class="text-[11px] font-semibold uppercase tracking-wide text-base-content/60">Ролей</p>
                        </div>
                        <div class="text-center sm:text-left">
                            <p class="ui-heading-display text-2xl font-black tabular-nums text-secondary sm:text-3xl">{{ $openSlots }}</p>
                            <p class="text-[11px] font-semibold uppercase tracking-wide text-base-content/60">Свободно</p>
                        </div>
                    </div>

                    <div class="mt-auto flex flex-col gap-2 pt-1 sm:flex-row sm:flex-wrap">
                        <a
                            href="{{ route('teams.show', $team) }}"
                            wire:navigate
                            class="ui-cta-primary order-first w-full gap-2 sm:order-none sm:flex-1"
                        >
                            <x-app-icon icon="heroicons:eye" class="h-4 w-4" />
                            Просмотреть
                        </a>
                        <button
                            type="button"
                            class="ui-cta-outline w-full border-base-300 sm:flex-1"
                            wire:click="editTeam({{ $team->id }})"
                        >
                            <x-app-icon icon="heroicons:pencil-square" class="h-4 w-4" />
                            Изменить
                        </button>
                        <button
                            type="button"
                            class="ui-cta-danger w-full sm:flex-1"
                            wire:click="showDeleteTeamModal({{ $team->id }})"
                        >
                            <x-app-icon icon="heroicons:trash" class="h-4 w-4" />
                            Удалить
                        </button>
                    </div>
                </div>
            </article>
        @empty
            <div class="col-span-full">
                <x-team-empty-state
                    title="Пока нет команд"
                    description="Создайте команду для хакатона — настройте роли и пригласите участников через каталог."
                    icon="heroicons:user-group"
                    action-href="/teams/create"
                    action-label="Создать команду"
                />
            </div>
        @endforelse
    </div>
</div>
    </div>
