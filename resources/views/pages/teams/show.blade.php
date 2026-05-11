    @php
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
        $sectionCard = 'card rounded-3xl border border-base-300 bg-base-100 shadow-sm';
        $heroApplyBtn = 'btn btn-primary gap-2 shadow-lg shadow-primary/20';
        $modalTriggerClass = 'btn btn-primary btn-lg inline-flex cursor-pointer items-center justify-center gap-2 shadow-lg shadow-primary/25';
    @endphp

    @section('meta_description', $seoDescription)
    @section('canonical_url', route('teams.show', $team))
    @if ($teamImage)
        @section('og_image', $teamImage)
    @endif

    <div class="team-page mx-auto w-full max-w-7xl space-y-6" data-testid="team-page-root">
        <nav class="flex flex-wrap items-center gap-1 text-sm" aria-label="Навигация по хлебным крошкам">
            <a href="/" class="link link-hover text-base-content/80">Главная</a>
            <x-app-icon icon="heroicons:chevron-right-20-solid" class="h-4 w-4 shrink-0 text-base-content/40" />
            <a href="/teams" class="link link-hover text-base-content/80">Команды</a>
            <x-app-icon icon="heroicons:chevron-right-20-solid" class="h-4 w-4 shrink-0 text-base-content/40" />
            <span class="max-w-[min(100%,12rem)] truncate text-base-content/50 sm:max-w-md" title="{{ $team->title }}">{{ $team->title }}</span>
        </nav>

        <div class="tabs tabs-boxed w-full overflow-x-auto rounded-2xl border border-base-300/60 bg-base-200/50 p-1 shadow-inner" role="tablist" aria-label="Разделы команды" data-tab-list="team">
            <button type="button" class="tab tab-active" role="tab" aria-selected="true" aria-controls="team-panel-overview" data-tab-trigger="team" data-tab-value="overview">
                <x-app-icon icon="heroicons:squares-2x2" class="h-5 w-5 shrink-0 opacity-80" />
                Обзор
            </button>
            <button type="button" class="tab" role="tab" aria-selected="false" aria-controls="team-panel-roles" data-tab-trigger="team" data-tab-value="roles">
                <x-app-icon icon="heroicons:briefcase" class="h-5 w-5 shrink-0 opacity-80" />
                Роли
            </button>
            <button type="button" class="tab" role="tab" aria-selected="false" aria-controls="team-panel-members" data-tab-trigger="team" data-tab-value="members">
                <x-app-icon icon="heroicons:user-group" class="h-5 w-5 shrink-0 opacity-80" />
                Состав
            </button>
            @if ($team->user_id === auth()->id())
                <button type="button" class="tab" role="tab" aria-selected="false" aria-controls="team-panel-applications" data-tab-trigger="team" data-tab-value="applications">
                    <x-app-icon icon="heroicons:inbox" class="h-5 w-5 shrink-0 opacity-80" />
                    Заявки
                </button>
            @endif
        </div>

        <section id="team-panel-overview" role="tabpanel" data-tab-panel="team" data-tab-value="overview">
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <div class="relative overflow-hidden rounded-3xl border border-base-300 bg-linear-to-br from-base-100 via-base-100 to-primary/15 shadow-lg lg:col-span-2">
                    <div class="team-hero-orb pointer-events-none absolute -top-24 -right-20 h-64 w-64 rounded-full bg-secondary/15 blur-3xl motion-reduce:opacity-40"></div>
                    <div class="team-hero-orb pointer-events-none absolute -bottom-28 -left-20 h-72 w-72 rounded-full bg-primary/12 blur-3xl motion-reduce:opacity-40"></div>

                    <div class="relative border-b border-base-300/50 px-5 pb-5 pt-6 sm:px-7 sm:pt-8">
                        <h1 class="font-display text-pretty bg-linear-to-r from-base-content via-base-content to-base-content/65 bg-clip-text text-4xl font-bold tracking-tight text-transparent sm:text-5xl lg:text-6xl">
                            {{ $team->title }}
                        </h1>
                    </div>

                    <figure class="relative aspect-video w-full bg-base-200 ring-1 ring-base-content/5">
                        @if ($teamImage)
                            <img src="{{ $teamImage }}" class="h-full w-full object-cover shadow-inner" alt="{{ $team->title }}">
                        @else
                            <div
                                class="flex h-full min-h-44 flex-col items-center justify-center gap-3 bg-base-200 px-4 text-center text-base-content/55 bg-[radial-gradient(circle_at_1px_1px,color-mix(in_oklch,var(--color-base-300)_55%,transparent)_1px,transparent_0)] [background-size:28px_28px]">
                                <x-app-icon icon="heroicons:photo" class="h-16 w-16 text-primary/50" />
                                <span class="text-sm font-medium">Превью не загружено</span>
                            </div>
                        @endif
                    </figure>

                    <div class="card-body relative gap-4 px-5 pb-6 pt-2 sm:px-7">
                        <div class="flex flex-wrap items-center gap-3">
                            @if ($canHeroApply)
                                <a href="#team-tab-roles" class="{{ $heroApplyBtn }}">
                                    <x-app-icon icon="heroicons:paper-airplane" class="h-5 w-5" />
                                    Подать заявку
                                </a>
                            @endif
                            @guest
                                <a href="/login" class="btn btn-ghost btn-sm gap-2 border border-base-300/80">
                                    <x-app-icon icon="heroicons:arrow-right-on-rectangle" class="h-4 w-4" />
                                    Войти
                                </a>
                            @endguest
                        </div>
                        <div class="markdown-body mt-1">
                            {!! \App\Support\SafeMarkdown::toHtml($team->description ?? 'Описание отсутствует.') !!}
                        </div>
                    </div>
                </div>

                <div class="{{ $sectionCard }} overflow-hidden">
                    <div class="card-body gap-4 p-5 sm:p-6">
                        <h2 class="card-title text-base">
                            <x-app-icon icon="heroicons:information-circle" class="h-5 w-5 text-primary" />
                            О команде
                        </h2>
                        <div class="grid grid-cols-2 gap-3">
                            <div
                                class="flex flex-col gap-2 rounded-2xl border border-base-300/60 bg-base-200/35 p-4 transition duration-200 hover:border-primary/35 hover:bg-base-200/50">
                                <div class="flex items-center gap-2 text-base-content/70">
                                    <x-app-icon icon="heroicons:user-group" class="h-5 w-5 shrink-0 text-primary" />
                                    <span class="text-[0.65rem] font-bold uppercase tracking-wider">Ролей</span>
                                </div>
                                <span class="font-display text-2xl font-bold tabular-nums text-base-content">{{ $team->roles->count() }}</span>
                            </div>
                            <div
                                class="flex flex-col gap-2 rounded-2xl border border-base-300/60 bg-base-200/35 p-4 transition duration-200 hover:border-primary/35 hover:bg-base-200/50">
                                <div class="flex items-center gap-2 text-base-content/70">
                                    <x-app-icon icon="heroicons:megaphone" class="h-5 w-5 shrink-0 text-primary" />
                                    <span class="text-[0.65rem] font-bold uppercase tracking-wider">Открыто</span>
                                </div>
                                <span class="font-display text-2xl font-bold tabular-nums text-secondary">{{ $openRoles->count() }}</span>
                            </div>
                        </div>

                        <div class="divider my-0 text-xs text-base-content/40"></div>

                        <div class="flex gap-3 rounded-2xl border border-base-300/50 bg-base-200/25 px-4 py-3">
                            <x-app-icon icon="heroicons:user-circle" class="mt-0.5 h-5 w-5 shrink-0 text-primary" />
                            <div class="min-w-0 text-sm">
                                <span class="text-xs font-semibold uppercase tracking-wide text-base-content/50">Капитан</span>
                                <p class="mt-0.5 font-semibold leading-snug text-base-content">
                                    {{ $team->user->nickname ?? $team->user->name ?? $team->user->email }}
                                </p>
                            </div>
                        </div>

                        @if ($team->hackaton)
                            <div class="flex gap-3 rounded-2xl border border-base-300/50 bg-base-200/25 px-4 py-3">
                                <x-app-icon icon="heroicons:map-pin" class="mt-0.5 h-5 w-5 shrink-0 text-primary" />
                                <div class="min-w-0 text-sm">
                                    <span class="text-xs font-semibold uppercase tracking-wide text-base-content/50">Хакатон</span>
                                    <p class="mt-0.5 leading-snug">
                                        <a class="link link-primary font-semibold" href="{{ route('hackatons.show', $team->hackaton) }}">{{ $team->hackaton->title }}</a>
                                    </p>
                                </div>
                            </div>
                        @endif

                        @if ($team->socialLinks->isNotEmpty())
                            <div>
                                <p class="mb-2 text-xs font-bold uppercase tracking-wider text-base-content/45">Ссылки</p>
                                <ul class="space-y-2 text-sm">
                                    @foreach ($team->socialLinks as $link)
                                        <li class="flex items-center gap-2">
                                            <x-app-icon icon="heroicons:link" class="h-4 w-4 shrink-0 text-primary/70" />
                                            <a href="{{ $link->url }}" target="_blank" rel="noopener noreferrer" class="link link-hover link-primary truncate font-medium">
                                                {{ $link->name }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>

        <section id="team-panel-roles" role="tabpanel" class="hidden" data-tab-panel="team" data-tab-value="roles">
            <div id="team-open-roles" class="scroll-mt-24">
                <div class="{{ $sectionCard }}">
                    <div class="card-body gap-5">
                        <h2 class="card-title text-xl">
                            <x-app-icon icon="heroicons:briefcase" class="h-6 w-6 text-primary" />
                            Открытые роли
                        </h2>
                        @forelse ($openRoles as $role)
                            <div
                                class="animate-card-enter space-y-4 rounded-2xl border border-base-300/60 border-l-4 border-l-primary bg-base-100/75 p-5 shadow-lg shadow-primary/5 backdrop-blur-sm transition duration-300 ease-out first:mt-0 hover:-translate-y-0.5 hover:border-base-300/80 hover:shadow-xl hover:shadow-primary/10 sm:p-6"
                                style="animation-delay: {{ min($loop->index * 40, 200) }}ms">
                                <div class="flex flex-wrap items-start justify-between gap-4">
                                    <div class="min-w-0 space-y-2">
                                        <p class="font-display text-lg font-bold leading-tight tracking-tight sm:text-xl">{{ $role->title }}</p>
                                        <div class="flex flex-wrap items-center gap-2">
                                            @if ($role->role)
                                                <span class="badge border-0 bg-secondary/90 font-semibold text-secondary-content">{{ $role->role->name }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    @auth
                                        @if ($team->user_id !== auth()->id())
                                            @php
                                                $myApplication = $myApplicationsByRole->get($role->id);
                                            @endphp

                                            @if (auth()->user()->isOrganizer())
                                                <span class="badge badge-warning gap-1">
                                                    <x-app-icon icon="heroicons:exclamation-triangle" class="h-4 w-4" />
                                                    Организаторы не могут откликаться
                                                </span>
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
                                                            <button class="btn btn-xs btn-ghost gap-1" type="submit">
                                                                <x-app-icon icon="heroicons:x-mark" class="h-3.5 w-3.5" />
                                                                Отменить заявку
                                                            </button>
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
                                                    action="{{ route('team.applications.store') }}">
                                                    <x-slot:trigger>
                                                        <x-app-icon icon="heroicons:paper-airplane" class="h-5 w-5 shrink-0" />
                                                        Откликнуться
                                                    </x-slot:trigger>
                                                </x-application-modal>
                                            @endif
                                        @endif
                                    @endauth
                                </div>
                                <div class="markdown-body">
                                    {!! \App\Support\SafeMarkdown::toHtml($role->description ?? 'Описание роли отсутствует.') !!}
                                </div>
                                @if ($role->skills->isNotEmpty())
                                    <div class="flex flex-wrap gap-2">
                                        @foreach ($role->skills as $skill)
                                            <span
                                                class="badge badge-sm border-0 bg-primary/12 font-medium text-primary ring-1 ring-primary/30 transition duration-200 hover:ring-primary/55 hover:shadow-[0_0_14px_color-mix(in_oklch,var(--color-primary)_40%,transparent)]">
                                                {{ $skill->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @empty
                            <x-team-empty-state
                                title="Свободных ролей нет"
                                description="Команда закрыла набор или все места заняты. Загляните в каталог — там много других команд."
                                icon="heroicons:archive-box"
                                action-href="/teams"
                                action-label="Найти другие команды"
                                test-id="team-empty-roles" />
                        @endforelse

                        @guest
                            <div role="alert" class="alert border border-primary/25 bg-primary/10 text-base-content">
                                <x-app-icon icon="heroicons:lock-closed" class="h-6 w-6 text-primary" />
                                <span>Чтобы откликнуться на роль, <a class="link font-semibold link-primary" href="/login">войдите в аккаунт</a>.</span>
                            </div>
                        @endguest
                    </div>
                </div>
            </div>
        </section>

        <section id="team-panel-members" role="tabpanel" class="hidden" data-tab-panel="team" data-tab-value="members">
            <div class="{{ $sectionCard }}">
                <div class="card-body gap-5">
                    <h2 class="card-title text-xl">
                        <x-app-icon icon="heroicons:user-group" class="h-6 w-6 text-primary" />
                        Участники
                    </h2>
                    @if ($occupiedRoles->isEmpty())
                        <x-team-empty-state
                            title="Пока никого в составе"
                            description="Соберите команду мечты: откликнитесь на роль или пригласите тех, кто вам нужен."
                            icon="heroicons:user-plus"
                            action-href="#team-tab-roles"
                            action-label="Смотреть открытые роли"
                            test-id="team-empty-members" />
                    @else
                        <div class="mt-1 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach ($occupiedRoles as $role)
                                @php
                                    $u = $role->user;
                                    $displayName = $u->fio ?? $u->nickname ?? $u->email;
                                    $initials = filled($u->fio)
                                        ? $u->initials()
                                        : mb_strtoupper(mb_substr((string) ($u->nickname ?? $u->email), 0, 2));
                                    $publicProfile =
                                        $u->is_profile_public && filled($u->nickname)
                                            ? route('profile.public.show', ['user' => $u->nickname])
                                            : null;
                                @endphp
                                <div
                                    class="animate-card-enter group relative flex flex-col gap-3 rounded-2xl border border-base-300/55 bg-base-100/70 p-5 shadow-md ring-1 ring-transparent transition duration-300 ease-out hover:-translate-y-0.5 hover:border-primary/35 hover:shadow-xl hover:shadow-primary/10 hover:ring-primary/15">
                                    <div class="flex items-start gap-3">
                                        <div class="avatar placeholder">
                                            <div
                                                class="w-14 rounded-full bg-neutral text-neutral-content ring-2 ring-base-300/80 ring-offset-2 ring-offset-base-100 transition duration-300 group-hover:ring-primary/40">
                                                <span class="text-lg font-semibold">{{ $initials ?: '?' }}</span>
                                            </div>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            @if ($publicProfile)
                                                <a href="{{ $publicProfile }}" class="link link-hover link-primary font-display font-bold leading-snug" title="{{ $displayName }}">
                                                    <span class="line-clamp-2">{{ $displayName }}</span>
                                                </a>
                                            @else
                                                <p class="truncate font-display font-bold leading-snug text-base-content" title="{{ $displayName }}">{{ $displayName }}</p>
                                            @endif
                                            <p class="mt-1 flex items-center gap-1.5 text-sm text-base-content/65">
                                                <x-app-icon icon="heroicons:identification" class="h-4 w-4 shrink-0 opacity-70" />
                                                {{ $role->title }}
                                            </p>
                                        </div>
                                    </div>
                                    @if (auth()->check() && $team->user_id === auth()->id())
                                        <form method="POST" action="{{ route('teams.participants.destroy', ['team' => $team, 'teamRole' => $role]) }}"
                                            class="mt-auto border-t border-base-300/50 pt-4"
                                            onsubmit="return confirm('Удалить участника из команды?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-error btn-xs btn-block gap-1" type="submit">
                                                <x-app-icon icon="heroicons:user-minus" class="h-4 w-4" />
                                                Удалить из команды
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </section>

        @if ($team->user_id === auth()->id())
            <section id="team-panel-applications" role="tabpanel" class="hidden" data-tab-panel="team" data-tab-value="applications">
                <div class="{{ $sectionCard }}">
                    <div class="card-body gap-5">
                        <h2 class="card-title text-xl">
                            <x-app-icon icon="heroicons:inbox" class="h-6 w-6 text-primary" />
                            Заявки на вступление
                        </h2>

                        @if ($team->applications->isEmpty())
                            <x-team-empty-state
                                title="Заявок пока нет"
                                description="Когда участник откликнется на открытую роль, вы увидите его сообщение и сможете принять или отклонить заявку здесь."
                                icon="heroicons:document-text"
                                action-href="#team-tab-roles"
                                action-label="Открыть роли"
                                test-id="team-empty-applications" />
                        @else
                            <div class="hidden overflow-x-auto md:block">
                                <table class="table table-zebra">
                                    <thead>
                                        <tr>
                                            <th>
                                                <span class="inline-flex items-center gap-1.5">
                                                    <x-app-icon icon="heroicons:user" class="h-4 w-4 opacity-70" />
                                                    Пользователь
                                                </span>
                                            </th>
                                            <th>
                                                <span class="inline-flex items-center gap-1.5">
                                                    <x-app-icon icon="heroicons:briefcase" class="h-4 w-4 opacity-70" />
                                                    Роль
                                                </span>
                                            </th>
                                            <th>
                                                <span class="inline-flex items-center gap-1.5">
                                                    <x-app-icon icon="heroicons:chat-bubble-left" class="h-4 w-4 opacity-70" />
                                                    Сообщение
                                                </span>
                                            </th>
                                            <th>
                                                <span class="inline-flex items-center gap-1.5">
                                                    <x-app-icon icon="heroicons:clock" class="h-4 w-4 opacity-70" />
                                                    Отправлена
                                                </span>
                                            </th>
                                            <th>
                                                <span class="inline-flex items-center gap-1.5">
                                                    <x-app-icon icon="heroicons:flag" class="h-4 w-4 opacity-70" />
                                                    Статус
                                                </span>
                                            </th>
                                            <th>
                                                <span class="inline-flex items-center gap-1.5">
                                                    <x-app-icon icon="heroicons:eye" class="h-4 w-4 opacity-70" />
                                                    Рассмотрел
                                                </span>
                                            </th>
                                            <th>
                                                <span class="inline-flex items-center gap-1.5">
                                                    <x-app-icon icon="heroicons:calendar-days" class="h-4 w-4 opacity-70" />
                                                    Рассмотрена
                                                </span>
                                            </th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($team->applications as $app)
                                            <tr>
                                                <td class="font-medium">{{ $app->user->fio ?? $app->user->nickname ?? $app->user->email }}</td>
                                                <td>{{ $app->teamRole->title }}</td>
                                                <td class="max-w-xs truncate">{{ $app->message }}</td>
                                                <td class="whitespace-nowrap text-sm">{{ $app->created_at?->format('d.m.Y H:i') ?? '—' }}</td>
                                                <td>
                                                    <span
                                                        class="badge badge-{{ $app->status->isAccepted() ? 'success' : ($app->status->isRejected() ? 'error' : 'warning') }}">
                                                        {{ $app->status->label() }}
                                                    </span>
                                                </td>
                                                <td>{{ $app->reviewer?->nickname ?? $app->reviewer?->name ?? '—' }}</td>
                                                <td class="whitespace-nowrap text-sm">{{ $app->reviewed_at?->format('d.m.Y H:i') ?? '—' }}</td>
                                                <td>
                                                    @if ($app->status->isPending())
                                                        <form method="POST" action="{{ route('team.applications.update', $app) }}" class="inline-flex">
                                                            @csrf
                                                            @method('PATCH')
                                                            <input type="hidden" name="status" value="accepted">
                                                            <button class="btn btn-success btn-xs gap-0.5" type="submit">
                                                                <x-app-icon icon="heroicons:check" class="h-3.5 w-3.5" />
                                                                Принять
                                                            </button>
                                                        </form>

                                                        <form method="POST" action="{{ route('team.applications.update', $app) }}"
                                                            class="ml-2 inline-flex"
                                                            onsubmit="return confirm('Отклонить заявку? Пользователь останется без роли.');">
                                                            @csrf
                                                            @method('PATCH')
                                                            <input type="hidden" name="status" value="rejected">
                                                            <button class="btn btn-error btn-xs gap-0.5" type="submit">
                                                                <x-app-icon icon="heroicons:x-mark" class="h-3.5 w-3.5" />
                                                                Отклонить
                                                            </button>
                                                        </form>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="space-y-4 md:hidden">
                                @foreach ($team->applications as $app)
                                    <div class="rounded-2xl border border-base-300/70 bg-base-200/25 p-4 shadow-sm">
                                        <div class="flex flex-wrap items-start justify-between gap-2">
                                            <p class="font-semibold leading-snug">{{ $app->user->fio ?? $app->user->nickname ?? $app->user->email }}</p>
                                            <span
                                                class="badge badge-{{ $app->status->isAccepted() ? 'success' : ($app->status->isRejected() ? 'error' : 'warning') }} shrink-0">
                                                {{ $app->status->label() }}
                                            </span>
                                        </div>
                                        <p class="mt-2 text-sm text-base-content/70">
                                            <span class="font-medium text-base-content/80">Роль:</span> {{ $app->teamRole->title }}
                                        </p>
                                        @if (filled($app->message))
                                            <p class="mt-2 line-clamp-4 text-sm text-base-content/75">«{{ $app->message }}»</p>
                                        @endif
                                        <dl class="mt-3 grid gap-1 text-xs text-base-content/60">
                                            <div class="flex justify-between gap-2">
                                                <dt>Отправлена</dt>
                                                <dd class="font-medium text-base-content/80">{{ $app->created_at?->format('d.m.Y H:i') ?? '—' }}</dd>
                                            </div>
                                            <div class="flex justify-between gap-2">
                                                <dt>Рассмотрел</dt>
                                                <dd class="font-medium text-base-content/80">{{ $app->reviewer?->nickname ?? $app->reviewer?->name ?? '—' }}</dd>
                                            </div>
                                            <div class="flex justify-between gap-2">
                                                <dt>Рассмотрена</dt>
                                                <dd class="font-medium text-base-content/80">{{ $app->reviewed_at?->format('d.m.Y H:i') ?? '—' }}</dd>
                                            </div>
                                        </dl>
                                        @if ($app->status->isPending())
                                            <div class="mt-4 flex flex-wrap gap-2">
                                                <form method="POST" action="{{ route('team.applications.update', $app) }}" class="flex-1 min-w-[8rem]">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="accepted">
                                                    <button class="btn btn-success btn-sm btn-block gap-1" type="submit">
                                                        <x-app-icon icon="heroicons:check" class="h-4 w-4" />
                                                        Принять
                                                    </button>
                                                </form>
                                                <form method="POST" action="{{ route('team.applications.update', $app) }}" class="flex-1 min-w-[8rem]"
                                                    onsubmit="return confirm('Отклонить заявку? Пользователь останется без роли.');">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="rejected">
                                                    <button class="btn btn-error btn-sm btn-block gap-1" type="submit">
                                                        <x-app-icon icon="heroicons:x-mark" class="h-4 w-4" />
                                                        Отклонить
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </section>
        @endif
    </div>

    <script>
        (function () {
            const setupTabGroup = (groupName, fallbackTab) => {
                const triggers = Array.from(document.querySelectorAll(`[data-tab-trigger="${groupName}"]`));
                const panels = Array.from(document.querySelectorAll(`[data-tab-panel="${groupName}"]`));

                if (triggers.length === 0 || panels.length === 0) {
                    return;
                }

                const availableTabs = new Set(triggers.map((trigger) => trigger.dataset.tabValue));
                const hash = window.location.hash;
                const hashPrefix = `#${groupName}-tab-`;
                const requestedTab = hash.startsWith(hashPrefix) ? hash.slice(hashPrefix.length) : null;
                let activeTab = requestedTab && availableTabs.has(requestedTab) ? requestedTab : fallbackTab;

                if (!availableTabs.has(activeTab)) {
                    activeTab = triggers[0].dataset.tabValue;
                }

                const setActiveTab = (tabValue, replace = false) => {
                    if (!availableTabs.has(tabValue)) {
                        return;
                    }

                    triggers.forEach((trigger) => {
                        const isActive = trigger.dataset.tabValue === tabValue;
                        trigger.classList.toggle('tab-active', isActive);
                        trigger.setAttribute('aria-selected', isActive ? 'true' : 'false');
                    });

                    panels.forEach((panel) => {
                        panel.classList.toggle('hidden', panel.dataset.tabValue !== tabValue);
                    });

                    const nextHash = `${hashPrefix}${tabValue}`;
                    if (replace) {
                        history.replaceState(null, '', nextHash);
                    } else {
                        history.pushState(null, '', nextHash);
                    }
                };

                triggers.forEach((trigger) => {
                    trigger.addEventListener('click', () => setActiveTab(trigger.dataset.tabValue));
                });

                setActiveTab(activeTab, true);
            };

            setupTabGroup('team', 'overview');
        })();
    </script>
