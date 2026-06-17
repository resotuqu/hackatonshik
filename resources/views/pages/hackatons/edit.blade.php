
<div class="mx-auto w-full max-w-6xl space-y-4">
    @php
        $hasFilledDocument = collect($hackatonDocuments)->contains(function ($document) {
            $hasDocumentType = array_key_exists('filling_by_team_member', $document)
                && $document['filling_by_team_member'] !== ''
                && $document['filling_by_team_member'] !== null;
            $hasFile = !empty($document['file_url']) || filled($document['existing_file_url'] ?? null);

            return filled($document['name'] ?? null)
                && filled($document['description'] ?? null)
                && $hasFile
                && $hasDocumentType;
        });
        $hasPhoto = !empty($photo) || filled($hackaton->image_url ?? null);

        $progressSteps = [
            filled($title),
            filled($description),
            $hasPhoto,
            filled($start_at),
            filled($end_at),
            $hasFilledDocument,
        ];
        $completedSteps = collect($progressSteps)->filter()->count();
        $totalSteps = count($progressSteps);
        $progressPercent = (int) round(($completedSteps / max($totalSteps, 1)) * 100);
    @endphp

    <div class="text-sm breadcrumbs">
        <ul>
            <li><a href="/">Главная</a></li>
            <li><a href="{{ route('organizer.dashboard') }}">Мои хакатоны</a></li>
            <li class="opacity-70">Редактирование хакатона</li>
        </ul>
    </div>

    <x-marycard class="card card-border bg-base-100">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold">Редактирование хакатона</h1>
                <p class="text-sm text-base-content/70">
                    Обновите описание, даты, обложку и список документов.
                </p>
            </div>
            <x-marybadge class="badge-neutral" value="{{ $hackaton->title }}" />
        </div>
    </x-marycard>

    <x-marycard class="card card-border bg-base-100">
        <div class="space-y-2">
            <div class="flex items-center justify-between gap-2">
                <p class="text-sm font-medium">Прогресс заполнения</p>
                <span class="text-sm text-base-content/70">{{ $completedSteps }}/{{ $totalSteps }}</span>
            </div>
            <progress class="progress progress-primary w-full" value="{{ $progressPercent }}" max="100"></progress>
            <p class="text-xs text-base-content/70">{{ $progressPercent }}% заполнено</p>
        </div>
    </x-marycard>

    <x-marycard class="w-full justify-self-center card card-border bg-base-100">
        <x-maryform wire:submit="save" class="space-y-6">
            <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                <div class="card border border-base-300 bg-base-100/50 p-4 sm:p-5 space-y-4">
                    <h2 class="text-lg font-semibold">Основная информация</h2>
                    <x-mary-input label="Название хакатона" wire:model="title" />
                    <x-marymarkdown wire:model="description" :config="$this->config" label="Описание хакатона" />
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <x-marydatetime label="Дата начала" wire:model="start_at" />
                        <x-marydatetime label="Дата конца" wire:model="end_at" />
                    </div>
                    <x-marydatetime
                        label="Дедлайн регистрации"
                        wire:model="registration_deadline_at"
                        hint="Когда закрывается прием заявок (не позже даты начала)"
                    />

                    <div class="divider my-1 text-xs text-base-content/50">Метрики хакатона</div>

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

                <div class="card border border-base-300 bg-base-100/50 p-4 sm:p-5 space-y-4">
                    <h2 class="text-lg font-semibold">Обложка</h2>
                    <x-maryfile label="Обложка хакатона" hint="Загрузите файл только если хотите заменить" wire:model="photo" />
                    @if ($photo)
                        <div class="rounded-xl border border-base-300 bg-base-200 p-2">
                            <img class="w-full object-contain h-64 rounded-lg" src="{{ $photo->temporaryUrl() }}" alt="Превью обложки хакатона">
                        </div>
                    @elseif(!empty($hackaton->image_url))
                        <div class="rounded-xl border border-base-300 bg-base-200 p-2">
                            <img class="w-full object-contain h-64 rounded-lg" src="{{ asset('storage/' . $hackaton->image_url) }}" alt="Текущая обложка хакатона">
                        </div>
                    @endif
                    <div class="space-y-2">
                        <label class="label p-0">
                            <span class="label-text">Добавить фото в галерею</span>
                        </label>
                        <input type="file" wire:model="galleryPhotos" multiple accept="image/*" class="file-input file-input-bordered w-full" />
                        <p class="text-xs text-base-content/70">Новые фото добавятся в конец галереи.</p>
                    </div>
                    @if (!empty($galleryPhotos))
                        <div class="grid grid-cols-2 gap-2 sm:grid-cols-3">
                            @foreach ($galleryPhotos as $galleryPhoto)
                                <img src="{{ $galleryPhoto->temporaryUrl() }}" alt="Превью новой фотографии хакатона"
                                     class="h-24 w-full rounded-lg object-cover border border-base-300">
                            @endforeach
                        </div>
                    @endif
                    @php
                        $activeImages = $hackaton->images->reject(fn ($image) => in_array($image->id, $imagesToDelete, true));
                    @endphp
                    @if ($activeImages->isNotEmpty())
                        <div class="space-y-2">
                            <p class="text-sm font-medium">Текущая галерея</p>
                            <div class="grid grid-cols-2 gap-2 sm:grid-cols-3">
                                @foreach ($activeImages as $galleryImage)
                                    <div class="relative">
                                        <img src="{{ asset('storage/' . $galleryImage->path) }}" alt="Фото хакатона"
                                             class="h-24 w-full rounded-lg object-cover border border-base-300">
                                        <button type="button"
                                                wire:click="markImageForDelete({{ $galleryImage->id }})"
                                                class="btn btn-xs btn-error absolute right-1 top-1">
                                            Удалить
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card border border-base-300 bg-base-100/50 p-4 sm:p-5 space-y-4">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-semibold">Документы хакатона</h2>
                        <p class="text-sm text-base-content/70">Редактируйте существующие документы и добавляйте новые.</p>
                    </div>
                    <x-marybutton type="button" class="btn-primary btn-sm" wire:click="addHackatonDocument">
                        Добавить документ
                    </x-marybutton>
                </div>

                @if (empty($hackatonDocuments))
                    <div class="rounded-xl border border-dashed border-base-300 p-4 text-sm text-base-content/70">
                        Пока нет документов.
                    </div>
                @endif

                <div class="space-y-3">
                    @foreach($hackatonDocuments as $index => $hackatonDocument)
                        <x-marycard class="bg-base-200" wire:key="hackatonDocument-{{ $hackatonDocument['id'] }}">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <x-marybadge class="badge-neutral" value="Документ #{{ $index + 1 }}" />
                                <x-marybutton type="button" wire:click="removeHackatonDocument({{ $index }})" class="btn-error btn-sm">
                                    Удалить
                                </x-marybutton>
                            </div>

                            <div class="mt-3 space-y-3">
                                <x-mary-input wire:model="hackatonDocuments.{{$index}}.name" label="Название" />

                                <x-marymarkdown
                                    label="Описание"
                                    wire:model="hackatonDocuments.{{$index}}.description"
                                    :config="$this->config"
                                />

                                <x-maryfile wire:model="hackatonDocuments.{{$index}}.file_url" label="Новый файл (необязательно)" />

                                @if (!empty($hackatonDocument['existing_file_url']))
                                    <a class="link link-primary text-sm" href="{{ asset('storage/' . $hackatonDocument['existing_file_url']) }}" target="_blank" rel="noopener noreferrer">
                                        Открыть текущий файл
                                    </a>
                                @endif

                                <x-maryradio
                                    label="Тип документа"
                                    wire:model="hackatonDocuments.{{$index}}.filling_by_team_member"
                                    :options="$documentTypes"
                                    inline
                                />
                            </div>
                        </x-marycard>
                    @endforeach
                </div>
            </div>

            <div class="card border border-base-300 bg-base-100/50 p-4 sm:p-5 space-y-4">
                <h2 class="text-lg font-semibold">Автоматизации после завершения</h2>
                <label class="label cursor-pointer justify-start gap-3">
                    <input type="checkbox" class="checkbox checkbox-primary" wire:model="auto_publish_results_announcement" />
                    <span class="label-text">Автоматически опубликовать анонс с итогами</span>
                </label>
                <label class="label cursor-pointer justify-start gap-3">
                    <input type="checkbox" class="checkbox checkbox-primary" wire:model="auto_issue_certificates" />
                    <span class="label-text">Автоматически выдать сертификаты участникам</span>
                </label>
                <x-maryfile wire:model="certificateTemplate" label="Шаблон сертификата (PDF)" hint="Нужен для автовыдачи сертификатов" />
                @if(filled($hackaton->certificate_template_path))
                    <p class="text-sm text-base-content/70">Текущий шаблон загружен.</p>
                @endif
            </div>

            <x-slot:actions>
                <a href="{{ route('organizer.dashboard') }}">
                    <x-marybutton type="button" label="Отмена" class="btn-ghost" />
                </a>
                <x-marybutton label="Сохранить изменения" type="submit" class="btn-primary" spinner="save"
                    wire:loading.attr="disabled" />
            </x-slot:actions>
        </x-maryform>
    </x-marycard>

    @can('viewActivityHistory', $hackaton)
        <x-marycard class="card card-border bg-base-100">
            <x-activity-timeline :subject="$hackaton" :limit="20" />
        </x-marycard>
    @endcan
</div>
