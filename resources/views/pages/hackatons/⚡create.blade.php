<?php

use App\Enums\HackatonStatus;
use App\Models\Hackaton;
use App\Models\Team;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Layout('layouts::app', ['title' => 'Создание хакатона'])]
class extends Component {

    use WithFileUploads, \Mary\Traits\Toast;

    //----------------------------------------------------------------
    #[Validate(['title' => 'required'], message: ['title.required' => 'Заголовок должен быть заполнен'])]
    public string $title = '';

    #[Validate(['description' => 'required'], message: ['description.required' => 'Описание должно быть заполнено'])]
    public string $description = '';

    #[Validate(['photo' => ['required', 'image', 'max:4096']], message: [
        'photo.required' => 'Изображение обязательно',
        'photo.image' => 'Файл должен быть валидным изображением',
        'photo.max' => 'Размер изображения не может быть больше 4 Мбайт'
    ])]
    public $photo;

    #[Validate(['galleryPhotos.*' => ['nullable', 'image', 'max:5120']], message: [
        'galleryPhotos.*.image' => 'Каждый файл галереи должен быть изображением',
        'galleryPhotos.*.max' => 'Каждое изображение в галерее не может быть больше 5 МБ',
    ])]
    public array $galleryPhotos = [];

    #[Validate([
        'start_at' => ['required', 'date', 'after:now']
    ], message: [
        'start_at.required' => 'Выберите дату начала',
        'start_at.date' => 'Неверный формат даты',
        'start_at.after' => 'Дата начала может не раньше завтрашнего дня',
    ])]
    public $start_at;

    #[Validate([
        'end_at' => ['required', 'date', 'after:start_at']
    ], message: [
        'end_at.required' => 'Выберите дату конца',
        'end_at.date' => 'Неверный формат даты',
        'end_at.after' => 'Дата конца не может быть раньше следующего дня после начала',
    ])]
    public $end_at;


    public bool $is_public = true;

    //----------------------------------------------------------------


    #[Validate([
        'hackatonDocuments.*.name' => ['required'],
        'hackatonDocuments.*.description' => ['required'],
        'hackatonDocuments.*.file_url' => ['required', 'file'],
        'hackatonDocuments.*.filling_by_team_member' => ['required']
    ], message: [
        'hackatonDocuments.*.name.required' => 'Поле названия необходимо для заполнения',
        'hackatonDocuments.*.description.required' => 'Поле описания необходимо для заполнения',
        'hackatonDocuments.*.file_url.required' => 'Необходимо загрузить документ',
        'hackatonDocuments.*.filling_by_team_member.required' => 'Выберите тип документа',
    ])]
    public $hackatonDocuments = [];


    public function addHackatonDocument()
    {
        $this->hackatonDocuments[] = [
            'id' => uniqid(),
            'name' => '',
            'description' => '',
            'file_url' => '',
            'filling_by_team_member' => '',
        ];
    }

    public function removeHackatonDocument($index)
    {
        unset($this->hackatonDocuments[$index]);
        $this->hackatonDocuments = array_values($this->hackatonDocuments);
    }


    //----------------------------------------------------------------

    public function save()
    {
//        dd($this->hackatonDocuments[0]['file_url']->storePublicly('hackaton_documents', options: 'public'));
//        dd($this->hackatonDocuments);

        try {
            $this->validate();
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->error('Ошибка заполнения полей !', position: 'toast-center toast-top');
            throw $e;
        }

        $photo = $this->photo->storePublicly(path: 'hackaton_photos', options: 'public');

        $hackaton = Hackaton::create([
            'user_id' => Auth::user()->id,
            'title' => $this->title,
            'description' => $this->description,
            'image_url' => $photo,
            'start_at' => $this->start_at,
            'end_at' => $this->end_at,
            'is_public' => $this->is_public,
            'status' => $this->is_public ? HackatonStatus::REGISTRATION_OPEN : HackatonStatus::DRAFT,
        ]);

        foreach ($this->hackatonDocuments as $hackatonDocument) {
            $file = $hackatonDocument['file_url']->storePublicly('hackaton_documents', options: 'public');
            $hackaton->documents()->create([
                'name' => $hackatonDocument['name'],
                'description' => $hackatonDocument['description'],
                'file_url' => $file,
                'filling_by_team_member' => $hackatonDocument['filling_by_team_member'],
            ]);
        }

        foreach ($this->galleryPhotos as $index => $galleryPhoto) {
            $hackaton->images()->create([
                'path' => $galleryPhoto->storePublicly(path: 'hackaton_gallery', options: 'public'),
                'sort_order' => $index,
            ]);
        }

        $this->success('Хакатон создан !', position: 'toast-center toast-top');
        $this->redirect('/profile/hackatons');






    }

    //----------------------------------------------------------------

    public $documentTypes = [
        ['id' => 0, 'name' => 'Информационный документ', 'hint' => 'Положение о проведении, регламент и т.д.'],
        ['id' => 1, 'name' => 'Заполняемый документ', 'hint' => 'Согласие на обработку персональных данных и т.д.'],
    ];

    public function mount()
    {
    }

    public $config = [
        'toolbar' => ['heading', 'bold', 'italic', '|', 'preview'],
        'uploadImage' => false,
    ];
};

?>

<div class="mx-auto w-full max-w-6xl space-y-4">
    <x-marytoast/>
    @php
        $hasFilledDocument = collect($hackatonDocuments)->contains(function ($document) {
            $hasDocumentType = array_key_exists('filling_by_team_member', $document)
                && $document['filling_by_team_member'] !== ''
                && $document['filling_by_team_member'] !== null;

            return filled($document['name'] ?? null)
                && filled($document['description'] ?? null)
                && !empty($document['file_url'])
                && $hasDocumentType;
        });

        $progressSteps = [
            filled($title),
            filled($description),
            !empty($photo),
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
            <li class="opacity-70">Создание хакатона</li>
        </ul>
    </div>

    <x-marycard class="card card-border bg-base-100">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold">Создание хакатона</h1>
                <p class="text-sm text-base-content/70">
                    Укажите ключевую информацию, даты и документы для участников.
                </p>
            </div>
            <x-marybadge class="badge-primary" value="Шаг 1 из 1" />
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
                    <x-mary-input label="Название хакатона" wire:model="title" placeholder="Например, HackFest 2026"/>
                    <x-marymarkdown wire:model="description" :config="$this->config" label="Описание хакатона" />
                    <x-marydatetime label="Дата начала" wire:model="start_at"/>
                    <x-marydatetime label="Дата конца" wire:model="end_at"/>
                </div>

                <div class="card border border-base-300 bg-base-100/50 p-4 sm:p-5 space-y-4">
                    <h2 class="text-lg font-semibold">Обложка</h2>
                    <x-maryfile label="Обложка хакатона" wire:model="photo" hint="PNG/JPEG/WebP, до 4 МБ" />
                    @if ($photo)
                        <div class="rounded-xl border border-base-300 bg-base-200 p-2">
                            <img class="w-full object-contain h-64 rounded-lg" src="{{ $photo->temporaryUrl() }}" alt="Превью обложки хакатона">
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
                                     class="h-24 w-full rounded-lg object-cover border border-base-300">
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div class="card border border-base-300 bg-base-100/50 p-4 sm:p-5 space-y-4">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-semibold">Документы хакатона</h2>
                        <p class="text-sm text-base-content/70">Добавьте регламенты и документы для заполнения участниками.</p>
                    </div>
                    <x-marybutton type="button" class="btn-primary btn-sm" wire:click="addHackatonDocument">
                        Добавить документ
                    </x-marybutton>
                </div>

                @if (empty($hackatonDocuments))
                    <div class="rounded-xl border border-dashed border-base-300 p-4 text-sm text-base-content/70">
                        Пока нет документов. Добавьте хотя бы один информационный документ.
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

            <x-slot:actions>
                <a href="/profile/hackatons">
                    <x-marybutton type="button" label="Отмена" class="btn-ghost" />
                </a>
                <x-marybutton type="submit" label="Создать хакатон" spinner="save"
                    wire:loading.attr="disabled" class="btn-primary" />
            </x-slot:actions>
        </x-maryform>
    </x-marycard>
</div>
