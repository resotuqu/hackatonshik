    @php
        $hackatonGalleryImages = $hackaton->images;
        if ($hackatonGalleryImages->isEmpty() && filled($hackaton->image_url)) {
            $hackatonGalleryImages = collect([
                (object) [
                    'path' => $hackaton->image_url,
                    'alt' => $hackaton->title,
                ],
            ]);
        }
        $fieldTypeLabels = [
            'text' => 'Короткий текст',
            'url' => 'Ссылка',
            'textarea' => 'Большой текст',
            'file' => 'Файл',
        ];
        $teamsCount = $hackaton->teams->count();
        $participantsCount = $hackaton->teams->sum(fn($team) => $team->roles->whereNotNull('user_id')->count());
        $myTeamIds = auth()->check() ? $availableTeams->pluck('id') : collect();
        $myApplicationsByTeam = auth()->check()
            ? $hackaton->applications->whereIn('team_id', $myTeamIds)->keyBy('team_id')
            : collect();
        $teamsWithoutApplication = auth()->check()
            ? $availableTeams->reject(fn ($team) => $myApplicationsByTeam->has($team->id))->values()
            : collect();
        $announcementTemplates = [
            'start' => 'Старт хакатона',
            'deadline' => 'Напоминание о дедлайне',
            'results' => 'Публикация результатов',
        ];
        $modals = [
            'announcement_create' => 'organizer-announcement-create-modal',
            'case_create' => 'organizer-case-create-modal',
            'judge_invite' => 'organizer-judge-invite-modal',
            'judge_assign' => 'organizer-judge-assign-modal',
            'certificate_upload' => 'organizer-certificate-upload-modal',
        ];
        $issuedCertificatesByUser = $hackaton->certificates->groupBy('user_id');
        $nextStepTitle = 'Авторизуйтесь';
        $nextStepHint = 'Войдите в аккаунт, чтобы подавать заявки и отправлять решения кейсов.';

        $plainDescription = strip_tags(\App\Support\SafeMarkdown::toHtml($hackaton->description ?? ''));
        $plainDescription = preg_replace('/\s+/u', ' ', $plainDescription ?? '') ?? '';
        $seoDescription = trim(mb_substr($plainDescription !== '' ? $plainDescription : 'Онлайн и офлайн хакатон на платформе «Хакатонщик».', 0, 180, 'UTF-8'));

        $heroImage = null;
        if ($hackatonGalleryImages->isNotEmpty()) {
            $first = $hackatonGalleryImages->first();
            $heroImage = isset($first->path) ? (str_starts_with((string) $first->path, 'http') ? $first->path : asset('storage/'.$first->path)) : null;
        } elseif (filled($hackaton->image_url)) {
            $heroImage = str_starts_with((string) $hackaton->image_url, 'http') ? $hackaton->image_url : asset('storage/'.$hackaton->image_url);
        }

        if (auth()->check()) {
            if ($isOrganizer) {
                $nextStepTitle = 'Управляйте хакатоном';
                $nextStepHint = 'Публикуйте анонсы и кейсы, а затем рассматривайте заявки команд.';
            } elseif ($isAssignedJudge) {
                $nextStepTitle = 'Оценивайте решения';
                $nextStepHint = 'Вы назначены судьей: используйте блок кейсов для выставления оценок и комментариев.';
            } elseif ($availableTeams->isEmpty()) {
                $nextStepTitle = 'Создайте команду';
                $nextStepHint = 'Без команды нельзя подать заявку на участие в хакатоне.';
            } elseif ($teamsWithoutApplication->isNotEmpty()) {
                $nextStepTitle = 'Подайте заявку команды';
                $nextStepHint = 'Выберите команду и отправьте заявку на участие прямо на этой странице.';
            } elseif ($myApplicationsByTeam->where('status', \App\Enums\ApplicationStatus::ACCEPTED)->isNotEmpty()) {
                $nextStepTitle = 'Отправьте решение кейса';
                $nextStepHint = 'Команда допущена: перейдите к блоку кейсов и отправьте ответы.';
            } else {
                $nextStepTitle = 'Ожидайте модерацию';
                $nextStepHint = 'Заявка уже отправлена. Следите за обновлением статуса ниже.';
            }
        }
    @endphp

    @section('title', $hackaton->title)
    @section('meta_description', $seoDescription)
    @section('canonical_url', route('hackatons.show', $hackaton))
    @if ($heroImage)
        @section('og_image', $heroImage)
    @endif

    <div class="mx-auto w-full max-w-7xl space-y-6">
        <div class="text-sm breadcrumbs">
            <ul>
                <li><a href="/">Главная</a></li>
                <li><a href="/hackatons">Хакатоны</a></li>
                <li class="opacity-70">{{ $hackaton->title }}</li>
            </ul>
        </div>

        <div class="tabs tabs-boxed w-full overflow-x-auto scroll-smooth rounded-2xl border border-base-300/60 bg-base-200/50 p-1 shadow-inner focus-within:ring-2 focus-within:ring-primary/30 focus-within:ring-offset-2" role="tablist" aria-label="Разделы хакатона" data-tab-list="hackaton">
            <button type="button" class="tab tab-active" role="tab" aria-selected="true" aria-controls="hackaton-panel-description" data-tab-trigger="hackaton" data-tab-value="description">
                Описание
            </button>
            <button type="button" class="tab" role="tab" aria-selected="false" aria-controls="hackaton-panel-documents" data-tab-trigger="hackaton" data-tab-value="documents">
                Документы
            </button>
            <button type="button" class="tab" role="tab" aria-selected="false" aria-controls="hackaton-panel-announcements" data-tab-trigger="hackaton" data-tab-value="announcements">
                Анонсы
            </button>
            <button type="button" class="tab" role="tab" aria-selected="false" aria-controls="hackaton-panel-cases" data-tab-trigger="hackaton" data-tab-value="cases">
                Кейсы
            </button>
            <button type="button" class="tab" role="tab" aria-selected="false" aria-controls="hackaton-panel-participants" data-tab-trigger="hackaton" data-tab-value="participants">
                Участники
            </button>
            @if($isOrganizer || $isAssignedJudge)
                <button type="button" class="tab" role="tab" aria-selected="false" aria-controls="hackaton-panel-organization" data-tab-trigger="hackaton" data-tab-value="organization">
                    Организация
                </button>
            @endif
        </div>

        <section id="hackaton-panel-description" role="tabpanel" data-tab-panel="hackaton" data-tab-value="description">
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
                    <div class="prose max-w-none prose-sm sm:prose-base">
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

        <section id="hackaton-panel-documents" role="tabpanel" class="hidden" data-tab-panel="hackaton" data-tab-value="documents">
        <div class="card bg-base-100 border border-base-200 shadow-sm">
            <div class="card-body">
                <h2 class="card-title text-xl">Документы хакатона</h2>
                @if ($hackaton->documents->isEmpty())
                    <x-empty-state
                        embedded
                        title="Документов пока нет"
                        description="Проверяйте раздел позже или уточните детали у организатора."
                        icon="heroicons:document-text"
                    />
                @else
                    <div class="space-y-3">
                        @foreach ($hackaton->documents as $document)
                            <div class="rounded-xl border border-base-300 p-4">
                                <p class="font-semibold">{{ $document->name }}</p>
                                <p class="text-sm text-base-content/70 mt-1">{{ $document->description }}</p>
                                <div class="mt-3">
                                    <a class="btn btn-sm btn-outline"
                                        href="{{ asset('storage/' . $document->file_url) }}"
                                        target="_blank"
                                        rel="noopener noreferrer">
                                        Открыть документ
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
        </section>

        <section id="hackaton-panel-announcements" role="tabpanel" class="hidden" data-tab-panel="hackaton" data-tab-value="announcements">
        <div class="card bg-base-100 border border-base-200 shadow-sm">
            <div class="card-body space-y-4">
                <div class="flex items-center justify-between gap-3">
                    <h2 class="card-title text-xl">Анонсы</h2>
                    @if($isOrganizer)
                        <x-organizer-action-modal
                            :modal-id="$modals['announcement_create']"
                            button-label="Новый анонс"
                            title="Опубликовать анонс"
                            description="Подготовьте текст анонса и при необходимости запланируйте публикацию.">
                            <form method="POST" enctype="multipart/form-data" action="{{ route('hackatons.announcements.store', $hackaton) }}" class="space-y-3">
                                @csrf
                                <input type="hidden" name="_open_modal" value="{{ $modals['announcement_create'] }}">
                                <input name="title" class="input input-bordered w-full" placeholder="Заголовок анонса" required autofocus>
                                <textarea name="body" class="textarea textarea-bordered w-full" rows="4" placeholder="Текст анонса" required></textarea>
                                <div class="space-y-2">
                                    <label class="label p-0">
                                        <span class="label-text">Изображения анонса</span>
                                    </label>
                                    <input name="images[]" type="file" multiple accept="image/*" class="file-input file-input-bordered w-full">
                                    <p class="text-xs text-base-content/70">Можно загрузить несколько изображений (до 10 файлов).</p>
                                </div>
                                <div class="grid grid-cols-1 gap-3 md:grid-cols-3">
                                    <select name="template_key" class="select select-bordered">
                                        <option value="">Без шаблона</option>
                                        @foreach($announcementTemplates as $templateKey => $templateName)
                                            <option value="{{ $templateKey }}">{{ $templateName }}</option>
                                        @endforeach
                                    </select>
                                    <input name="published_at" type="datetime-local" class="input input-bordered">
                                    <label class="label cursor-pointer justify-start gap-2">
                                        <input type="checkbox" name="is_draft" value="1" class="checkbox checkbox-sm">
                                        <span class="label-text">Сохранить как черновик</span>
                                    </label>
                                </div>
                                <button class="btn btn-primary btn-sm">Сохранить анонс</button>
                            </form>
                        </x-organizer-action-modal>
                    @endif
                </div>

                @if($hackaton->announcements->isEmpty())
                    <x-empty-state
                        embedded
                        title="Анонсов пока нет"
                        description="Проверяйте раздел перед стартом и во время хакатона."
                        icon="heroicons:megaphone"
                    />
                @else
                    <div class="space-y-3">
                        @foreach($hackaton->announcements as $announcement)
                            <div class="rounded-xl border border-base-300 p-4">
                                <div class="flex flex-wrap items-start justify-between gap-2">
                                    <div>
                                        <p class="font-semibold">{{ $announcement->title }}</p>
                                        <p class="text-xs text-base-content/70">
                                            {{ $announcement->published_at?->format('d.m.Y H:i') ?? '—' }}
                                        </p>
                                        @if($isOrganizer && $announcement->is_draft)
                                            <span class="badge badge-warning badge-xs">Черновик</span>
                                        @elseif($isOrganizer && $announcement->published_at?->isFuture())
                                            <span class="badge badge-info badge-xs">Запланировано</span>
                                        @endif
                                    </div>
                                    @if($isOrganizer)
                                        <form method="POST" action="{{ route('hackatons.announcements.destroy', [$hackaton, $announcement]) }}"
                                            onsubmit="return confirm('Удалить анонс?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-xs btn-error">Удалить</button>
                                        </form>
                                    @endif
                                </div>
                                <div class="prose max-w-none prose-sm mt-2">
                                    {!! \App\Support\SafeMarkdown::toHtml($announcement->body) !!}
                                </div>
                                @if ($announcement->images->isNotEmpty())
                                    <div class="mt-3">
                                        <x-image-carousel
                                            :carousel-id="'announcement-carousel-'.$announcement->id"
                                            :items="$announcement->images"
                                            aspect-class="aspect-[16/9]"
                                            empty-text="Изображения отсутствуют" />
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
        </section>

        <section id="hackaton-panel-cases" role="tabpanel" class="hidden space-y-4" data-tab-panel="hackaton" data-tab-value="cases">
        <div class="card bg-base-100 border border-base-200 shadow-sm">
            <div class="card-body space-y-4">
                <h2 class="card-title text-xl">Кейсы</h2>

                @if($hackaton->cases->isEmpty())
                    <x-empty-state
                        embedded
                        title="Кейсов пока нет"
                        description="Организатор ещё не опубликовал задания для этого хакатона."
                        icon="heroicons:puzzle-piece"
                    />
                @else
                    <div class="space-y-4">
                        @foreach($hackaton->cases as $case)
                            @if($case->isPublishedNow() || $isOrganizer)
                                <div class="rounded-xl border border-base-300 p-4 space-y-4">
                                    <div class="flex flex-wrap items-start justify-between gap-4">
                                        <div class="flex-1 space-y-3">
                                            @if($case->images->isNotEmpty())
                                                <div class="max-w-md">
                                                    <x-image-carousel
                                                        :carousel-id="'case-carousel-'.$case->id"
                                                        :items="$case->images"
                                                        aspect-class="aspect-video"
                                                        empty-text="Изображения кейса отсутствуют" />
                                                </div>
                                            @endif
                                            <div>
                                                <h3 class="text-xl font-bold">{{ $case->title }}</h3>
                                                <div class="prose prose-sm max-w-none text-base-content/80">
                                                    {!! \App\Support\SafeMarkdown::toHtml($case->description ?? 'Описание отсутствует.') !!}
                                                </div>
                                                <p class="text-xs text-base-content/50 mt-2">
                                                    Публикация: {{ $case->publish_at?->format('d.m.Y H:i') ?? 'сразу' }} |
                                                    Дедлайн: {{ $case->deadline_at?->format('d.m.Y H:i') ?? 'не задан' }}
                                                </p>
                                            </div>
                                        </div>
                                        @if($isOrganizer)
                                            <div class="flex flex-wrap gap-2">
                                                <form method="POST" action="{{ route('hackatons.cases.destroy', [$hackaton, $case]) }}"
                                                    onsubmit="return confirm('Удалить кейс?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-xs btn-error">Удалить кейс</button>
                                                </form>
                                            </div>
                                        @endif
                                    </div>

                                    @auth
                                        @php
                                            $myJoinedTeams = $submitterTeams->where('hackaton_case_id', $case->id);
                                            $canJoinAnyTeam = $submitterTeams->whereNull('hackaton_case_id')->isNotEmpty();
                                            $joinedAnyTeam = $submitterTeams->where('hackaton_case_id', $case->id)->isNotEmpty();
                                        @endphp

                                        @if($joinedAnyTeam && !empty($case->resources_json))
                                            <div class="rounded-xl bg-info/10 border border-info/20 p-4 space-y-2">
                                                <h4 class="font-semibold text-info flex items-center gap-2">
                                                    <x-app-icon icon="heroicons:link" class="h-4 w-4" />
                                                    Полезные ссылки для участников
                                                </h4>
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                                    @foreach($case->resources_json as $resource)
                                                        <a href="{{ $resource['url'] }}" target="_blank" class="flex items-center gap-2 p-2 rounded-lg bg-base-100 border border-info/10 hover:border-info/30 transition-colors">
                                                            <x-app-icon icon="heroicons:chat-bubble-left-right" class="h-4 w-4 text-info" />
                                                            <span class="text-sm font-medium">{{ $resource['label'] ?? 'Ссылка на чат/ресурс' }}</span>
                                                        </a>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif

                                        @if($canJoinAnyTeam && !$isOrganizer)
                                            <div class="rounded-xl border border-primary/20 bg-primary/5 p-4 flex flex-col md:flex-row items-center justify-between gap-4">
                                                <div>
                                                    <h4 class="font-semibold text-primary">Присоединиться к кейсу</h4>
                                                    <p class="text-xs text-base-content/70">Выберите вашу одобренную команду, чтобы начать работу над этим кейсом.</p>
                                                </div>
                                                <form method="POST" action="{{ route('hackatons.cases.join', [$hackaton, $case]) }}" class="flex items-center gap-2 w-full md:w-auto">
                                                    @csrf
                                                    <select name="team_id" class="select select-bordered select-sm flex-1 md:w-48" required>
                                                        <option value="">Выберите команду</option>
                                                        @foreach($submitterTeams->whereNull('hackaton_case_id') as $team)
                                                            <option value="{{ $team->id }}">{{ $team->title }}</option>
                                                        @endforeach
                                                    </select>
                                                    <button class="btn btn-sm btn-primary">Вступить</button>
                                                </form>
                                            </div>
                                        @endif
                                    @endauth

                                    @if($case->fields->isNotEmpty())
                                        <div class="collapse collapse-arrow border border-base-200 bg-base-100 rounded-xl overflow-hidden">
                                            <input type="checkbox" /> 
                                            <div class="collapse-title text-sm font-medium py-2 min-h-0">
                                                Требования к решению (поля)
                                            </div>
                                            <div class="collapse-content px-0">
                                                <table class="table table-sm">
                                                    <thead>
                                                        <tr>
                                                            <th>Поле</th>
                                                            <th>Тип</th>
                                                            <th>Обязательное</th>
                                                            @if($isOrganizer)
                                                                <th></th>
                                                            @endif
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($case->fields as $field)
                                                            <tr>
                                                                <td>{{ $field->label }}</td>
                                                                <td>{{ $fieldTypeLabels[$field->type] ?? $field->type }}</td>
                                                                <td>{{ $field->is_required ? 'Да' : 'Нет' }}</td>
                                                                @if($isOrganizer)
                                                                    <td class="text-right">
                                                                        <form method="POST" action="{{ route('hackatons.cases.fields.destroy', [$hackaton, $case, $field]) }}"
                                                                            onsubmit="return confirm('Удалить поле?');">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <button class="btn btn-xs btn-ghost text-error">Удалить</button>
                                                                        </form>
                                                                    </td>
                                                                @endif
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    @endif

                                    @auth
                                        @if(!$isOrganizer && $case->isOpenForSubmission() && $case->fields->isNotEmpty())
                                            @php
                                                $isInDevStatus = $hackaton->status === \App\Enums\HackatonStatus::IN_PROGRESS;
                                            @endphp

                                            @if($submitterTeams->where('hackaton_case_id', $case->id)->isNotEmpty())
                                                <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">
                                                    @php
                                                        $personalSubmission = $case->submissions->where('user_id', auth()->id())->where('team_id', null)->first();
                                                        $personalAnswers = $personalSubmission ? $personalSubmission->answers->keyBy('hackaton_case_field_id') : collect();
                                                    @endphp
                                                    <form method="POST" enctype="multipart/form-data"
                                                        action="{{ route('hackatons.cases.submissions.store', [$hackaton, $case]) }}"
                                                        class="space-y-3 rounded-xl border border-base-300 p-4 bg-base-100 shadow-sm">
                                                        @csrf
                                                        <div class="flex items-center justify-between border-b pb-2">
                                                            <h4 class="font-bold text-lg">Личное решение</h4>
                                                            @if($personalSubmission)
                                                                <span class="badge badge-success">Отправлено</span>
                                                            @endif
                                                        </div>
                                                        <input type="hidden" name="scope" value="user">
                                                        <div class="space-y-3">
                                                            @foreach($case->fields as $field)
                                                                @php
                                                                    $ans = $personalAnswers->get($field->id);
                                                                    $isUrlField = $field->type === 'url';
                                                                    $isBlockedUrl = $isUrlField && !$isInDevStatus;
                                                                @endphp
                                                                
                                                                <div class="form-control">
                                                                    <label class="label py-1">
                                                                        <span class="label-text font-semibold">{{ $field->label }} @if($field->is_required)*@endif</span>
                                                                    </label>
                                                                    
                                                                    @if($field->type === 'file')
                                                                        <input type="file" name="files[{{ $field->id }}]" class="file-input file-input-bordered w-full file-input-sm">
                                                                        @if($ans && $ans->file_path)
                                                                            <div class="mt-1 flex items-center gap-2 text-xs text-success">
                                                                                <x-app-icon icon="heroicons:check-circle" class="h-4 w-4" />
                                                                                <span>Файл загружен: <a href="{{ asset('storage/' . $ans->file_path) }}" target="_blank" class="link">Посмотреть</a></span>
                                                                            </div>
                                                                        @endif
                                                                    @elseif($field->type === 'textarea')
                                                                        <textarea name="answers[{{ $field->id }}]" rows="3" class="textarea textarea-bordered w-full textarea-sm">{{ old("answers.{$field->id}", $ans?->value_text) }}</textarea>
                                                                    @else
                                                                        <div class="relative">
                                                                            <input
                                                                                type="{{ $isUrlField ? 'url' : 'text' }}"
                                                                                name="answers[{{ $field->id }}]"
                                                                                value="{{ old("answers.{$field->id}", $ans?->value_text) }}"
                                                                                @disabled($isBlockedUrl)
                                                                                class="input input-bordered w-full input-sm @if($isBlockedUrl) bg-base-200 @endif"
                                                                                @if($isUrlField) placeholder="https://..." @endif>
                                                                            @if($isBlockedUrl)
                                                                                <div class="mt-1 flex items-center gap-1 text-[10px] text-warning uppercase font-bold tracking-wider">
                                                                                    <x-app-icon icon="heroicons:lock-closed" class="h-3 w-3" />
                                                                                    Будет доступно в статусе «В процессе»
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                        <button class="btn btn-sm btn-primary w-full shadow-md">
                                                            {{ $personalSubmission ? 'Обновить решение' : 'Отправить решение' }}
                                                        </button>
                                                    </form>

                                                    <div class="space-y-4">
                                                        @foreach($submitterTeams->where('hackaton_case_id', $case->id) as $team)
                                                            @php
                                                                $teamSubmission = $case->submissions->where('team_id', $team->id)->first();
                                                                $teamAnswers = $teamSubmission ? $teamSubmission->answers->keyBy('hackaton_case_field_id') : collect();
                                                            @endphp
                                                            <form method="POST" enctype="multipart/form-data"
                                                                action="{{ route('hackatons.cases.submissions.store', [$hackaton, $case]) }}"
                                                                class="space-y-3 rounded-xl border border-base-300 p-4 bg-base-50/50">
                                                                @csrf
                                                                <div class="flex items-center justify-between border-b border-base-200 pb-2">
                                                                    <h4 class="font-bold">От имени команды «{{ $team->title }}»</h4>
                                                                    @if($teamSubmission)
                                                                        <span class="badge badge-success badge-sm">Отправлено</span>
                                                                    @endif
                                                                </div>
                                                                <input type="hidden" name="scope" value="team">
                                                                <input type="hidden" name="team_id" value="{{ $team->id }}">
                                                                
                                                                <div class="space-y-3">
                                                                    @foreach($case->fields as $field)
                                                                        @php
                                                                            $ans = $teamAnswers->get($field->id);
                                                                            $isUrlField = $field->type === 'url';
                                                                            $isBlockedUrl = $isUrlField && !$isInDevStatus;
                                                                        @endphp
                                                                        <div class="form-control">
                                                                            <label class="label py-1">
                                                                                <span class="label-text text-xs font-semibold">{{ $field->label }} @if($field->is_required)*@endif</span>
                                                                            </label>
                                                                            @if($field->type === 'file')
                                                                                <input type="file" name="files[{{ $field->id }}]" class="file-input file-input-bordered file-input-sm w-full">
                                                                                @if($ans && $ans->file_path)
                                                                                    <div class="mt-1 flex items-center gap-1 text-[10px] text-success">
                                                                                        <x-app-icon icon="heroicons:check" class="h-3 w-3" />
                                                                                        <span>Файл есть: <a href="{{ asset('storage/' . $ans->file_path) }}" target="_blank" class="link">Просмотр</a></span>
                                                                                    </div>
                                                                                @endif
                                                                            @elseif($field->type === 'textarea')
                                                                                <textarea name="answers[{{ $field->id }}]" rows="2" class="textarea textarea-bordered textarea-sm w-full">{{ old("answers.{$field->id}", $ans?->value_text) }}</textarea>
                                                                            @else
                                                                                <div class="relative">
                                                                                    <input
                                                                                        type="{{ $isUrlField ? 'url' : 'text' }}"
                                                                                        name="answers[{{ $field->id }}]"
                                                                                        value="{{ old("answers.{$field->id}", $ans?->value_text) }}"
                                                                                        @disabled($isBlockedUrl)
                                                                                        class="input input-bordered input-sm w-full @if($isBlockedUrl) bg-base-200 @endif">
                                                                                    @if($isBlockedUrl)
                                                                                        <div class="mt-1 text-[9px] text-warning font-bold">ТОЛЬКО В СТАТУСЕ «В ПРОЦЕССЕ»</div>
                                                                                    @endif
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                                <button class="btn btn-xs btn-primary w-full mt-2">
                                                                    {{ $teamSubmission ? 'Обновить командное решение' : 'Отправить командное решение' }}
                                                                </button>
                                                            </form>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @else
                                                <div class="alert alert-info shadow-sm">
                                                    <x-app-icon icon="heroicons:information-circle" class="h-5 w-5" />
                                                    <div class="flex flex-col">
                                                        <span class="font-bold">Вы не выбрали этот кейс</span>
                                                        <span class="text-xs">Для отправки решения ваша команда должна сначала «Присоединиться» к этому кейсу выше.</span>
                                                    </div>
                                                </div>
                                            @endif
                                        @endif
                                    @endauth

                                    @if(($isOrganizer || $isAssignedJudge) && $case->submissions->isNotEmpty())
                                        <div class="rounded-xl border border-base-300 p-3 space-y-3">
                                            <p class="font-medium">Оценивание решений</p>
                                            @foreach($case->submissions as $submission)
                                                <div class="rounded-lg border border-base-200 p-3 bg-base-50/50">
                                                    <div class="flex items-center justify-between mb-2">
                                                        <span class="font-medium text-sm">
                                                            {{ $submission->team?->title ?? ($submission->user?->nickname ?? $submission->user?->email ?? 'Личное решение') }}
                                                        </span>
                                                        <span class="text-xs text-base-content/50">
                                                            {{ $submission->submitted_at?->format('d.m.Y H:i') }}
                                                        </span>
                                                    </div>

                                                    @if($submission->answers->isNotEmpty())
                                                        <div class="space-y-2 mb-4">
                                                            @foreach($submission->answers as $answer)
                                                                <div class="text-sm">
                                                                    <p class="text-xs font-semibold text-base-content/60">{{ $answer->field->label }}:</p>
                                                                    @if($answer->field->type === 'file' && $answer->file_path)
                                                                        <a href="{{ asset('storage/' . $answer->file_path) }}" target="_blank" class="link link-primary flex items-center gap-1 mt-1">
                                                                            <x-app-icon icon="heroicons:document-arrow-down" class="h-4 w-4" />
                                                                            Скачать файл
                                                                        </a>
                                                                    @elseif($answer->field->type === 'url' && $answer->value_text)
                                                                        <a href="{{ $answer->value_text }}" target="_blank" class="link link-primary break-all">
                                                                            {{ $answer->value_text }}
                                                                        </a>
                                                                    @else
                                                                        <p class="mt-1 whitespace-pre-wrap">{{ $answer->value_text ?? '—' }}</p>
                                                                    @endif
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @endif

                                                    <form method="POST" action="{{ route('hackatons.scores.store', $hackaton) }}" class="grid grid-cols-1 md:grid-cols-4 gap-2">
                                                        @csrf
                                                        <input type="hidden" name="hackaton_case_submission_id" value="{{ $submission->id }}">
                                                        <div class="md:col-span-2 flex items-center gap-2">
                                                            <input name="score" type="number" min="0" max="100" class="input input-bordered input-sm w-full"
                                                                placeholder="Балл"
                                                                value="{{ $submission->score?->score }}">
                                                            <span class="text-base-content/50">/</span>
                                                            <input name="max_score" type="number" min="1" max="100" class="input input-bordered input-sm w-full"
                                                                placeholder="Макс"
                                                                value="{{ $submission->score?->max_score ?? 100 }}">
                                                        </div>
                                                        <button class="btn btn-sm btn-primary md:col-span-2">Сохранить оценку</button>
                                                        <textarea name="comment" rows="1" class="textarea textarea-bordered textarea-sm md:col-span-4"
                                                            placeholder="Комментарий к оценке">{{ $submission->score?->comment }}</textarea>
                                                    </form>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                    @if($isOrganizer)
                                        <div class="divider my-1"></div>
                                        <x-organizer-action-modal
                                            :modal-id="'organizer-case-field-create-modal-'.$case->id"
                                            button-label="Добавить поле"
                                            button-class="btn btn-sm btn-outline"
                                            title="Добавить поле к кейсу"
                                            :description="'Кейс: '.$case->title">
                                            <form method="POST" action="{{ route('hackatons.cases.fields.store', [$hackaton, $case]) }}"
                                                class="grid grid-cols-1 gap-2 md:grid-cols-2">
                                                @csrf
                                                <input type="hidden" name="_open_modal" value="{{ 'organizer-case-field-create-modal-'.$case->id }}">
                                                <input name="label" class="input input-bordered" placeholder="Название поля" required autofocus>
                                                <select name="type" class="select select-bordered" required>
                                                    <option value="text">Короткий текст</option>
                                                    <option value="url">Ссылка</option>
                                                    <option value="textarea">Большой текст</option>
                                                    <option value="file">Файл</option>
                                                </select>
                                                <label class="label cursor-pointer justify-start gap-2">
                                                    <input type="checkbox" name="is_required" value="1" class="checkbox checkbox-sm">
                                                    <span class="label-text">Обязательное поле</span>
                                                </label>
                                                <button class="btn btn-sm btn-outline md:col-span-2">Добавить поле</button>
                                            </form>
                                        </x-organizer-action-modal>
                                    @endif
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        @guest
            <div class="alert">
                <span>Чтобы подать заявку на участие команды, <a class="link link-primary" href="/login">войдите в аккаунт</a>.</span>
            </div>
        @endguest
        </section>

        @if($isOrganizer || $isAssignedJudge)
            <section id="hackaton-panel-organization" role="tabpanel" class="hidden space-y-6" data-tab-panel="hackaton" data-tab-value="organization">
            <div class="divider">{{ $isOrganizer ? 'Для организатора' : 'Для судьи' }}</div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="card bg-base-100 border border-base-200 shadow-sm">
                    <div class="card-body">
                        <p class="text-sm text-base-content/70">Заявки (всего)</p>
                        <p class="text-3xl font-semibold">{{ $metrics['applications_total'] }}</p>
                        <p class="text-xs">В работе: {{ $metrics['applications_pending'] }}</p>
                    </div>
                </div>
                <div class="card bg-base-100 border border-base-200 shadow-sm">
                    <div class="card-body">
                        <p class="text-sm text-base-content/70">Решения кейсов</p>
                        <p class="text-3xl font-semibold">{{ $metrics['submissions_total'] }}</p>
                        <p class="text-xs">Оценено: {{ $metrics['submissions_scored'] }}</p>
                    </div>
                </div>
                <div class="card bg-base-100 border border-base-200 shadow-sm">
                    <div class="card-body">
                        <p class="text-sm text-base-content/70">Прогресс оценивания</p>
                        <p class="text-3xl font-semibold">{{ $metrics['submissions_scored_percent'] }}%</p>
                    </div>
                </div>
            </div>

            <div class="card bg-base-100 border border-base-200 shadow-sm">
                <div class="card-body">
                    <h2 class="card-title text-xl">Рейтинг команд</h2>
                    @if($leaderboard->isEmpty())
                        <p class="text-base-content/60">Пока нет оцененных решений команд.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Команда</th>
                                        <th>Баллы</th>
                                        <th>Макс. баллы</th>
                                        <th>Прогресс</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($leaderboard as $row)
                                        <tr>
                                            <td>{{ $row['team']->title }}</td>
                                            <td>{{ $row['total_score'] }}</td>
                                            <td>{{ $row['max_score'] }}</td>
                                            <td>{{ $row['progress_percent'] }}%</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            @if($isOrganizer)
                <div class="card bg-base-100 border border-base-200 shadow-sm">
                    <div class="card-body">
                        <h2 class="card-title text-xl">Экспорт данных</h2>
                        <p class="text-sm text-base-content/70">Скачайте команды, участников или архив подписанных документов.</p>
                        <div class="flex flex-wrap gap-2 mt-2">
                            <a href="{{ route('hackatons.export.teams', $hackaton) }}" class="btn btn-sm btn-outline">Экспорт команд (CSV)</a>
                            <a href="{{ route('hackatons.export.participants', $hackaton) }}" class="btn btn-sm btn-outline">Экспорт участников (CSV)</a>
                            <a href="{{ route('hackatons.export.documents-zip', $hackaton) }}" class="btn btn-sm btn-primary">Документы (ZIP)</a>
                        </div>
                    </div>
                </div>

                <div class="card bg-base-100 border border-base-200 shadow-sm">
                    <div class="card-body space-y-4">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <h2 class="card-title text-xl">Судьи хакатона</h2>
                            <div class="flex flex-wrap gap-2">
                                <x-organizer-action-modal
                                    :modal-id="$modals['judge_invite']"
                                    button-label="Пригласить судью"
                                    title="Приглашение судьи по email">
                                    <form method="POST" action="{{ route('hackatons.judges.invite', $hackaton) }}" class="space-y-3">
                                        @csrf
                                        <input type="hidden" name="_open_modal" value="{{ $modals['judge_invite'] }}">
                                        <input name="email" type="email" class="input input-bordered w-full" placeholder="email судьи для приглашения" required autofocus>
                                        <button class="btn btn-primary btn-sm">Отправить инвайт</button>
                                    </form>
                                </x-organizer-action-modal>

                                <x-organizer-action-modal
                                    :modal-id="$modals['judge_assign']"
                                    button-label="Назначить судью"
                                    button-class="btn btn-outline btn-sm"
                                    title="Назначение зарегистрированного судьи">
                                    <form method="POST" action="{{ route('hackatons.judges.assign', $hackaton) }}" class="space-y-3">
                                        @csrf
                                        <input type="hidden" name="_open_modal" value="{{ $modals['judge_assign'] }}">
                                        <select name="user_id" class="select select-bordered w-full" required autofocus>
                                            <option value="">Выберите зарегистрированного судью</option>
                                            @foreach($judgeCandidates as $candidate)
                                                <option value="{{ $candidate->id }}">{{ $candidate->fio }} ({{ $candidate->email }})</option>
                                            @endforeach
                                        </select>
                                        <button class="btn btn-outline btn-sm">Назначить</button>
                                    </form>
                                </x-organizer-action-modal>
                            </div>
                        </div>
                        @if($hackaton->judges->isNotEmpty())
                            <div class="overflow-x-auto">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Судья</th>
                                            <th>Email</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($hackaton->judges as $judge)
                                            <tr>
                                                <td>{{ $judge->fio }}</td>
                                                <td>{{ $judge->email }}</td>
                                                <td class="text-right">
                                                    <form method="POST" action="{{ route('hackatons.judges.unassign', [$hackaton, $judge]) }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="btn btn-xs btn-error">Снять</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                        @if($pendingJudgeInvitations->isNotEmpty())
                            <div class="space-y-1">
                                <p class="text-sm font-medium">Ожидают подтверждения</p>
                                @foreach($pendingJudgeInvitations as $invite)
                                    <p class="text-xs text-base-content/70">{{ $invite->invited_email }} - {{ route('judges.invitations.accept', $invite->token) }}</p>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <div class="card bg-base-100 border border-base-200 shadow-sm">
                    <div class="card-body space-y-4">
                        <div class="flex items-center justify-between gap-3">
                            <h2 class="card-title text-xl">Управление кейсами</h2>
                            <x-organizer-action-modal
                                :modal-id="$modals['case_create']"
                                button-label="Новый кейс"
                                title="Создание нового кейса"
                                description="Заполните параметры публикации и дедлайна.">
                                <form method="POST" action="{{ route('hackatons.cases.store', $hackaton) }}" class="grid grid-cols-1 gap-3 md:grid-cols-2">
                                    @csrf
                                    <input type="hidden" name="_open_modal" value="{{ $modals['case_create'] }}">
                                    <input name="title" class="input input-bordered" placeholder="Название кейса" required autofocus>
                                    <label class="label cursor-pointer justify-start gap-2">
                                        <input type="checkbox" name="is_published" value="1" class="checkbox checkbox-sm">
                                        <span class="label-text">Опубликовать сразу</span>
                                    </label>
                                    <input name="publish_at" type="datetime-local" class="input input-bordered" placeholder="Дата публикации">
                                    <input name="deadline_at" type="datetime-local" class="input input-bordered" placeholder="Дедлайн">
                                    <textarea name="description" rows="3" class="textarea textarea-bordered md:col-span-2"
                                        placeholder="Описание кейса"></textarea>
                                    <button class="btn btn-primary btn-sm md:col-span-2">Добавить кейс</button>
                                </form>
                            </x-organizer-action-modal>
                        </div>
                    </div>
                </div>
            @endif

            @if($isOrganizer)
            <div class="card bg-base-100 border border-base-200 shadow-sm">
                <div class="card-body space-y-4">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <h2 class="card-title text-xl">Сертификаты участников</h2>
                        @if($participantUsers->isNotEmpty())
                            <x-organizer-action-modal
                                :modal-id="$modals['certificate_upload']"
                                button-label="Загрузить сертификат"
                                title="Загрузка сертификата"
                                description="Выберите одного или нескольких участников и загрузите файл сертификата.">
                                <form method="POST" enctype="multipart/form-data" action="{{ route('hackatons.certificates.store', $hackaton) }}"
                                    class="grid grid-cols-1 gap-3 md:grid-cols-2">
                                    @csrf
                                    <input type="hidden" name="_open_modal" value="{{ $modals['certificate_upload'] }}">
                                    <select name="user_ids[]" class="select select-bordered md:col-span-2" multiple required autofocus>
                                        @foreach($participantUsers as $participant)
                                            <option value="{{ $participant->id }}">
                                                {{ $participant->fio ?? $participant->nickname ?? $participant->email }}
                                                @if($issuedCertificatesByUser->has($participant->id))
                                                    (уже выдано: {{ $issuedCertificatesByUser->get($participant->id)->count() }})
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    <input name="title" class="input input-bordered" placeholder="Название сертификата" required>
                                    <input name="issued_at" type="date" class="input input-bordered">
                                    <input name="file" type="file" class="file-input file-input-bordered md:col-span-2" required>
                                    <button class="btn btn-primary btn-sm md:col-span-2">Загрузить сертификат</button>
                                </form>
                            </x-organizer-action-modal>
                        @endif
                    </div>
                    @if($participantUsers->isEmpty())
                        <p class="text-base-content/60">Пока нет участников для выдачи сертификатов.</p>
                    @endif

                    @if($hackaton->certificates->isNotEmpty())
                        <div class="overflow-x-auto">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Участник</th>
                                        <th>Название</th>
                                        <th>Дата выдачи</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($hackaton->certificates as $certificate)
                                        <tr>
                                            <td>{{ $certificate->user->fio ?? $certificate->user->nickname ?? $certificate->user->email }}</td>
                                            <td>{{ $certificate->title }}</td>
                                            <td>{{ $certificate->issued_at?->format('d.m.Y') ?? '—' }}</td>
                                            <td class="text-right">
                                                <a href="{{ route('certificates.download', $certificate) }}" class="btn btn-xs btn-outline">Скачать</a>
                                                <form method="POST" action="{{ route('hackatons.certificates.destroy', [$hackaton, $certificate]) }}" class="inline-block"
                                                    onsubmit="return confirm('Удалить сертификат?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-xs btn-ghost text-error">Удалить</button>
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
            </section>
        @endif

        <section id="hackaton-panel-participants" role="tabpanel" class="hidden" data-tab-panel="hackaton" data-tab-value="participants">
            @if($isOrganizer)
            <div class="card bg-base-100 border border-base-200 shadow-sm">
                <div class="card-body">
                    <h2 class="card-title text-xl">Заявки команд</h2>
                    <form method="GET" class="my-3 flex items-center gap-2">
                        <select name="applications_status" class="select select-bordered select-sm">
                            <option value="">Все статусы</option>
                            <option value="pending" @selected($applicationStatusFilter === 'pending')>На рассмотрении</option>
                            <option value="accepted" @selected($applicationStatusFilter === 'accepted')>Принята</option>
                            <option value="rejected" @selected($applicationStatusFilter === 'rejected')>Отклонена</option>
                        </select>
                        <button class="btn btn-sm btn-outline">Фильтровать</button>
                    </form>

                    @if($applications->isEmpty())
                    <p class="text-base-content/60">Пока нет заявок. Когда команды подадут заявки, они появятся в этом списке.</p>
                    @else
                        <div class="mb-3 flex items-center gap-2">
                            <select form="bulk-status-update" name="status" class="select select-bordered select-sm">
                                <option value="accepted">Принять</option>
                                <option value="rejected">Отклонить</option>
                            </select>
                            <button form="bulk-status-update" class="btn btn-sm btn-primary">Применить к выбранным</button>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>Команда</th>
                                        <th>Сообщение</th>
                                        <th>Отправлена</th>
                                        <th>Статус</th>
                                        <th>Рассмотрел</th>
                                        <th>Рассмотрена</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($applications as $app)
                                        <tr>
                                            <td>
                                                @if($app->status->isPending())
                                                    <input form="bulk-status-update" type="checkbox" name="application_ids[]" value="{{ $app->id }}" class="checkbox checkbox-sm">
                                                @endif
                                            </td>
                                            <td>{{ $app->team->title }}</td>
                                            <td class="max-w-xs truncate">{{ $app->message }}</td>
                                            <td>{{ $app->created_at?->format('d.m.Y H:i') ?? '—' }}</td>
                                            <td>
                                                <span class="badge badge-{{ $app->status->isAccepted() ? 'success' : ($app->status->isRejected() ? 'error' : 'warning') }}">
                                                    {{ $app->status->label() }}
                                                </span>
                                            </td>
                                            <td>{{ $app->reviewer?->nickname ?? $app->reviewer?->name ?? '—' }}</td>
                                            <td>{{ $app->reviewed_at?->format('d.m.Y H:i') ?? '—' }}</td>
                                            <td>
                                                @if($app->status->isPending())
                                                    <form method="POST" action="{{ route('hackaton.applications.update', $app) }}" class="inline-flex">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="status" value="accepted">
                                                        <button class="btn btn-success btn-xs">Принять</button>
                                                    </form>

                                                    <form method="POST" action="{{ route('hackaton.applications.update', $app) }}" class="inline-flex ml-2"
                                                        onsubmit="return confirm('Отклонить заявку команды?');">
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
                        <form id="bulk-status-update" method="POST" action="{{ route('hackaton.applications.bulk-update', $hackaton) }}" class="hidden">
                            @csrf
                            @method('PATCH')
                        </form>
                    @endif
                </div>
            </div>
            @else
                <div class="card bg-base-100 border border-base-200 shadow-sm">
                    <div class="card-body">
                        <h2 class="card-title text-xl">Участники хакатона</h2>
                        <p class="text-base-content/70">Статистика участников доступна в разделе «Описание». Для заявок вашей команды используйте карточку «Информация о хакатоне».</p>
                    </div>
                </div>
            @endif
        </section>
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
                        trigger.tabIndex = isActive ? 0 : -1;
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
                    trigger.addEventListener('keydown', (event) => {
                        if (!['ArrowRight', 'ArrowLeft', 'Home', 'End'].includes(event.key)) {
                            return;
                        }
                        event.preventDefault();
                        const index = triggers.indexOf(trigger);
                        if (index === -1) {
                            return;
                        }
                        let nextIndex = index;
                        if (event.key === 'ArrowRight') {
                            nextIndex = (index + 1) % triggers.length;
                        } else if (event.key === 'ArrowLeft') {
                            nextIndex = (index - 1 + triggers.length) % triggers.length;
                        } else if (event.key === 'Home') {
                            nextIndex = 0;
                        } else if (event.key === 'End') {
                            nextIndex = triggers.length - 1;
                        }
                        const nextTrigger = triggers[nextIndex];
                        setActiveTab(nextTrigger.dataset.tabValue);
                        nextTrigger.focus();
                    });
                });

                setActiveTab(activeTab, true);
            };

            setupTabGroup('hackaton', 'description');

            const carousels = document.querySelectorAll('[data-image-carousel]');

            carousels.forEach((carousel) => {
                const slides = Array.from(carousel.querySelectorAll('[data-carousel-slide]'));
                const prevButton = carousel.querySelector('[data-carousel-prev]');
                const nextButton = carousel.querySelector('[data-carousel-next]');
                const dots = Array.from(carousel.querySelectorAll('[data-carousel-dot]'));

                if (slides.length <= 1) {
                    return;
                }

                let currentIndex = 0;

                const render = (nextIndex) => {
                    const normalizedIndex = (nextIndex + slides.length) % slides.length;
                    currentIndex = normalizedIndex;

                    slides.forEach((slide, slideIndex) => {
                        slide.classList.toggle('hidden', slideIndex !== currentIndex);
                    });

                    dots.forEach((dot, dotIndex) => {
                        dot.classList.toggle('bg-base-100', dotIndex === currentIndex);
                        dot.classList.toggle('bg-base-100/40', dotIndex !== currentIndex);
                    });
                };

                prevButton?.addEventListener('click', () => render(currentIndex - 1));
                nextButton?.addEventListener('click', () => render(currentIndex + 1));

                dots.forEach((dot, dotIndex) => {
                    dot.addEventListener('click', () => render(dotIndex));
                });
            });
        })();
    </script>

    @if ($errors->any() && filled(old('_open_modal')))
        <script>
            (function () {
                const modalId = @json(old('_open_modal'));
                const modalToggle = document.getElementById(modalId);

                if (!modalToggle) {
                    return;
                }

                modalToggle.checked = true;

                window.requestAnimationFrame(() => {
                    const modal = modalToggle.closest('.inline-block');
                    const firstField = modal?.querySelector('input:not([type="hidden"]), textarea, select');
                    firstField?.focus();
                });
            })();
        </script>
    @endif