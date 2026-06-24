
<div class="mx-auto w-full max-w-6xl space-y-4">
    @php
        $wizardLabels = [
            1 => 'Основная информация и даты',
            2 => 'Призовой фонд и уровень',
            3 => 'Видимость и кейсы',
            4 => 'Документы',
            5 => 'Обзор и запуск',
        ];
    @endphp

    <nav class="text-sm breadcrumbs" aria-label="{{ __('ui.breadcrumbs.aria_label') }}">
        <ul>
            <li><a href="/">{{ __('ui.nav.home') }}</a></li>
            <li><a href="{{ route('organizer.dashboard') }}">{{ __('ui.dashboard.organizer.my_hackatons') }}</a></li>
            <li class="opacity-70">{{ __('ui.dashboard.organizer.create_hackaton') }}</li>
        </ul>
    </nav>

    <x-marycard class="card border border-base-300 bg-base-100">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold">Создание хакатона</h1>
                <p class="text-sm text-base-content/70">
                    {{ $wizardLabels[$wizardStep] ?? '' }}
                </p>
            </div>
            <x-marybadge class="badge-neutral" value="Шаг {{ $wizardStep }} из {{ \App\Livewire\Pages\Hackatons\Create::WIZARD_LAST_STEP }}" />
        </div>
    </x-marycard>

    <x-marycard class="card border border-base-300 bg-base-100">
        <div class="space-y-2" role="region" aria-live="polite" aria-label="Form progress">
            <div class="flex items-center justify-between gap-2">
                <p class="text-sm font-medium">{{ __('ui.auth.register.progress_label') }}</p>
                <span class="text-sm text-base-content/70" aria-current="step">{{ $wizardStep }}/{{ \App\Livewire\Pages\Hackatons\Create::WIZARD_LAST_STEP }}</span>
            </div>
            <progress class="progress w-full transition-all duration-300" value="{{ (int) round(($wizardStep / max(\App\Livewire\Pages\Hackatons\Create::WIZARD_LAST_STEP, 1)) * 100) }}" max="100" aria-label="Progress: {{ $wizardStep }} of {{ \App\Livewire\Pages\Hackatons\Create::WIZARD_LAST_STEP }}"></progress>
        </div>
    </x-marycard>

    <x-marycard class="w-full justify-self-center card border border-base-300 bg-base-100">
        <x-maryform wire:submit="wizardSubmit" class="space-y-6" aria-label="Hackathon creation wizard">
            @if ($wizardStep === 1)
                <div class="animate-form-slide-in card border border-base-300 bg-base-100/50 p-4 sm:p-5 space-y-4">
                    <h2 class="text-lg font-semibold">{{ __('ui.auth.register.step_personal') }}</h2>

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

                    <div class="space-y-2">
                        <div class="flex items-center gap-2">
                            <label class="label p-0">Название хакатона</label>
                            <div class="tooltip tooltip-right cursor-help" data-tip="Название, которое увидят все участники. Должно быть ясным и привлекательным.">
                                <x-mary-icon name="o-question-mark-circle" class="h-4 w-4 text-base-content/50 hover:text-base-content/70" />
                            </div>
                        </div>
                        <x-mary-input
                            wire:model="title"
                            placeholder="Например, HackFest 2026"
                            :error="$errors->has('title')"
                            aria-invalid="{{ $errors->has('title') ? 'true' : 'false' }}"
                            aria-describedby="{{ $errors->has('title') ? 'title-error' : '' }}"
                        />
                        @error('title')
                            <span id="title-error" class="text-xs text-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <div class="flex items-center gap-2">
                            <label class="label p-0">Описание хакатона</label>
                            <div class="tooltip tooltip-right cursor-help" data-tip="Подробное описание с целями, правилами и ожиданиями. Поддерживает Markdown.">
                                <x-mary-icon name="o-question-mark-circle" class="h-4 w-4 text-base-content/50 hover:text-base-content/70" />
                            </div>
                        </div>
                        <x-marymarkdown wire:model="description" :config="$this->config" />
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <x-marydatetime label="Дата начала" wire:model.live="start_at" wire:blur="validateStartDate"/>
                        <x-marydatetime label="Дата конца" wire:model.live="end_at" wire:blur="validateEndDate"/>
                    </div>
                    <div class="space-y-2">
                        <div class="flex items-center gap-2">
                            <label class="label p-0">Дедлайн регистрации</label>
                            <div class="tooltip tooltip-right cursor-help" data-tip="Дата, до которой пользователи смогут зарегистрироваться. Должна быть не позже даты начала хакатона.">
                                <x-mary-icon name="o-question-mark-circle" class="h-4 w-4 text-base-content/50 hover:text-base-content/70" />
                            </div>
                        </div>
                        <x-marydatetime
                            wire:model.live="registration_deadline_at"
                            wire:blur="validateRegistrationDeadline"
                            hint="Не позже даты начала"
                        />
                    </div>
                </div>
            @endif

            @if ($wizardStep === 2)
                <div class="animate-form-slide-in card border border-base-300 bg-base-100/50 p-4 sm:p-5 space-y-4">
                    <h2 class="text-lg font-semibold">Шаг 2 — Призовой фонд и уровень</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="space-y-2">
                            <div class="flex items-center gap-2">
                                <label class="label p-0">Призовой фонд (₽)</label>
                                <div class="tooltip tooltip-right cursor-help" data-tip="Общая сумма денежного вознаграждения для победителей. Оставьте пустым, если призов нет.">
                                    <x-mary-icon name="o-question-mark-circle" class="h-4 w-4 text-base-content/50 hover:text-base-content/70" />
                                </div>
                            </div>
                            <x-mary-input
                                type="number"
                                min="0"
                                step="0.01"
                                placeholder="Например, 500000"
                                wire:model.live="prize_fund"
                                wire:blur="validatePrizeFund"
                            />
                        </div>
                        <div class="space-y-2">
                            <div class="flex items-center gap-2">
                                <label class="label p-0">Количество призовых мест</label>
                                <div class="tooltip tooltip-right cursor-help" data-tip="Сколько команд получат призы (от 1 до 1000). Обычно: 3, 5 или 10.">
                                    <x-mary-icon name="o-question-mark-circle" class="h-4 w-4 text-base-content/50 hover:text-base-content/70" />
                                </div>
                            </div>
                            <x-mary-input
                                type="number"
                                min="1"
                                placeholder="Например, 3"
                                wire:model.live="prize_places_count"
                                wire:blur="validatePrizePlaces"
                            />
                        </div>
                    </div>
                    <div class="space-y-2">
                        <div class="flex items-center gap-2">
                            <label class="label p-0">Уровень хакатона</label>
                            <div class="tooltip tooltip-right cursor-help" data-tip="Уровень сложности помогает участникам выбрать подходящие мероприятия.">
                                <x-mary-icon name="o-question-mark-circle" class="h-4 w-4 text-base-content/50 hover:text-base-content/70" />
                            </div>
                        </div>
                        <x-maryselect
                            wire:model="level"
                            :options="$this->levelOptions()"
                        />
                    </div>
                </div>
            @endif

            @if ($wizardStep === 3)
                <div class="animate-form-slide-in card border border-base-300 bg-base-100/50 p-4 sm:p-5 space-y-4">
                    <h2 class="text-lg font-semibold">Шаг 3 — Видимость и кейсы</h2>
                    <label class="label cursor-pointer justify-start gap-3 rounded-xl border border-base-300 bg-base-200/40 p-4">
                        <input type="checkbox" class="checkbox" wire:model.live="is_public" />
                        <span>
                            <span class="label-text font-semibold">Публичный хакатон</span>
                            <span class="block text-xs font-normal text-base-content/70">Если выключено, хакатон сохранится как черновик до публикации.</span>
                        </span>
                    </label>
                    <div class="rounded-xl border border-base-300 bg-base-100 p-4 text-sm text-base-content/80">
                        <p class="font-medium text-base-content">Структура кейсов</p>
                        <p class="mt-1">После создания откройте страницу хакатона → вкладка «Кейсы» / «Организация»: добавьте кейсы и поля решения (в т.ч. шаблоны полей по мере развития продукта).</p>
                    </div>
                </div>
            @endif

            @if ($wizardStep === 4)
                <div class="animate-form-slide-in card border border-base-300 bg-base-100/50 p-4 sm:p-5 space-y-4">
                    <h2 class="text-lg font-semibold">Шаг 4 — Обложка и документы</h2>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div class="flex items-start justify-between">
                                <h3 class="text-base font-semibold">Обложка и галерея</h3>
                                <div class="tooltip tooltip-left cursor-help" data-tip="Привлекательные изображения помогают выделить ваш хакатон в каталоге событий.">
                                    <x-mary-icon name="o-question-mark-circle" class="h-4 w-4 text-base-content/50 hover:text-base-content/70" />
                                </div>
                            </div>
                            <div class="space-y-2">
                                <label class="label p-0">Обложка хакатона</label>
                                <x-maryfile
                                    wire:model.live="photo"
                                    accept="image/png, image/jpeg, image/webp"
                                    hint="PNG/JPEG/WebP, до 4 МБ. Будет видна в каталоге и на странице хакатона."
                                />
                            </div>
                            @if ($photo)
                                <div class="rounded-xl border border-base-300 bg-base-200 p-2">
                                    <img class="h-64 w-full rounded-lg object-contain" src="{{ $photo->temporaryUrl() }}" alt="Превью обложки хакатона">
                                </div>
                            @endif
                            <div class="space-y-2">
                                <div class="flex items-center gap-2">
                                    <label class="label p-0">Фотографии хакатона (галерея)</label>
                                    <div class="tooltip tooltip-right cursor-help" data-tip="Загрузите до 10 фотографий событий, участников или результатов для галереи.">
                                        <x-mary-icon name="o-question-mark-circle" class="h-4 w-4 text-base-content/50 hover:text-base-content/70" />
                                    </div>
                                </div>
                                <input type="file" wire:model="galleryPhotos" multiple accept="image/*" class="file-input file-input-bordered w-full" />
                                <p class="text-xs text-base-content/70">Можно загрузить несколько фото для слайдера на странице хакатона. Максимум 5 МБ за файл.</p>
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
                                <x-marybutton type="button" class="btn-primary btn-sm gap-2" wire:click="addHackatonDocument">
                                    <x-mary-icon name="o-plus" class="h-4 w-4" />
                                    Добавить документ
                                </x-marybutton>
                            </div>
                            @if (empty($hackatonDocuments))
                                <div class="rounded-xl border border-dashed border-base-300 bg-base-100/50 p-6 text-center">
                                    <p class="text-sm font-medium text-base-content/80">Документы необязательны на этом шаге</p>
                                    <p class="text-xs text-base-content/70 mt-1">Для публикации обычно нужен хотя бы регламент.</p>
                                </div>
                            @else
                                <div class="space-y-3 border-t border-base-300 pt-4">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-base-content/50">{{ count($hackatonDocuments) }} документов</p>
                                    @foreach($hackatonDocuments as $index => $hackatonDocument)
                                        @php
                                            $documentWireKey = $hackatonDocument['id'] ?? 'index-' . $index;
                                            $isFilled = !empty($hackatonDocument['name']) && !empty($hackatonDocument['description']) && !empty($hackatonDocument['file_url']);
                                        @endphp
                                        <div class="space-y-3 p-4 bg-base-200/30 rounded-lg border border-base-300" wire:key="hackatonDocument-{{ $documentWireKey }}">
                                            <div class="flex flex-wrap items-center justify-between gap-2">
                                                <x-marybadge
                                                    class="badge-neutral"
                                                    value="{{ $hackatonDocument['name'] ?: 'Документ #' . ($index + 1) }}"
                                                />
                                                @if (!$isFilled)
                                                    <span class="text-xs text-warning/70 font-medium">⚠ Заполнено частично</span>
                                                @else
                                                    <span class="text-xs text-success/70 font-medium">✓ Заполнено</span>
                                                @endif
                                                <x-marybutton type="button" wire:click="removeHackatonDocument({{ $index }})"
                                                              class="btn-error btn-sm btn-ghost" icon="o-trash">
                                                </x-marybutton>
                                            </div>
                                            <div class="space-y-3">
                                                <x-mary-input wire:model="hackatonDocuments.{{$index}}.name" label="Название документа"/>
                                                <x-marymarkdown label="Описание документа"
                                                                wire:model="hackatonDocuments.{{$index}}.description"
                                                                :config="$this->config"/>
                                                <x-maryfile wire:model="hackatonDocuments.{{$index}}.file_url" label="Файл документа" />
                                                <div class="space-y-2">
                                                    <div class="flex items-center gap-2">
                                                        <label class="label p-0">Тип документа</label>
                                                        <div class="tooltip tooltip-right cursor-help" data-tip="Информационный: только для чтения. Заполняемый: участники смогут заполнить этот документ.">
                                                            <x-mary-icon name="o-question-mark-circle" class="h-4 w-4 text-base-content/50 hover:text-base-content/70" />
                                                        </div>
                                                    </div>
                                                    <x-maryradio
                                                        wire:model="hackatonDocuments.{{$index}}.filling_by_team_member"
                                                        :options="$documentTypes" inline/>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            @if ($wizardStep === 5)
                <div class="animate-form-slide-in card border border-base-300 bg-base-100/50 p-4 sm:p-5 space-y-4">
                    <h2 class="text-lg font-semibold">Шаг 5 — Обзор</h2>
                    <dl class="grid grid-cols-1 gap-3 text-sm sm:grid-cols-2">
                        <div><dt class="text-xs uppercase text-base-content/50">Название</dt><dd class="font-medium">{{ $title }}</dd></div>
                        <div><dt class="text-xs uppercase text-base-content/50">Публичный</dt><dd class="font-medium">{{ $is_public ? 'Да' : 'Нет (черновик)' }}</dd></div>
                        <div><dt class="text-xs uppercase text-base-content/50">Старт</dt><dd class="tabular-nums">{{ $start_at }}</dd></div>
                        <div><dt class="text-xs uppercase text-base-content/50">Финиш</dt><dd class="tabular-nums">{{ $end_at }}</dd></div>
                        <div><dt class="text-xs uppercase text-base-content/50">Документов</dt><dd>{{ count($hackatonDocuments) }}</dd></div>
                        <div><dt class="text-xs uppercase text-base-content/50">Фото в галерее</dt><dd>{{ count($galleryPhotos) }}</dd></div>
                    </dl>
                    <p class="text-xs text-base-content/70">Нажмите «Создать хакатон», чтобы сохранить событие и перейти в дашборд организатора.</p>
                </div>
            @endif

            <x-slot:actions>
                <a href="{{ route('organizer.dashboard') }}">
                    <x-marybutton type="button" label="{{ __('ui.auth.register.btn_back') }}" class="btn-ghost transition-all duration-200 hover:scale-105 active:scale-95" />
                </a>
                @if($wizardStep > 1)
                    <x-marybutton type="button" label="{{ __('ui.auth.register.btn_back') }}" class="btn-outline transition-all duration-200 hover:scale-105 active:scale-95" wire:click="previousStep" />
                @endif
                @if($wizardStep < \App\Livewire\Pages\Hackatons\Create::WIZARD_LAST_STEP)
                    <x-marybutton type="submit" label="{{ __('ui.auth.register.btn_next') }}" class="btn-primary transition-all duration-200 hover:scale-105 active:scale-95" wire:loading.attr="disabled" spinner="wizardSubmit" />
                @else
                    <x-marybutton type="submit" label="{{ __('ui.dashboard.organizer.create_hackaton') }}" spinner="wizardSubmit"
                        wire:loading.attr="disabled" class="btn-primary transition-all duration-200 hover:scale-105 active:scale-95" />
                @endif
            </x-slot:actions>
        </x-maryform>
    </x-marycard>
</div>
