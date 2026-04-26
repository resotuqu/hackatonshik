<?php

use App\Models\Hackaton;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Layout('layouts::app', ['title' => 'Изменение хакатона'])]
class extends Component {
    use WithFileUploads, \Mary\Traits\Toast;

    public Hackaton $hackaton;

    #[Validate(['title' => 'required'], message: ['title.required' => 'Заголовок должен быть заполнен'])]
    public string $title = '';

    #[Validate(['description' => 'required'], message: ['description.required' => 'Описание должно быть заполнено'])]
    public string $description = '';

    #[Validate(['photo' => ['nullable', 'image', 'max:4096']], message: [
        'photo.image' => 'Файл должен быть валидным изображением',
        'photo.max' => 'Размер изображения не может быть больше 4 Мбайт',
    ])]
    public $photo;

    #[Validate(['galleryPhotos.*' => ['nullable', 'image', 'max:5120']], message: [
        'galleryPhotos.*.image' => 'Каждый файл галереи должен быть изображением',
        'galleryPhotos.*.max' => 'Каждое изображение в галерее не может быть больше 5 МБ',
    ])]
    public array $galleryPhotos = [];

    public array $imagesToDelete = [];

    #[Validate([
        'start_at' => ['required', 'date'],
    ], message: [
        'start_at.required' => 'Выберите дату начала',
        'start_at.date' => 'Неверный формат даты',
    ])]
    public $start_at;

    #[Validate([
        'end_at' => ['required', 'date', 'after:start_at'],
    ], message: [
        'end_at.required' => 'Выберите дату конца',
        'end_at.date' => 'Неверный формат даты',
        'end_at.after' => 'Дата конца не может быть раньше даты начала',
    ])]
    public $end_at;

    public bool $is_public = true;

    #[Validate([
        'hackatonDocuments.*.name' => ['required'],
        'hackatonDocuments.*.description' => ['required'],
        'hackatonDocuments.*.filling_by_team_member' => ['required'],
    ], message: [
        'hackatonDocuments.*.name.required' => 'Поле названия необходимо для заполнения',
        'hackatonDocuments.*.description.required' => 'Поле описания необходимо для заполнения',
        'hackatonDocuments.*.filling_by_team_member.required' => 'Выберите тип документа',
    ])]
    public $hackatonDocuments = [];

    public array $documentTypes = [
        ['id' => 0, 'name' => 'Информационный документ', 'hint' => 'Положение о проведении, регламент и т.д.'],
        ['id' => 1, 'name' => 'Заполняемый документ', 'hint' => 'Согласие на обработку персональных данных и т.д.'],
    ];

    public array $config = [
        'toolbar' => ['heading', 'bold', 'italic', '|', 'preview'],
        'uploadImage' => false,
    ];

    public function mount(Hackaton $hackaton)
    {
        if (Auth::id() !== $hackaton->user_id) {
            $this->redirect('/profile/hackatons');
            return;
        }

        $this->hackaton = $hackaton->load('images');
        $this->title = $hackaton->title;
        $this->description = $hackaton->description;
        $this->start_at = $hackaton->start_at;
        $this->end_at = $hackaton->end_at;
        $this->is_public = (bool) $hackaton->is_public;

        foreach ($hackaton->documents as $document) {
            $this->hackatonDocuments[] = [
                'id' => uniqid(),
                'db_id' => $document->id,
                'name' => $document->name,
                'description' => $document->description,
                'existing_file_url' => $document->file_url,
                'file_url' => null,
                'filling_by_team_member' => $document->filling_by_team_member,
            ];
        }
    }

    public function addHackatonDocument(): void
    {
        $this->hackatonDocuments[] = [
            'id' => uniqid(),
            'db_id' => null,
            'name' => '',
            'description' => '',
            'existing_file_url' => null,
            'file_url' => null,
            'filling_by_team_member' => '',
        ];
    }

    public function removeHackatonDocument($index): void
    {
        unset($this->hackatonDocuments[$index]);
        $this->hackatonDocuments = array_values($this->hackatonDocuments);
    }

    public function markImageForDelete(int $imageId): void
    {
        if (!in_array($imageId, $this->imagesToDelete, true)) {
            $this->imagesToDelete[] = $imageId;
        }
    }

    public function save(): void
    {
        try {
            $this->validate();
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->error('Ошибка заполнения полей !', position: 'toast-center toast-top');
            throw $e;
        }

        foreach ($this->hackatonDocuments as $index => $hackatonDocument) {
            $hasNewFile = !empty($hackatonDocument['file_url']);
            $hasOldFile = !empty($hackatonDocument['existing_file_url']);

            if (!$hasNewFile && !$hasOldFile) {
                $this->addError('hackatonDocuments.' . $index . '.file_url', 'Необходимо загрузить документ');
                $this->error('Ошибка заполнения полей !', position: 'toast-center toast-top');
                return;
            }
        }

        $data = [
            'title' => $this->title,
            'description' => $this->description,
            'start_at' => $this->start_at,
            'end_at' => $this->end_at,
            'is_public' => $this->is_public,
        ];

        if ($this->photo) {
            $data['image_url'] = $this->photo->storePublicly(path: 'hackaton_photos', options: 'public');
        }

        $this->hackaton->update($data);
        $this->hackaton->syncStatusByTimeline();

        $existingDocuments = $this->hackaton->documents()->get()->keyBy('id');
        $savedDocumentIds = [];

        foreach ($this->hackatonDocuments as $hackatonDocument) {
            $dbId = $hackatonDocument['db_id'] ?? null;

            if (!empty($dbId) && $existingDocuments->has($dbId)) {
                $document = $existingDocuments->get($dbId);
                $fileUrl = $document->file_url;

                if (!empty($hackatonDocument['file_url'])) {
                    $fileUrl = $hackatonDocument['file_url']->storePublicly('hackaton_documents', options: 'public');
                }

                $document->update([
                    'name' => $hackatonDocument['name'],
                    'description' => $hackatonDocument['description'],
                    'file_url' => $fileUrl,
                    'filling_by_team_member' => $hackatonDocument['filling_by_team_member'],
                ]);

                $savedDocumentIds[] = $document->id;
                continue;
            }

            $newDocumentFile = $hackatonDocument['file_url']->storePublicly('hackaton_documents', options: 'public');
            $newDocument = $this->hackaton->documents()->create([
                'name' => $hackatonDocument['name'],
                'description' => $hackatonDocument['description'],
                'file_url' => $newDocumentFile,
                'filling_by_team_member' => $hackatonDocument['filling_by_team_member'],
            ]);

            $savedDocumentIds[] = $newDocument->id;
        }

        $documentsToDelete = $this->hackaton->documents();
        if (!empty($savedDocumentIds)) {
            $documentsToDelete->whereNotIn('id', $savedDocumentIds);
        }
        $documentsToDelete->delete();

        if (!empty($this->imagesToDelete)) {
            $this->hackaton->images()->whereIn('id', $this->imagesToDelete)->delete();
        }

        $sortOrder = (int) $this->hackaton->images()->max('sort_order') + 1;
        foreach ($this->galleryPhotos as $galleryPhoto) {
            $this->hackaton->images()->create([
                'path' => $galleryPhoto->storePublicly(path: 'hackaton_gallery', options: 'public'),
                'sort_order' => $sortOrder++,
            ]);
        }

        $this->success('Хакатон обновлён !', position: 'toast-center toast-top');
        $this->redirect('/profile/hackatons');
    }
};
?>

<div class="mx-auto w-full max-w-6xl space-y-4">
    <x-marytoast />
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
            <li><a href="/profile/hackatons">Мои хакатоны</a></li>
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
                    <x-marydatetime label="Дата начала" wire:model="start_at" />
                    <x-marydatetime label="Дата конца" wire:model="end_at" />
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

            <x-slot:actions>
                <a href="/profile/hackatons">
                    <x-marybutton type="button" label="Отмена" class="btn-ghost" />
                </a>
                <x-marybutton label="Сохранить изменения" type="submit" class="btn-primary" spinner="save"
                    wire:loading.attr="disabled" />
            </x-slot:actions>
        </x-maryform>
    </x-marycard>
</div>
