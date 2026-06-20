<div class="mx-auto w-full max-w-6xl space-y-10 pb-12">

    {{-- Loading skeletons --}}
    <div wire:loading class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3" aria-busy="true">
        @foreach (range(1, 6) as $_)
            <x-team-card-skeleton />
        @endforeach
    </div>

    <div wire:loading.remove class="space-y-10">

        {{-- Breadcrumbs --}}
        <nav class="text-sm breadcrumbs" aria-label="Навигация">
            <ul>
                <li><a href="/" class="link link-hover">Главная</a></li>
                <li><a href="/profile" class="link link-hover">Профиль</a></li>
                <li class="opacity-70">Мои команды</li>
            </ul>
        </nav>

        {{-- Header --}}
        <x-page-header
            title="Ваши команды"
            description="Управляйте составом, вакансиями и заявками в одном месте."
        >
            <x-slot:actions>
                <a href="/teams/create" wire:navigate class="btn btn-neutral btn-sm shrink-0 gap-2">
                    <x-app-icon icon="heroicons:plus" class="h-4 w-4" />
                    Создать команду
                </a>
            </x-slot:actions>
        </x-page-header>

        {{-- Заявки на роли --}}
        <section class="card border border-base-300 bg-base-100 p-6 sm:p-8" aria-labelledby="pending-apps-heading">
            <h2 id="pending-apps-heading" class="text-2xl font-semibold mb-6">Заявки на роли в командах</h2>

            @if ($this->pendingTeamRoleApplications->isEmpty())
                <x-empty-state
                    bare
                    icon="heroicons:inbox"
                    title="Нет заявок на рассмотрение"
                    description="Когда участники откликнутся — они появятся здесь"
                />
            @else
                <div class="grid grid-cols-1 gap-4">
                    @foreach ($this->pendingTeamRoleApplications as $application)
                        @php
                            $appTeam = $application->teamRole?->team;
                            $role = $application->teamRole?->role ?? $application->teamRole;
                        @endphp
                        <div class="flex flex-col gap-3 rounded-2xl border border-base-300 bg-base-100 p-5 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p class="font-medium">{{ $appTeam?->title ?? '—' }}</p>
                                @if ($role)
                                    <p class="text-sm text-base-content/70">Роль: <span class="font-medium">{{ $role->name ?? $role->title }}</span></p>
                                @endif
                                @if ($appTeam?->hackaton)
                                    <p class="text-sm text-base-content/60">{{ $appTeam->hackaton->title }}</p>
                                @endif
                                <p class="text-xs text-base-content/50">
                                    Подана <x-datetime :value="$application->created_at" mode="relative" />
                                </p>
                            </div>
                            <div class="flex items-center gap-3">
                                <x-application-status-badge :status="$application->status" size="sm" />
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

        {{-- Список команд --}}
        <section>
            <h2 class="text-2xl font-semibold mb-6">Ваши команды</h2>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @forelse ($this->teams as $team)
                    @php
                        $hackaton = $team->hackaton;
                        $openSlots = (int) ($team->empty_roles_count ?? 0);
                    @endphp

                    <article wire:key="my-team-{{ $team->id }}"
                             class="card border border-base-300 bg-base-100 overflow-hidden transition-shadow hover:shadow-md group">
                        
                        <x-team-cover
                            :title="$team->title"
                            :cover-url="$team->coverImagePublicUrl()"
                            :initials="$team->initialsForCover()"
                            :show-brand-strip="true"
                        />

                        <div class="p-6 flex-1 flex flex-col">
                            <h3 class="font-semibold text-xl line-clamp-2 mb-4">{{ $team->title }}</h3>

                            @if ($hackaton)
                                <div class="text-sm text-base-content/70 mb-5">
                                    {{ $hackaton->start_at?->format('d.m.Y') }} — {{ $hackaton->end_at?->format('d.m.Y') }}
                                </div>
                            @endif

                            <div class="grid grid-cols-2 gap-4 mb-6">
                                <div class="text-center bg-base-200/60 rounded-2xl py-4">
                                    <div class="text-3xl font-bold text-base-content">{{ $team->roles_count ?? $team->roles->count() }}</div>
                                    <div class="text-xs font-medium uppercase tracking-widest text-base-content/60">Ролей</div>
                                </div>
                                <div class="text-center bg-base-200/60 rounded-2xl py-4">
                                    <div class="text-3xl font-bold text-base-content">{{ $openSlots }}</div>
                                    <div class="text-xs font-medium uppercase tracking-widest text-base-content/60">Свободно</div>
                                </div>
                            </div>

                            <div class="mt-auto flex flex-col gap-2 sm:flex-row">
                                <a href="{{ route('teams.show', $team) }}" wire:navigate
                                   class="btn btn-neutral flex-1 gap-2">
                                    <x-app-icon icon="heroicons:eye" class="h-4 w-4" />
                                    Просмотреть
                                </a>
                                <button wire:click="editTeam({{ $team->id }})"
                                        class="btn btn-ghost flex-1 gap-2">
                                    <x-app-icon icon="heroicons:pencil" class="h-4 w-4" />
                                    Изменить
                                </button>
                                <button wire:click="showDeleteTeamModal({{ $team->id }})"
                                        class="btn btn-ghost text-error hover:bg-error/10 flex-1 gap-2">
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
                            description="Создайте команду для хакатона — настройте роли и пригласите участников."
                            icon="heroicons:user-group"
                            action-href="/teams/create"
                            action-label="Создать команду"
                        />
                    </div>
                @endforelse
            </div>
        </section>

    </div>

    {{-- Delete Modal --}}
    <x-mary-modal wire:model="deleteTeamModal" title="Удаление команды">
        <p class="text-base-content/90">
            @if ($deleteTeamTitle)
                Вы действительно хотите удалить команду «<span class="font-semibold">{{ $deleteTeamTitle }}</span>»?<br>
                Это действие необратимо.
            @else
                Вы действительно хотите удалить команду? Это действие необратимо.
            @endif
        </p>

        <x-slot:actions>
            <x-mary-button class="btn-error" label="Удалить" wire:click="deleteTeam" />
            <x-mary-button class="btn-ghost" label="Отмена" @click="$wire.deleteTeamModal = false" />
        </x-slot:actions>
    </x-mary-modal>

</div>