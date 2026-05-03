<?php

use App\Enums\HackatonStatus;
use App\Models\Hackaton;
use App\Models\Team;
use App\Models\TeamRole;
use App\Models\User;
use App\ViewModels\HomeDashboardData;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::app', ['title' => 'Главная'])] class extends Component {
    /** @var array<int, Hackaton> */
    public array $featuredHackatons = [];

    /** Публичные хакатоны (все статусы кроме черновика), включая завершённые и в архиве */
    public int $publicHackatonsCount = 0;

    public int $publicParticipantsCount = 0;

    public int $publicTeamsCount = 0;

    public int $teamsCount = 0;

    public int $certificatesCount = 0;

    public int $pendingTeamApplicationsCount = 0;

    public int $pendingHackatonApplicationsCount = 0;

    /** @var list<array{id: int, hackaton_id: int, title: string, team_title: string, status_label: string}> */
    public array $hackatonApplicationsPreview = [];

    /** @var list<array{id: int, title: string, start_at: string|null}> */
    public array $participantHackatonsPreview = [];

    public string $participantNextStepTitle = '';

    public string $participantNextStepHint = '';

    public ?string $participantNextStepHref = null;

    public ?string $participantNextStepLabel = null;

    public int $hackatonsCount = 0;

    public ?int $organizerFirstPendingHackatonId = null;

    public int $judgeHackatonsCount = 0;

    /** @var list<array{id: int, title: string, start_at: string|null}> */
    public array $judgeHackatonsPreview = [];

    public int $usersCount = 0;

    public int $adminHackatonsCount = 0;

    public int $adminPartnersCount = 0;

    public int $adminPendingApplicationsCount = 0;

    public int $unreadNotificationsCount = 0;

    public bool $showPhoneVerificationBanner = false;

    public function mount(): void
    {
        $this->featuredHackatons = Hackaton::query()
            ->select('hackatons.*')
            ->selectSub(function ($query) {
                $query->from('team_roles')
                    ->join('teams', 'teams.id', '=', 'team_roles.team_id')
                    ->whereColumn('teams.hackaton_id', 'hackatons.id')
                    ->whereNotNull('team_roles.user_id')
                    ->selectRaw('count(*)');
            }, 'participants_aggregate')
            ->where('is_public', true)
            ->whereIn('status', [
                HackatonStatus::PUBLISHED,
                HackatonStatus::REGISTRATION_OPEN,
                HackatonStatus::IN_PROGRESS,
                HackatonStatus::JUDGING,
            ])
            ->withCount('teams')
            ->latest('start_at')
            ->limit(4)
            ->get()
            ->all();

        // Все публичные события кроме черновика (в т.ч. завершённые и в архиве) + суммарно команды и участники по ним.
        $totals = Cache::remember('home-public-totals-v3', now()->addMinutes(10), function (): array {
            $hackatonsCount = Hackaton::query()
                ->where('is_public', true)
                ->whereNot('status', HackatonStatus::DRAFT)
                ->count();

            $teamsCount = Team::query()
                ->whereExists(function ($query): void {
                    $query->selectRaw('1')
                        ->from('hackatons')
                        ->whereColumn('hackatons.id', 'teams.hackaton_id')
                        ->where('hackatons.is_public', true)
                        ->whereNot('hackatons.status', HackatonStatus::DRAFT);
                })
                ->count();

            $participantsCount = TeamRole::query()
                ->whereNotNull('user_id')
                ->whereExists(function ($query): void {
                    $query->from('teams')
                        ->join('hackatons', 'hackatons.id', '=', 'teams.hackaton_id')
                        ->whereColumn('teams.id', 'team_roles.team_id')
                        ->where('hackatons.is_public', true)
                        ->whereNot('hackatons.status', HackatonStatus::DRAFT);
                })
                ->count();

            return [
                'hackatons' => $hackatonsCount,
                'participants' => $participantsCount,
                'teams' => $teamsCount,
            ];
        });

        $this->publicHackatonsCount = (int) ($totals['hackatons'] ?? 0);
        $this->publicParticipantsCount = (int) ($totals['participants'] ?? 0);
        $this->publicTeamsCount = (int) ($totals['teams'] ?? 0);

        if (! Auth::check()) {
            return;
        }

        $user = Auth::user();
        if (! $user instanceof User) {
            return;
        }

        foreach (HomeDashboardData::fromUser($user)->toLivewireArray() as $key => $value) {
            $this->{$key} = $value;
        }
    }
};

?>

@guest
<div class="mx-auto w-full max-w-7xl space-y-20 sm:space-y-24 xl:space-y-28">
    {{-- Hero --}}
    <section
        id="start"
        class="relative min-h-[26rem] overflow-hidden rounded-3xl border border-primary/25 bg-base-100 shadow-xl shadow-primary/10 transition-all duration-700 ease-out sm:min-h-[30rem] lg:min-h-[70vh] lg:max-h-[min(90vh,56rem)]"
        x-data="{ shown: false }"
        x-init="requestAnimationFrame(() => { shown = true })"
        :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-3'"
    >
        <div class="pointer-events-none absolute inset-0 opacity-90" aria-hidden="true" style="background: radial-gradient(1200px 600px at 10% -10%, oklch(56% 0.21 272 / 0.28), transparent 55%), radial-gradient(900px 500px at 90% 20%, oklch(82% 0.19 118 / 0.22), transparent 50%), radial-gradient(600px 400px at 50% 100%, oklch(22% 0.06 264 / 0.35), transparent 45%);"></div>
        <div class="relative flex min-h-[inherit] flex-col gap-8 px-5 py-10 sm:gap-10 sm:px-8 sm:py-14 lg:flex-row lg:items-center lg:gap-16 lg:px-12 lg:py-16">
            <div class="flex max-w-xl flex-1 flex-col justify-center lg:max-w-[min(36rem,50%)] lg:text-left">
                <h1 class="font-display text-5xl font-bold leading-[1.06] tracking-tight sm:text-6xl lg:text-7xl">
                    Найдите команду. Проведите хакатон.
                </h1>
                <p class="mt-5 max-w-2xl text-base leading-relaxed text-base-content/85 sm:text-lg">
                    Хакатонщик помогает участникам, командам и организаторам пройти весь путь — от поиска единомышленников до финальной защиты и вручения сертификатов.
                </p>
                <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-center lg:justify-start">
                    <a
                        href="/hackatons/create"
                        class="btn btn-secondary btn-lg order-1 text-base shadow-xl shadow-secondary/30 ring-2 ring-secondary/40 ring-offset-2 ring-offset-base-100 transition-transform hover:scale-[1.02] active:scale-[0.99] sm:text-lg sm:order-2"
                    >
                        Создать хакатон
                    </a>
                    <a href="/teams" class="btn btn-primary btn-lg order-2 btn-outline border-primary/50 sm:order-1">
                        Найти команду
                    </a>
                </div>
            </div>
            <div class="relative flex max-w-[18rem] flex-1 shrink-0 items-center justify-center self-center sm:max-w-[20rem] lg:max-w-[min(24rem,40%)] lg:justify-end lg:scale-95">
                <div class="relative aspect-square w-full max-w-full">
                    <div class="absolute inset-6 rounded-[2rem] bg-gradient-to-br from-secondary/20 via-primary/12 to-base-300/70 blur-2xl" aria-hidden="true"></div>
                    <div class="relative flex h-full w-full items-center justify-center rounded-3xl border border-base-300/80 bg-base-200/40 p-4 shadow-inner backdrop-blur-sm sm:p-5">
                        <img
                            src="{{ url('/hackatonshik.svg') }}"
                            alt=""
                            class="h-auto w-full max-h-[min(18rem,42vh)] object-contain drop-shadow-2xl sm:max-h-[min(20rem,48vh)] lg:max-h-[min(22rem,50vh)]"
                            width="480"
                            height="480"
                            loading="eager"
                        />
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Активные хакатоны --}}
    <section
        class="space-y-8 transition-all duration-700 ease-out will-change-transform"
        x-data="{ shown: false }"
        x-init="const io = new IntersectionObserver((es) => { es.forEach(e => { if (e.isIntersecting) { shown = true; io.disconnect(); } }); }, { threshold: 0.08, rootMargin: '0px 0px -5% 0px' }); io.observe($el);"
        :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
    >
        <div class="flex flex-wrap items-end justify-between gap-4">
            <h2 class="font-display text-3xl font-bold tracking-tight sm:text-4xl">Активные хакатоны</h2>
            <a href="/hackatons" class="btn btn-ghost btn-sm gap-2 sm:btn-md">
                <x-app-icon icon="heroicons:arrow-right" class="h-4 w-4" />
                Все хакатоны
            </a>
        </div>
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            @forelse ($featuredHackatons as $hackaton)
                @php
                    $hackatonImage = filled($hackaton->image_url)
                        ? (str_starts_with((string) $hackaton->image_url, 'http') ? $hackaton->image_url : asset('storage/'.$hackaton->image_url))
                        : null;
                    $teamsTotal = (int) ($hackaton->teams_count ?? 0);
                    $participantsTotal = (int) ($hackaton->participants_aggregate ?? 0);
                @endphp
                <x-mary-card class="card card-border border-base-300 h-full shadow-sm transition-all duration-200 hover:border-primary/35 hover:shadow-lg">
                    <div class="overflow-hidden rounded-xl bg-base-200 aspect-video">
                        @if ($hackatonImage)
                            <img src="{{ $hackatonImage }}" class="h-full w-full object-cover" alt="{{ $hackaton->title }}" loading="lazy" />
                        @else
                            <div class="flex h-full min-h-[10rem] w-full flex-col items-center justify-center gap-2 px-4 text-center text-base-content/50">
                                <x-app-icon icon="heroicons:photo" class="h-12 w-12 opacity-40" />
                                <span class="text-sm">Изображение появится позже</span>
                            </div>
                        @endif
                    </div>
                    <div class="mt-3 flex grow flex-col gap-3">
                        <div class="flex flex-wrap items-start justify-between gap-2">
                            <a href="{{ route('hackatons.show', $hackaton) }}" class="card-title link-hover link text-lg leading-snug">{{ $hackaton->title }}</a>
                            <span class="badge badge-primary badge-outline shrink-0">{{ $hackaton->status?->label() }}</span>
                        </div>
                        <p class="text-sm text-base-content/75">
                            @if ($hackaton->start_at && $hackaton->end_at)
                                {{ $hackaton->start_at->translatedFormat('d.m.Y H:i') }}
                                —
                                {{ $hackaton->end_at->translatedFormat('d.m.Y H:i') }}
                            @endif
                        </p>
                        <div class="flex flex-wrap gap-2">
                            <span class="badge badge-neutral gap-1">
                                <x-app-icon icon="heroicons:user-group" class="h-3.5 w-3.5" />
                                Команд: {{ $teamsTotal }}
                            </span>
                            <span class="badge badge-neutral gap-1">
                                <x-app-icon icon="heroicons:users" class="h-3.5 w-3.5" />
                                Участников: {{ $participantsTotal }}
                            </span>
                        </div>
                    </div>
                    <x-slot:actions class="mt-auto pt-2">
                        <a href="{{ route('hackatons.show', $hackaton) }}" class="btn btn-primary btn-sm">Подробнее</a>
                    </x-slot:actions>
                </x-mary-card>
            @empty
                <div class="sm:col-span-2">
                    <div class="card card-border border-base-300 border-dashed bg-base-100/80 bg-gradient-to-br from-base-200/80 to-base-100 shadow-sm">
                        <div class="card-body flex flex-col items-center gap-6 px-6 py-14 text-center sm:flex-row sm:text-left">
                            <div class="flex h-36 w-36 shrink-0 items-center justify-center rounded-3xl bg-secondary/10 ring-1 ring-secondary/25">
                                <x-app-icon icon="heroicons:rocket-launch" class="h-16 w-16 text-secondary" label="Скоро новые хакатоны" />
                            </div>
                            <div class="max-w-lg space-y-4">
                                <h3 class="font-display text-xl font-bold sm:text-2xl">Первые хакатоны уже скоро!</h3>
                                <p class="text-base leading-relaxed text-base-content/75">Следите за обновлениями — скоро здесь появятся интересные события.</p>
                                <a href="/hackatons" class="btn btn-secondary btn-lg mt-2 w-full shadow-md shadow-secondary/25 ring-2 ring-secondary/25 ring-offset-2 ring-offset-base-100 transition-transform hover:scale-[1.02] active:scale-[0.99] sm:mt-0 sm:w-auto">
                                    Открыть каталог
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
    </section>

    {{-- Платформа в цифрах --}}
    <section
        id="home-stats"
        class="rounded-3xl border border-base-300 bg-base-100 p-6 shadow-sm transition-all duration-700 ease-out will-change-transform sm:p-8"
        x-data="{
            reveal: false,
            active: 0,
            participants: 0,
            teams: 0,
            started: false,
            start() {
                const self = this;
                const io = new IntersectionObserver((entries) => {
                    entries.forEach((entry) => {
                        if (entry.isIntersecting) {
                            self.reveal = true;
                            self.animate();
                            io.disconnect();
                        }
                    });
                }, { threshold: 0.12, rootMargin: '0px 0px -8% 0px' });
                io.observe(this.$el);
            },
            animate() {
                if (this.started) {
                    return;
                }
                this.started = true;
                const root = this;
                const run = (prop, target) => {
                    let current = 0;
                    const step = Math.max(1, Math.ceil(target / 45));
                    const timer = setInterval(() => {
                        current += step;
                        if (current >= target) {
                            current = target;
                            clearInterval(timer);
                        }
                        root[prop] = current;
                    }, 24);
                };
                run('active', {{ $publicHackatonsCount }});
                run('participants', {{ $publicParticipantsCount }});
                run('teams', {{ $publicTeamsCount }});
            },
        }"
        x-init="start()"
        :class="reveal ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
    >
        <h2 class="font-display text-3xl font-bold tracking-tight sm:text-4xl">Платформа в цифрах</h2>
        <p class="mt-2 max-w-2xl text-base-content/70">Учитываются все публичные хакатоны на платформе — текущие, предстоящие, завершённые и в архиве (кроме черновиков).</p>
        <div class="mt-8 grid grid-cols-1 gap-5 sm:grid-cols-3 sm:gap-6">
            <div
                class="rounded-2xl border border-base-300/80 bg-base-200/50 p-6 text-center transition-all duration-500 ease-out hover:border-secondary/30 sm:p-7"
                :class="reveal ? 'translate-y-0 opacity-100 delay-75' : 'translate-y-6 opacity-0'"
            >
                <div class="mx-auto mb-5 flex h-20 w-20 items-center justify-center rounded-2xl bg-primary/15 text-primary">
                    <x-app-icon icon="heroicons:trophy" class="h-10 w-10" label="Хакатоны на платформе" />
                </div>
                <p class="text-base font-semibold text-base-content/90">Хакатонов</p>
                <p class="mt-3 text-5xl font-bold tabular-nums tracking-tight text-base-content sm:text-6xl" x-text="active"></p>
            </div>
            <div
                class="rounded-2xl border border-base-300/80 bg-base-200/50 p-6 text-center transition-all duration-500 ease-out hover:border-secondary/30 sm:p-7"
                :class="reveal ? 'translate-y-0 opacity-100 delay-150' : 'translate-y-6 opacity-0'"
            >
                <div class="mx-auto mb-5 flex h-20 w-20 items-center justify-center rounded-2xl bg-secondary/15 text-secondary">
                    <x-app-icon icon="heroicons:users" class="h-10 w-10" label="Участники" />
                </div>
                <p class="text-base font-semibold text-base-content/90">Участников</p>
                <p class="mt-3 text-5xl font-bold tabular-nums tracking-tight text-base-content sm:text-6xl" x-text="participants"></p>
            </div>
            <div
                class="rounded-2xl border border-base-300/80 bg-base-200/50 p-6 text-center transition-all duration-500 ease-out hover:border-secondary/30 sm:p-7"
                :class="reveal ? 'translate-y-0 opacity-100 delay-200' : 'translate-y-6 opacity-0'"
            >
                <div class="mx-auto mb-5 flex h-20 w-20 items-center justify-center rounded-2xl bg-accent/15 text-accent">
                    <x-app-icon icon="heroicons:user-group" class="h-10 w-10" label="Команды" />
                </div>
                <p class="text-base font-semibold text-base-content/90">Команд</p>
                <p class="mt-3 text-5xl font-bold tabular-nums tracking-tight text-base-content sm:text-6xl" x-text="teams"></p>
            </div>
        </div>
    </section>

    <div
        class="transition-all duration-700 ease-out will-change-transform"
        x-data="{ shown: false }"
        x-init="const io = new IntersectionObserver((es) => { es.forEach(e => { if (e.isIntersecting) { shown = true; io.disconnect(); } }); }, { threshold: 0.06 }); io.observe($el);"
        :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
    >
        <livewire:home-how-it-works />
    </div>

    {{-- Отзывы: snap-карусель до lg, сетка на больших экранах --}}
    <section
        class="space-y-8 transition-all duration-700 ease-out will-change-transform"
        x-data="{
            shown: false,
            tIdx: 0,
            tPrev() { this.tIdx = (this.tIdx + 2) % 3 },
            tNext() { this.tIdx = (this.tIdx + 1) % 3 },
        }"
        x-init="const io = new IntersectionObserver((es) => { es.forEach(e => { if (e.isIntersecting) { shown = true; io.disconnect(); } }); }, { threshold: 0.08 }); io.observe($el);"
        :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
    >
        <h2 class="font-display text-3xl font-bold tracking-tight sm:text-4xl">Отзывы участников</h2>

        <div class="lg:hidden">
            <div class="overflow-hidden rounded-3xl border border-base-300 bg-base-100/50 shadow-sm">
                <div
                    class="flex transition-transform duration-300 ease-out"
                    :style="'transform: translateX(-' + (tIdx * 100) + '%)'"
                >
                    <article class="flex w-full shrink-0 flex-col p-7 sm:p-8">
                        <div class="flex items-center gap-4">
                            <img
                                src="https://ui-avatars.com/api/?name=Анна+К.&background=a4f01d&color=16181d&size=160"
                                alt=""
                                class="h-16 w-16 shrink-0 rounded-full object-cover ring-4 ring-base-300/80"
                                width="64"
                                height="64"
                                loading="lazy"
                            />
                            <div>
                                <p class="font-semibold leading-tight">Анна К.</p>
                                <p class="text-xs text-base-content/60">Участница, продукт</p>
                            </div>
                        </div>
                        <blockquote class="mt-5 grow text-sm leading-relaxed text-base-content/85">
                            «Нашли двух разработчиков за один вечер и подали заявку за 10 минут.»
                        </blockquote>
                    </article>
                    <article class="flex w-full shrink-0 flex-col p-7 sm:p-8">
                        <div class="flex items-center gap-4">
                            <img
                                src="https://ui-avatars.com/api/?name=Михаил+Т.&background=5170ff&color=ffffff&size=160"
                                alt=""
                                class="h-16 w-16 shrink-0 rounded-full object-cover ring-4 ring-base-300/80"
                                width="64"
                                height="64"
                                loading="lazy"
                            />
                            <div>
                                <p class="font-semibold leading-tight">Михаил Т.</p>
                                <p class="text-xs text-base-content/60">Разработчик</p>
                            </div>
                        </div>
                        <blockquote class="mt-5 grow text-sm leading-relaxed text-base-content/85">
                            «Удобно следить за дедлайнами и анонсами без десятка чатов.»
                        </blockquote>
                    </article>
                    <article class="flex w-full shrink-0 flex-col p-7 sm:p-8">
                        <div class="flex items-center gap-4">
                            <img
                                src="https://ui-avatars.com/api/?name=Елена+В.&background=374151&color=f3f4f6&size=160"
                                alt=""
                                class="h-16 w-16 shrink-0 rounded-full object-cover ring-4 ring-base-300/80"
                                width="64"
                                height="64"
                                loading="lazy"
                            />
                            <div>
                                <p class="font-semibold leading-tight">Елена В.</p>
                                <p class="text-xs text-base-content/60">Организатор</p>
                            </div>
                        </div>
                        <blockquote class="mt-5 grow text-sm leading-relaxed text-base-content/85">
                            «Организаторам стало проще модерировать команды и заявки.»
                        </blockquote>
                    </article>
                </div>
            </div>
            <div class="mt-4 flex items-center justify-center gap-3">
                <button type="button" class="btn btn-circle btn-ghost btn-sm border border-base-300" aria-label="Предыдущий отзыв" @click="tPrev()">
                    <x-app-icon icon="heroicons:chevron-left" class="h-5 w-5" />
                </button>
                <div class="flex gap-2">
                    <button type="button" class="h-2.5 w-2.5 rounded-full transition-colors" :class="tIdx === 0 ? 'bg-secondary' : 'bg-base-300'" aria-label="Отзыв 1" @click="tIdx = 0"></button>
                    <button type="button" class="h-2.5 w-2.5 rounded-full transition-colors" :class="tIdx === 1 ? 'bg-secondary' : 'bg-base-300'" aria-label="Отзыв 2" @click="tIdx = 1"></button>
                    <button type="button" class="h-2.5 w-2.5 rounded-full transition-colors" :class="tIdx === 2 ? 'bg-secondary' : 'bg-base-300'" aria-label="Отзыв 3" @click="tIdx = 2"></button>
                </div>
                <button type="button" class="btn btn-circle btn-ghost btn-sm border border-base-300" aria-label="Следующий отзыв" @click="tNext()">
                    <x-app-icon icon="heroicons:chevron-right" class="h-5 w-5" />
                </button>
            </div>
        </div>

        <div class="hidden gap-8 lg:grid lg:grid-cols-3">
            <article class="flex flex-col rounded-3xl border border-base-300 bg-base-100 p-7 shadow-sm transition-all duration-200 hover:border-primary/30 hover:shadow-md sm:p-8">
                <div class="flex items-center gap-4">
                    <img
                        src="https://ui-avatars.com/api/?name=Анна+К.&background=a4f01d&color=16181d&size=160"
                        alt=""
                        class="h-16 w-16 shrink-0 rounded-full object-cover ring-4 ring-base-300/80"
                        width="64"
                        height="64"
                        loading="lazy"
                    />
                    <div>
                        <p class="font-semibold leading-tight">Анна К.</p>
                        <p class="text-xs text-base-content/60">Участница, продукт</p>
                    </div>
                </div>
                <blockquote class="mt-5 grow text-sm leading-relaxed text-base-content/85">
                    «Нашли двух разработчиков за один вечер и подали заявку за 10 минут.»
                </blockquote>
            </article>
            <article class="flex flex-col rounded-3xl border border-base-300 bg-base-100 p-7 shadow-sm transition-all duration-200 hover:border-primary/30 hover:shadow-md sm:p-8">
                <div class="flex items-center gap-4">
                    <img
                        src="https://ui-avatars.com/api/?name=Михаил+Т.&background=5170ff&color=ffffff&size=160"
                        alt=""
                        class="h-16 w-16 shrink-0 rounded-full object-cover ring-4 ring-base-300/80"
                        width="64"
                        height="64"
                        loading="lazy"
                    />
                    <div>
                        <p class="font-semibold leading-tight">Михаил Т.</p>
                        <p class="text-xs text-base-content/60">Разработчик</p>
                    </div>
                </div>
                <blockquote class="mt-5 grow text-sm leading-relaxed text-base-content/85">
                    «Удобно следить за дедлайнами и анонсами без десятка чатов.»
                </blockquote>
            </article>
            <article class="flex flex-col rounded-3xl border border-base-300 bg-base-100 p-7 shadow-sm transition-all duration-200 hover:border-primary/30 hover:shadow-md sm:p-8">
                <div class="flex items-center gap-4">
                    <img
                        src="https://ui-avatars.com/api/?name=Елена+В.&background=374151&color=f3f4f6&size=160"
                        alt=""
                        class="h-16 w-16 shrink-0 rounded-full object-cover ring-4 ring-base-300/80"
                        width="64"
                        height="64"
                        loading="lazy"
                    />
                    <div>
                        <p class="font-semibold leading-tight">Елена В.</p>
                        <p class="text-xs text-base-content/60">Организатор</p>
                    </div>
                </div>
                <blockquote class="mt-5 grow text-sm leading-relaxed text-base-content/85">
                    «Организаторам стало проще модерировать команды и заявки.»
                </blockquote>
            </article>
        </div>
    </section>

    <div class="mt-14 flex justify-center border-t border-base-300/60 px-4 pb-2 pt-10 sm:mt-16 sm:pt-12">
        <a href="{{ route('home') }}" class="block w-1/2 max-w-[50%] shrink-0" aria-label="Хакатонщик — на главную">
            <img
                src="{{ url('/logo_white.svg') }}"
                onerror="this.onerror=null;this.src='{{ url('/logo.svg') }}';"
                alt="Хакатонщик"
                class="block h-auto w-full object-contain group-data-[theme=hackatonshik-light]:hidden"
                loading="lazy"
                decoding="async"
            />
            <img
                src="{{ url('/logo_black.svg') }}"
                onerror="this.onerror=null;this.src='{{ url('/logo.svg') }}';"
                alt="Хакатонщик"
                class="hidden h-auto w-full object-contain group-data-[theme=hackatonshik-light]:block"
                loading="lazy"
                decoding="async"
            />
        </a>
    </div>

    @php
        $schema = [
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'WebSite',
                    'name' => 'Хакатонщик',
                    'url' => url('/'),
                    'potentialAction' => [
                        '@type' => 'SearchAction',
                        'target' => url('/hackatons').'?q={search_term_string}',
                        'query-input' => 'required name=search_term_string',
                    ],
                ],
                [
                    '@type' => 'Organization',
                    'name' => 'Хакатонщик',
                    'url' => url('/'),
                    'logo' => url('/logo.svg'),
                ],
            ],
        ];
    @endphp
    <script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
</div>
@endguest

@auth
<section
    class="mx-auto w-full max-w-7xl space-y-10"
    data-test="home-dashboard"
    aria-labelledby="dashboard-heading"
>
    <header class="space-y-2">
        <h1 id="dashboard-heading" class="font-display text-3xl font-bold sm:text-4xl">Краткая сводка</h1>
        <p class="text-base-content/80">
            Здравствуйте, {{ auth()->user()->fio }}.
        </p>
    </header>

    @if ($showPhoneVerificationBanner)
        <div role="alert" class="alert alert-warning shadow-sm" data-test="dashboard-phone-banner">
            <span>Подтвердите номер телефона, чтобы пользоваться всеми функциями.</span>
            <a href="{{ route('phone.verify.notice') }}" class="btn btn-sm btn-neutral">Подтвердить</a>
        </div>
    @endif

    @if ($unreadNotificationsCount > 0)
        <p class="text-sm text-base-content/80" data-test="dashboard-unread-notifications">
            Непрочитанных уведомлений: <span class="font-semibold tabular-nums">{{ $unreadNotificationsCount }}</span>
            (список в шапке сайта).
        </p>
    @endif

    @if (auth()->user()->isParticipant())
        @if ($participantNextStepTitle !== '')
            <div class="rounded-xl border border-primary/20 bg-primary/10 p-4 shadow-sm">
                <p class="text-xs uppercase tracking-wide text-primary/80">Ваш следующий шаг</p>
                <p class="mt-1 font-semibold">{{ $participantNextStepTitle }}</p>
                <p class="mt-1 text-sm text-base-content/80">{{ $participantNextStepHint }}</p>
                @if ($participantNextStepHref && $participantNextStepLabel)
                    <a href="{{ $participantNextStepHref }}" class="btn btn-primary btn-sm mt-3">{{ $participantNextStepLabel }}</a>
                @endif
            </div>
        @endif

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <x-marycard title="Мои команды" class="card card-border bg-base-100 shadow-sm">
                <p class="text-3xl font-semibold tabular-nums">{{ $teamsCount }}</p>
                <x-slot:menu>
                    <a href="/profile/teams" class="btn btn-ghost btn-sm">Открыть</a>
                </x-slot:menu>
            </x-marycard>
            <x-marycard title="Сертификаты" class="card card-border bg-base-100 shadow-sm">
                <p class="text-3xl font-semibold tabular-nums">{{ $certificatesCount }}</p>
                <x-slot:menu>
                    <a href="/profile/certificates" class="btn btn-ghost btn-sm">Открыть</a>
                </x-slot:menu>
            </x-marycard>
            <x-marycard title="Заявки в команду на рассмотрении" class="card card-border bg-base-100 shadow-sm">
                <p class="text-3xl font-semibold tabular-nums">{{ $pendingTeamApplicationsCount }}</p>
                <p class="mt-2 text-sm text-base-content/70">Заявки, которые вы подали на участие в ролях команд.</p>
                <x-slot:menu>
                    <a href="/profile/teams#pending-team-role-applications" class="btn btn-ghost btn-sm">Открыть</a>
                </x-slot:menu>
            </x-marycard>
            <x-marycard title="Заявки команд на хакатоны (ожидают)" class="card card-border bg-base-100 shadow-sm">
                <p class="text-3xl font-semibold tabular-nums">{{ $pendingHackatonApplicationsCount }}</p>
                <p class="mt-2 text-sm text-base-content/70">По вашим командам, ожидающие решения организатора.</p>
                @if ($pendingHackatonApplicationsCount > 0)
                    <x-slot:menu>
                        <a href="/hackatons" class="btn btn-ghost btn-sm">Каталог</a>
                    </x-slot:menu>
                @endif
            </x-marycard>
        </div>

        @if (count($hackatonApplicationsPreview) > 0)
            <x-marycard title="Заявки команд на хакатоны" class="card card-border bg-base-100 shadow-sm w-full max-w-2xl">
                <ul class="space-y-2">
                    @foreach ($hackatonApplicationsPreview as $row)
                        <li class="flex flex-wrap items-baseline justify-between gap-2 border-b border-base-200 pb-2 last:border-0">
                            <div>
                                <a href="{{ route('hackatons.show', $row['hackaton_id']) }}#participant-hackaton-applications" class="link link-primary font-medium">{{ $row['title'] }}</a>
                                <span class="text-sm text-base-content/70"> — {{ $row['team_title'] }}</span>
                            </div>
                            <span class="badge badge-warning badge-sm">{{ $row['status_label'] }}</span>
                        </li>
                    @endforeach
                </ul>
            </x-marycard>
        @endif

        @if (count($participantHackatonsPreview) > 0)
            <x-marycard title="Хакатоны (ваши и ближайшие публичные)" class="card card-border bg-base-100 shadow-sm w-full max-w-2xl">
                <ul class="space-y-2">
                    @foreach ($participantHackatonsPreview as $row)
                        <li class="flex flex-wrap items-baseline justify-between gap-2 border-b border-base-200 pb-2 last:border-0">
                            <a href="{{ route('hackatons.show', $row['id']) }}" class="link link-primary font-medium">{{ $row['title'] }}</a>
                            @if ($row['start_at'])
                                <span class="text-sm text-base-content/70">{{ $row['start_at'] }}</span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </x-marycard>
        @endif

        <div class="flex flex-wrap gap-3">
            <a href="/hackatons" class="btn btn-primary">Хакатоны</a>
            <a href="/teams/create" class="btn btn-outline">Создать команду</a>
            <a href="/profile/teams" class="btn btn-outline">Мои команды</a>
        </div>
    @elseif (auth()->user()->isOrganizer())
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <x-marycard title="Мои хакатоны" class="card card-border bg-base-100 shadow-sm">
                <p class="text-3xl font-semibold tabular-nums">{{ $hackatonsCount }}</p>
                <x-slot:menu>
                    <a href="/profile/hackatons" class="btn btn-ghost btn-sm">Открыть</a>
                </x-slot:menu>
            </x-marycard>
            <x-marycard title="Заявки команд на рассмотрении" class="card card-border bg-base-100 shadow-sm">
                <p class="text-3xl font-semibold tabular-nums">{{ $pendingHackatonApplicationsCount }}</p>
                <p class="mt-2 text-sm text-base-content/70">По всем вашим хакатонам.</p>
                @if ($pendingHackatonApplicationsCount > 0 && $organizerFirstPendingHackatonId)
                    <x-slot:menu>
                        <a href="{{ route('hackatons.show', $organizerFirstPendingHackatonId) }}?applications_status=pending#organizer-team-applications" class="btn btn-ghost btn-sm">Рассмотреть</a>
                    </x-slot:menu>
                @endif
            </x-marycard>
        </div>
        <div class="flex flex-wrap gap-3">
            @if ($pendingHackatonApplicationsCount > 0 && $organizerFirstPendingHackatonId)
                <a href="{{ route('hackatons.show', $organizerFirstPendingHackatonId) }}?applications_status=pending#organizer-team-applications" class="btn btn-primary">Рассмотреть заявки</a>
            @endif
            <a href="/profile/hackatons" class="btn {{ $pendingHackatonApplicationsCount > 0 && $organizerFirstPendingHackatonId ? 'btn-outline' : 'btn-primary' }}">Мои хакатоны</a>
            <a href="/hackatons/create" class="btn btn-outline">Создать хакатон</a>
            <a href="/hackatons" class="btn btn-outline">Каталог хакатонов</a>
        </div>
    @elseif (auth()->user()->isJudge())
        @if ($judgeHackatonsCount === 0)
            <div class="rounded-xl border border-base-200 bg-base-100 p-6 text-center shadow-sm" data-test="judge-dashboard-empty">
                <p class="text-base-content/80">У вас пока нет назначенных хакатонов. Когда организатор добавит вас в судьи, события появятся здесь.</p>
                <a href="/hackatons" class="btn btn-primary mt-4">Каталог хакатонов</a>
            </div>
        @else
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <x-marycard title="Назначенные хакатоны" class="card card-border bg-base-100 shadow-sm">
                    <p class="text-3xl font-semibold tabular-nums">{{ $judgeHackatonsCount }}</p>
                    <x-slot:menu>
                        <a href="/hackatons" class="btn btn-ghost btn-sm">Каталог</a>
                    </x-slot:menu>
                </x-marycard>
            </div>
            @if (count($judgeHackatonsPreview) > 0)
                <x-marycard title="Ближайшие по дате начала" class="card card-border bg-base-100 shadow-sm w-full max-w-2xl">
                    <ul class="space-y-2">
                        @foreach ($judgeHackatonsPreview as $row)
                            <li class="flex flex-wrap items-center justify-between gap-2 border-b border-base-200 pb-2 last:border-0">
                                <div class="flex min-w-0 flex-1 flex-wrap items-baseline gap-2">
                                    <a href="{{ route('hackatons.show', $row['id']) }}" class="link link-primary font-medium">{{ $row['title'] }}</a>
                                    @if ($row['start_at'])
                                        <span class="text-sm text-base-content/70">{{ $row['start_at'] }}</span>
                                    @endif
                                </div>
                                <a href="{{ route('hackatons.show', $row['id']) }}#hackaton-cases" class="btn btn-ghost btn-xs shrink-0">К кейсам</a>
                            </li>
                        @endforeach
                    </ul>
                </x-marycard>
            @endif
        @endif
        <div class="flex flex-wrap gap-3">
            <a href="/hackatons" class="btn btn-primary">Все хакатоны</a>
            <a href="/profile" class="btn btn-outline">Профиль</a>
        </div>
    @elseif (auth()->user()->isAdmin())
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <x-marycard title="Пользователей" class="card card-border bg-base-100 shadow-sm">
                <p class="text-3xl font-semibold tabular-nums">{{ $usersCount }}</p>
            </x-marycard>
            <x-marycard title="Хакатонов" class="card card-border bg-base-100 shadow-sm">
                <p class="text-3xl font-semibold tabular-nums">{{ $adminHackatonsCount }}</p>
            </x-marycard>
            <x-marycard title="Партнёров" class="card card-border bg-base-100 shadow-sm">
                <p class="text-3xl font-semibold tabular-nums">{{ $adminPartnersCount }}</p>
            </x-marycard>
            <x-marycard title="Заявок команд на рассмотрении" class="card card-border bg-base-100 shadow-sm">
                <p class="text-3xl font-semibold tabular-nums">{{ $adminPendingApplicationsCount }}</p>
                <p class="mt-2 text-sm text-base-content/70">По всей платформе.</p>
            </x-marycard>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="/admin" class="btn btn-primary">Админ-панель</a>
            <a href="/hackatons" class="btn btn-outline">Хакатоны</a>
            <a href="/profile" class="btn btn-outline">Профиль</a>
        </div>
    @else
        <p class="text-base-content/80">Выберите раздел в меню слева или перейдите к хакатонам.</p>
        <div class="flex flex-wrap gap-3">
            <a href="/hackatons" class="btn btn-primary">Хакатоны</a>
            <a href="/profile" class="btn btn-outline">Профиль</a>
        </div>
    @endif
</section>
@endauth
