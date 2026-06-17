
<div class="mx-auto w-full max-w-6xl space-y-4">
    <x-marytoast/>
    @php
        $wizardLabels = [
            1 => 'Основная информация и даты',
            2 => 'Призовой фонд и уровень',
            3 => 'Видимость и кейсы',
            4 => 'Документы',
            5 => 'Обзор и запуск',
        ];
    @endphp

    <div class="text-sm breadcrumbs">
        <ul>
            <li><a href="/">Главная</a></li>
            <li><a href="{{ route('organizer.dashboard') }}">Мои хакатоны</a></li>
            <li class="opacity-70">Создание хакатона</li>
        </ul>
    </div>

    <x-marycard class="card card-border bg-base-100">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold">Создание хакатона</h1>
                <p class="text-sm text-base-content/70">
                    {{ $wizardLabels[$wizardStep] ?? '' }}
                </p>
            </div>
            <x-marybadge class="badge-primary" value="Шаг {{ $wizardStep }} из {{ \App\Livewire\Pages\Hackatons\Create::WIZARD_LAST_STEP }}" />
        </div>
    </x-marycard>

    <x-marycard class="card card-border bg-base-100">
        <div class="space-y-2">
            <div class="flex items-center justify-between gap-2">
                <p class="text-sm font-medium">Прогресс мастера</p>
                <span class="text-sm text-base-content/70">{{ $wizardStep }}/{{ \App\Livewire\Pages\Hackatons\Create::WIZARD_LAST_STEP }}</span>
            </div>
            <progress class="progress progress-primary w-full" value="{{ (int) round(($wizardStep / max(\App\Livewire\Pages\Hackatons\Create::WIZARD_LAST_STEP, 1)) * 100) }}" max="100"></progress>
        </div>
    </x-marycard>

    <x-marycard class="w-full justify-self-center card card-border bg-base-100">
        <x-maryform wire:submit="wizardSubmit" class="space-y-6">
            @if ($wizardStep === 1)
                <div class="card border border-base-300 bg-base-100/50 p-4 sm:p-5 space-y-4">
                    <h2 class="text-lg font-semibold">Шаг 1 — Основная информация</h2>

                    @if($templates->isNotEmpty())
                        <div class="space-y-2">
                            <p class="text-sm font-medium">Создать из шаблона</p>
                            <div class="flex flex-wrap gap-2">
                                @foreach($templates as $template)
                                    <button
                                        type="button"
                                        wire:click="applyTemplate({{ $template->id }})"
                                        class="btn btn-sm {{ $selectedTemplateId === $template->id ? 'btn-primary' : 'btn-outline' }}"
                                    >
                                        {{ $template->title }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <x-mary-input label="Название хакатона" wire:model="title" placeholder="Например, HackFest 2026"/>
                    <x-marymarkdown wire:model="description" :config="$this->config" label="Описание хакатона" />
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <x-marydatetime label="Дата начала" wire:model="start_at"/>
                        <x-marydatetime label="Дата конца" wire:model="end_at"/>
                    </div>
                    <x-marydatetime
                        label="Дедлайн регистрации"
                        wire:model="registration_deadline_at"
                        hint="Когда закрывается прием заявок (не позже даты начала)"
                    />
                </div>
            @endif

            @if ($wizardStep === 2)
                <div class="card border border-base-300 bg-base-100/50 p-4 sm:p-5 space-y-4">
                    <h2 class="text-lg font-semibold">Шаг 2 — Призовой фонд и уровень</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <x-mary-input
                            label="Призовой фонд (₽)"
                            type="number"
                            min="0"
                            step="0.01"
                            placeholder="Например, 500000"
                            wire:model="prize_fund"
                        />
                        <x-mary-input
                            label="Количество призовых мест"
                            type="number"
                            min="1"
                            placeholder="Например, 3"
                            wire:model="prize_places_count"
                        />
                    </div>
                    <x-maryselect
                        label="Уровень хакатона"
                        wire:model="level"
                        :options="$this->levelOptions()"
                    />
                </div>
            @endif

            @if ($wizardStep === 3)
                <div class="card border border-base-300 bg-base-100/50 p-4 sm:p-5 space-y-4">
                    <h2 class="text-lg font-semibold">Шаг 3 — Видимость и кейсы</h2>
                    <label class="label cursor-pointer justify-start gap-3 rounded-xl border border-base-300 bg-base-200/40 p-4">
                        <input type="checkbox" class="checkbox checkbox-primary" wire:model.live="is_public" />
                        <span>
                            <span class="label-text font-semibold">Публичный хакатон</span>
                            <span class="block text-xs font-normal text-base-content/70">Если выключено, хакатон сохранится как черновик до публикации.</span>
                        </span>
                    </label>
                    <div class="rounded-xl border border-info/25 bg-info/10 p-4 text-sm text-base-content/80">
                        <p class="font-medium text-info">Структура кейсов</p>
                        <p class="mt-1">После создания откройте страницу хакатона → вкладка «Кейсы» / «Организация»: добавьте кейсы и поля решения (в т.ч. шаблоны полей по мере развития продукта).</p>
                    </div>
                </div>
            @endif

            @if ($wizardStep === 4)
                <div class="card border border-base-300 bg-base-100/50 p-4 sm:p-5 space-y-4">
                    <h2 class="text-lg font-semibold">Шаг 4 — Обложка и документы</h2>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <h3 class="text-base font-semibold">Обложка и галерея</h3>
                            <x-maryfile
                                label="Обложка хакатона"
                                wire:model.live="photo"
                                accept="image/png, image/jpeg, image/webp"
                                hint="PNG/JPEG/WebP, до 4 МБ. Дождитесь превью ниже после выбора файла, затем нажмите «Далее»."
                            />
                            @if ($photo)
                                <div class="rounded-xl border border-base-300 bg-base-200 p-2">
                                    <img class="h-64 w-full rounded-lg object-contain" src="{{ $photo->temporaryUrl() }}" alt="Превью обложки хакатона">
                                </div>
                            @endif
                            <div class="space-y-2">
                                <label class="label p-0">
                                    <span class="label-text">Фотографии хакатона (галерея)</span>
                                </label>
                                <input type="file" wire:model="galleryPhotos" multiple accept="image/*" class="file-input file-input-bordered w-full" />
                                <p class="text-xs text-base-content/70">Можно загрузить несколько фото для слайдера на странице хакатона.</p>
                            </div>
                            @if (!empty($galleryPhotos))
                                <div class="grid grid-cols-2 gap-2 sm:grid-cols-3">
                                    @foreach ($galleryPhotos as $galleryPhoto)
                                        <img src="{{ $galleryPhoto->temporaryUrl() }}" alt="Превью фотографии хакатона"
                                             class="h-24 w-full rounded-lg border border-base-300 object-cover">
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        <div class="space-y-4">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <div>
                                    <h3 class="text-base font-semibold">Документы хакатона</h3>
                                    <p class="text-sm text-base-content/70">Регламенты и документы для участников.</p>
                                </div>
                                <x-marybutton type="button" class="btn-primary btn-sm" wire:click="addHackatonDocument">
                                    Добавить документ
                                </x-marybutton>
                            </div>
                            @if (empty($hackatonDocuments))
                                <div class="rounded-xl border border-dashed border-base-300 p-4 text-sm text-base-content/70">
                                    Документы необязательны на этом шаге, но для публикации обычно нужен хотя бы регламент.
                                </div>
                            @endif
                            <div class="space-y-3">
                                @foreach($hackatonDocuments as $index => $hackatonDocument)
                                    @php
                                        $documentWireKey = $hackatonDocument['id'] ?? 'index-' . $index;
                                    @endphp
                                    <x-marycard class="bg-base-200" wire:key="hackatonDocument-{{ $documentWireKey }}">
                                        <div class="flex flex-wrap items-center justify-between gap-2">
                                            <x-marybadge class="badge-neutral" value="Документ #{{ $index + 1 }}" />
                                            <x-marybutton type="button" wire:click="removeHackatonDocument({{ $index }})"
                                                          class="btn-error btn-sm">
                                                Удалить
                                            </x-marybutton>
                                        </div>
                                        <div class="mt-3 space-y-3">
                                            <x-mary-input wire:model="hackatonDocuments.{{$index}}.name" label="Название документа"/>
                                            <x-marymarkdown label="Описание документа"
                                                            wire:model="hackatonDocuments.{{$index}}.description"
                                                            :config="$this->config"/>
                                            <x-maryfile wire:model="hackatonDocuments.{{$index}}.file_url" label="Файл документа" />
                                            <x-maryradio label="Тип документа"
                                                         wire:model="hackatonDocuments.{{$index}}.filling_by_team_member"
                                                         :options="$documentTypes" inline/>
                                        </div>
                                    </x-marycard>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if ($wizardStep === 5)
                <div class="card border border-base-300 bg-base-100/50 p-4 sm:p-5 space-y-4">
                    <h2 class="text-lg font-semibold">Шаг 5 — Обзор</h2>
                    <dl class="grid grid-cols-1 gap-3 text-sm sm:grid-cols-2">
                        <div><dt class="text-xs uppercase text-base-content/50">Название</dt><dd class="font-medium">{{ $title }}</dd></div>
                        <div><dt class="text-xs uppercase text-base-content/50">Публичный</dt><dd class="font-medium">{{ $is_public ? 'Да' : 'Нет (черновик)' }}</dd></div>
                        <div><dt class="text-xs uppercase text-base-content/50">Старт</dt><dd class="tabular-nums">{{ $start_at }}</dd></div>
                        <div><dt class="text-xs uppercase text-base-content/50">Финиш</dt><dd class="tabular-nums">{{ $end_at }}</dd></div>
                        <div><dt class="text-xs uppercase text-base-content/50">Документов</dt><dd>{{ count($hackatonDocuments) }}</dd></div>
                        <div><dt class="text-xs uppercase text-base-content/50">Фото в галерее</dt><dd>{{ count($galleryPhotos) }}</dd></div>
                    </dl>
                    <p class="text-xs text-base-content/60">Нажмите «Создать хакатон», чтобы сохранить событие и перейти в дашборд организатора.</p>
                </div>
            @endif

            <x-slot:actions>
                <a href="{{ route('organizer.dashboard') }}">
                    <x-marybutton type="button" label="Отмена" class="btn-ghost" />
                </a>
                @if($wizardStep > 1)
                    <x-marybutton type="button" label="Назад" class="btn-outline" wire:click="previousStep" />
                @endif
                @if($wizardStep < \App\Livewire\Pages\Hackatons\Create::WIZARD_LAST_STEP)
                    <x-marybutton type="submit" label="Далее" class="btn-primary" wire:loading.attr="disabled" />
                @else
                    <x-marybutton type="submit" label="Создать хакатон" spinner="wizardSubmit"
                        wire:loading.attr="disabled" class="btn-primary" />
                @endif
            </x-slot:actions>
        </x-maryform>
    </x-marycard>
</div>
