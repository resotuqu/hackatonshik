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

        $this->hackaton = $hackaton;
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

        $this->success('Хакатон обновлён !', position: 'toast-center toast-top');
        $this->redirect('/profile/hackatons');
    }
};
?>

<div>
    <x-marytoast />

    <x-marycard title="Изменение хакатона" class="w-full lg:w-1/2 justify-self-center card card-border bg-base-100">
        <x-maryform wire:submit="save">
            <x-mary-input label="Заголовок" wire:model="title" />

            <x-marymarkdown wire:model="description" :config="$this->config" />

            <x-maryfile label="Фотография" hint="Необязательно: загружайте только если хотите заменить" wire:model="photo" />
            @if ($photo)
                <img class="w-auto object-contain h-64 mt-2" src="{{ $photo->temporaryUrl() }}" alt="">
            @elseif(!empty($hackaton->image_url))
                <img class="w-auto object-contain h-64 mt-2" src="/uploads/{{ $hackaton->image_url }}" alt="">
            @endif

            <x-marydatetime label="Дата начала" wire:model="start_at" />
            <x-marydatetime lavel="Дата конца" wire:model="end_at" />

            <div class="flex flex-col mt-4 w-full">
                <x-marybutton type="button" class="btn-primary" wire:click="addHackatonDocument">
                    Добавить документ
                </x-marybutton>

                <div class="space-y-2 mt-4">
                    @foreach($hackatonDocuments as $index => $hackatonDocument)
                        <x-marycard class="bg-base-200" wire:key="hackatonDocument-{{ $hackatonDocument['id'] }}">
                            <div class="flex flex-col sm:flex-row gap-2 sm:gap-4 items-start sm:items-center">
                                <x-marybutton type="button" wire:click="removeHackatonDocument({{ $index }})" class="btn-error">
                                    Удалить
                                </x-marybutton>
                            </div>

                            <div class="text-black">
                                <x-mary-input wire:model="hackatonDocuments.{{$index}}.name" label="Название" />

                                <x-marymarkdown
                                    label="Описание"
                                    wire:model="hackatonDocuments.{{$index}}.description"
                                    :config="$this->config"
                                />

                                <x-maryfile wire:model="hackatonDocuments.{{$index}}.file_url" />

                                <x-maryradio
                                    label="Выберите тип документа"
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
                <x-marybutton label="Сохранить изменения" type="submit" class="btn-primary" />
            </x-slot:actions>
        </x-maryform>
    </x-marycard>
</div>
