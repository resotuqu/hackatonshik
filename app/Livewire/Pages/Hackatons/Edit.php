<?php

namespace App\Livewire\Pages\Hackatons;

use App\Enums\HackatonLevel;
use App\Models\Hackaton;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;

#[Layout('layouts::app', ['title' => 'Изменение хакатона'])]
class Edit extends Component
{
    use AuthorizesRequests, Toast, WithFileUploads;

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

    #[Validate(['prize_fund' => ['nullable', 'numeric', 'min:0', 'max:9999999999.99']], message: [
        'prize_fund.numeric' => 'Призовой фонд должен быть числом',
        'prize_fund.min' => 'Призовой фонд не может быть отрицательным',
    ])]
    public $prize_fund = null;

    #[Validate(['prize_places_count' => ['nullable', 'integer', 'min:1', 'max:1000']], message: [
        'prize_places_count.integer' => 'Количество призовых мест должно быть целым числом',
        'prize_places_count.min' => 'Призовых мест должно быть минимум 1',
    ])]
    public $prize_places_count = null;

    #[Validate(['level' => ['nullable', 'string']], message: [
        'level.string' => 'Неверное значение уровня',
    ])]
    public ?string $level = null;

    #[Validate([
        'registration_deadline_at' => ['nullable', 'date', 'before_or_equal:start_at'],
    ], message: [
        'registration_deadline_at.date' => 'Неверный формат даты',
        'registration_deadline_at.before_or_equal' => 'Дедлайн регистрации не может быть позже даты начала',
    ])]
    public $registration_deadline_at = null;

    public bool $auto_issue_certificates = false;

    public bool $auto_publish_results_announcement = false;

    public bool $is_results_public = false;

    public $certificateTemplate;

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

    public function mount(Hackaton $hackaton): void
    {
        $this->authorize('update', $hackaton);

        $this->hackaton = $hackaton->load('images');
        $this->title = $hackaton->title;
        $this->description = $hackaton->description;
        $this->start_at = $hackaton->start_at;
        $this->end_at = $hackaton->end_at;
        $this->is_public = (bool) $hackaton->is_public;
        $this->prize_fund = $hackaton->prize_fund;
        $this->prize_places_count = $hackaton->prize_places_count;
        $this->level = is_string($hackaton->level) ? $hackaton->level : null;
        $this->registration_deadline_at = $hackaton->registration_deadline_at;
        $this->auto_issue_certificates = (bool) $hackaton->auto_issue_certificates;
        $this->auto_publish_results_announcement = (bool) $hackaton->auto_publish_results_announcement;
        $this->is_results_public = (bool) $hackaton->is_results_public;

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
        if (! in_array($imageId, $this->imagesToDelete, true)) {
            $this->imagesToDelete[] = $imageId;
        }
    }

    public function save(): void
    {
        $this->authorize('update', $this->hackaton);

        try {
            $this->validate();
            $this->validate([
                'certificateTemplate' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            ]);
        } catch (ValidationException $e) {
            $this->error('Ошибка заполнения полей !', position: 'toast-center toast-top');
            throw $e;
        }

        foreach ($this->hackatonDocuments as $index => $hackatonDocument) {
            $hasNewFile = ! empty($hackatonDocument['file_url']);
            $hasOldFile = ! empty($hackatonDocument['existing_file_url']);

            if (! $hasNewFile && ! $hasOldFile) {
                $this->addError('hackatonDocuments.'.$index.'.file_url', 'Необходимо загрузить документ');
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
            'prize_fund' => $this->prize_fund !== '' ? $this->prize_fund : null,
            'prize_places_count' => $this->prize_places_count !== '' ? $this->prize_places_count : null,
            'level' => $this->level !== '' ? $this->level : null,
            'registration_deadline_at' => $this->registration_deadline_at !== '' ? $this->registration_deadline_at : null,
            'auto_issue_certificates' => $this->auto_issue_certificates,
            'auto_publish_results_announcement' => $this->auto_publish_results_announcement,
            'is_results_public' => $this->is_results_public,
        ];

        if ($this->certificateTemplate) {
            $data['certificate_template_path'] = $this->certificateTemplate->store('hackaton_certificates', 'local');
        }

        if ($this->photo) {
            $data['image_url'] = $this->photo->storePublicly(path: 'hackaton_photos', options: 'public');
        }

        $this->hackaton->update($data);
        $this->hackaton->syncStatusByTimeline();

        $existingDocuments = $this->hackaton->documents()->get()->keyBy('id');
        $savedDocumentIds = [];

        foreach ($this->hackatonDocuments as $hackatonDocument) {
            $dbId = $hackatonDocument['db_id'] ?? null;

            if (! empty($dbId) && $existingDocuments->has($dbId)) {
                $document = $existingDocuments->get($dbId);
                $fileUrl = $document->file_url;

                if (! empty($hackatonDocument['file_url'])) {
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
        if (! empty($savedDocumentIds)) {
            $documentsToDelete->whereNotIn('id', $savedDocumentIds);
        }
        $documentsToDelete->delete();

        if (! empty($this->imagesToDelete)) {
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

        $this->redirect(route('organizer.dashboard'));
    }

    /**
     * @return list<array{id: string, name: string}>
     */
    public function levelOptions(): array
    {
        $options = [['id' => '', 'name' => 'Не указан']];
        foreach (HackatonLevel::cases() as $case) {
            $options[] = ['id' => $case->value, 'name' => $case->label()];
        }

        return $options;
    }

    public function render()
    {
        return view('pages.hackatons.edit');
    }
}
