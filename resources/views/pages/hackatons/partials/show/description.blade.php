<section id="hackaton-panel-description" role="tabpanel" data-tab-panel="hackaton" data-tab-value="description">
    <div id="hackaton-tab-description" class="scroll-mt-24" tabindex="-1"></div>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 card bg-base-100 border border-base-200 shadow-sm">
            <div class="p-4 pb-0">
                <x-image-carousel
                    carousel-id="hackaton-hero-carousel"
                    :items="$hackatonGalleryImages"
                    aspect-class="aspect-video"
                    empty-text="Изображения хакатона отсутствуют" />
            </div>
            <div class="card-body">
                <h1 class="card-title text-3xl">{{ $hackaton->title }}</h1>
                <div class="markdown-body">
                    {!! \App\Support\SafeMarkdown::toHtml($hackaton->description ?? 'Описание отсутствует.') !!}
                </div>
            </div>
        </div>

        <div class="card bg-base-100 border border-base-200 shadow-sm">
            <div class="card-body space-y-4">
                <h2 class="card-title text-lg">Информация о хакатоне</h2>
                <div class="rounded-xl border border-primary/20 bg-primary/10 p-4">
                    <p class="text-xs uppercase tracking-wide text-primary/80">Ваш следующий шаг</p>
                    <p class="mt-1 font-semibold">{{ $nextStepTitle }}</p>
                    <p class="mt-1 text-sm text-base-content/80">{{ $nextStepHint }}</p>
                </div>
                <div class="grid grid-cols-1 gap-2 text-sm">
                    <div class="flex items-center justify-between rounded-lg border border-base-300 px-3 py-2">
                        <span class="text-base-content/70">Организатор</span>
                        <span class="font-medium">{{ $hackaton->user->nickname ?? $hackaton->user->name ?? $hackaton->user->email }}</span>
                    </div>
                    <div class="flex items-center justify-between rounded-lg border border-base-300 px-3 py-2">
                        <span class="text-base-content/70">Старт</span>
                        <span class="font-medium">{{ \Illuminate\Support\Carbon::parse($hackaton->start_at)->format('d.m.Y H:i') }}</span>
                    </div>
                    <div class="flex items-center justify-between rounded-lg border border-base-300 px-3 py-2">
                        <span class="text-base-content/70">Финиш</span>
                        <span class="font-medium">{{ \Illuminate\Support\Carbon::parse($hackaton->end_at)->format('d.m.Y H:i') }}</span>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div class="rounded-xl border border-base-300 p-3 text-center">
                        <p class="text-xs text-base-content/70">Команд</p>
                        <p class="text-2xl font-semibold">{{ $teamsCount }}</p>
                    </div>
                    <div class="rounded-xl border border-base-300 p-3 text-center">
                        <p class="text-xs text-base-content/70">Участников</p>
                        <p class="text-2xl font-semibold">{{ $participantsCount }}</p>
                    </div>
                </div>

                @auth
                    @if ($hackaton->user_id !== auth()->id())
                        <div class="divider my-1"></div>
                        <a href="{{ route('profile.hackatons.hub', $hackaton) }}" class="btn btn-sm btn-outline w-full">
                            Открыть мой кабинет участника
                        </a>
                        @if ($myApplicationsByTeam->isNotEmpty())
                            <div id="participant-hackaton-applications" class="space-y-2">
                                <p class="text-sm font-medium">Ваши заявки</p>
                                @foreach ($myApplicationsByTeam as $myApplication)
                                    <div class="rounded-xl border border-base-300 p-2 text-sm">
                                        <p>
                                            Команда:
                                            <span class="font-medium">{{ $myApplication->team->title }}</span>
                                        </p>
                                        <div class="mt-1 flex items-center justify-between gap-2">
                                            <span class="badge badge-{{ $myApplication->status->isAccepted() ? 'success' : ($myApplication->status->isRejected() ? 'error' : 'warning') }}">
                                                {{ $myApplication->status->label() }}
                                            </span>
                                            @if ($myApplication->status->isPending())
                                                <form method="POST" action="{{ route('hackaton.applications.destroy', $myApplication) }}"
                                                    onsubmit="return confirm('Отменить поданную заявку команды?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-xs btn-ghost">Отменить</button>
                                                </form>
                                            @endif
                                        </div>
                                        <p class="mt-1 text-xs text-base-content/70">
                                            @if ($myApplication->status->isAccepted())
                                                Команда допущена. Переходите к блоку «Кейсы» и отправляйте решение.
                                            @elseif ($myApplication->status->isRejected())
                                                Заявка отклонена. Проверьте требования хакатона и подайте новую заявку другой командой.
                                            @else
                                                Заявка на рассмотрении. Мы уведомим вас после решения организатора.
                                            @endif
                                        </p>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        @if ($teamsWithoutApplication->isNotEmpty())
                            <x-application-modal type="hackaton" :id="$hackaton->id" :teams="$teamsWithoutApplication"
                                title="Подать заявку команды на хакатон"
                                action="{{ route('hackaton.applications.store') }}" />
                        @elseif ($availableTeams->isNotEmpty())
                            <p class="text-sm text-base-content/70">
                                Все ваши команды уже подали заявки на этот хакатон.
                            </p>
                        @else
                            <x-empty-state
                                embedded
                                title="Нет команд для заявки"
                                description="Создайте команду для этого хакатона, чтобы подать заявку на участие."
                                icon="heroicons:user-group"
                                action-href="/teams/create"
                                action-label="Создать команду"
                            />
                        @endif
                    @endif
                @endauth
            </div>
        </div>
    </div>
</section>
