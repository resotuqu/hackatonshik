<?php

declare(strict_types=1);

namespace App\Livewire\Pages\Hackatons;

use App\Enums\HackatonLevel;
use App\Enums\HackatonStatus;
use App\Models\Hackaton;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;

#[Layout('layouts::app', ['title' => 'Создание хакатона'])]
class Create extends Component
{
    use Toast, WithFileUploads;

    public int $wizardStep = 1;

    public const WIZARD_LAST_STEP = 5;

    #[Validate(['title' => 'required'], message: ['title.required' => 'Заголовок должен быть заполнен'])]
    public string $title = '';

    #[Validate(['description' => 'required'], message: ['description.required' => 'Описание должно быть заполнено'])]
    public string $description = '';

    public $photo;

    #[Validate(['galleryPhotos.*' => ['nullable', 'image', 'max:5120']], message: [
        'galleryPhotos.*.image' => 'Каждый файл галереи должен быть изображением',
        'galleryPhotos.*.max' => 'Каждое изображение в галерее не может быть больше 5 МБ',
    ])]
    public array $galleryPhotos = [];

    #[Validate([
        'start_at' => ['required', 'date', 'after:now'],
    ], message: [
        'start_at.required' => 'Выберите дату начала',
        'start_at.date' => 'Неверный формат даты',
        'start_at.after' => 'Дата начала может не раньше завтрашнего дня',
    ])]
    public $start_at;

    #[Validate([
        'end_at' => ['required', 'date', 'after:start_at'],
    ], message: [
        'end_at.required' => 'Выберите дату конца',
        'end_at.date' => 'Неверный формат даты',
        'end_at.after' => 'Дата конца не может быть раньше следующего дня после начала',
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

    #[Validate([
        'hackatonDocuments.*.name' => ['required'],
        'hackatonDocuments.*.description' => ['required'],
        'hackatonDocuments.*.file_url' => ['required', 'file'],
        'hackatonDocuments.*.filling_by_team_member' => ['required'],
    ], message: [
        'hackatonDocuments.*.name.required' => 'Поле названия необходимо для заполнения',
        'hackatonDocuments.*.description.required' => 'Поле описания необходимо для заполнения',
        'hackatonDocuments.*.file_url.required' => 'Необходимо загрузить документ',
        'hackatonDocuments.*.filling_by_team_member.required' => 'Выберите тип документа',
    ])]
    public $hackatonDocuments = [];

    public $documentTypes = [
        ['id' => 0, 'name' => 'Информационный документ', 'hint' => 'Положение о проведении, регламент и т.д.'],
        ['id' => 1, 'name' => 'Заполняемый документ', 'hint' => 'Согласие на обработку персональных данных и т.д.'],
    ];

    public $config = [
        'toolbar' => ['heading', 'bold', 'italic', '|', 'preview'],
        'uploadImage' => false,
    ];

    public function addHackatonDocument(): void
    {
        $this->hackatonDocuments[] = [
            'id' => uniqid(),
            'name' => '',
            'description' => '',
            'file_url' => '',
            'filling_by_team_member' => '',
        ];
    }

    public function removeHackatonDocument($index): void
    {
        unset($this->hackatonDocuments[$index]);
        $this->hackatonDocuments = array_values($this->hackatonDocuments);
    }

    public function wizardSubmit(): mixed
    {
        if ($this->wizardStep < self::WIZARD_LAST_STEP) {
            $this->nextStep();

            return null;
        }

        return $this->save();
    }

    public function nextStep(): void
    {
        $stepRules = $this->rulesForWizardStep($this->wizardStep);
        if ($stepRules !== []) {
            try {
                $this->validate($stepRules, $this->messagesForWizardStep($this->wizardStep));
            } catch (ValidationException $e) {
                $this->error('Проверьте поля шага', position: 'toast-center toast-top');
                throw $e;
            }
        }

        if ($this->wizardStep < self::WIZARD_LAST_STEP) {
            $this->wizardStep++;
        }
    }

    public function previousStep(): void
    {
        if ($this->wizardStep > 1) {
            $this->wizardStep--;
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function rulesForWizardStep(int $step): array
    {
        return match ($step) {
            1 => [
                'title' => 'required',
                'description' => 'required',
                'start_at' => ['required', 'date', 'after:now'],
                'end_at' => ['required', 'date', 'after:start_at'],
                'registration_deadline_at' => ['nullable', 'date', 'before_or_equal:start_at'],
            ],
            2 => [
                'prize_fund' => ['nullable', 'numeric', 'min:0', 'max:9999999999.99'],
                'prize_places_count' => ['nullable', 'integer', 'min:1', 'max:1000'],
                'level' => ['nullable', 'string'],
            ],
            3 => [],
            4 => array_merge([
                'photo' => ['required', 'image', 'max:4096'],
                'galleryPhotos.*' => ['nullable', 'image', 'max:5120'],
            ], count($this->hackatonDocuments) > 0 ? [
                'hackatonDocuments.*.name' => ['required'],
                'hackatonDocuments.*.description' => ['required'],
                'hackatonDocuments.*.file_url' => ['required', 'file'],
                'hackatonDocuments.*.filling_by_team_member' => ['required'],
            ] : []),
            default => [],
        };
    }

    /**
     * @return array<string, string>
     */
    private function messagesForWizardStep(int $step): array
    {
        return match ($step) {
            1 => [
                'title.required' => 'Заголовок должен быть заполнен',
                'description.required' => 'Описание должно быть заполнено',
                'start_at.required' => 'Выберите дату начала',
                'start_at.after' => 'Дата начала может не раньше завтрашнего дня',
                'end_at.required' => 'Выберите дату конца',
                'end_at.after' => 'Дата конца не может быть раньше следующего дня после начала',
            ],
            4 => [
                'photo.required' => 'Изображение обязательно',
                'photo.image' => 'Файл должен быть валидным изображением',
                'photo.max' => 'Размер изображения не может быть больше 4 Мбайт',
                'hackatonDocuments.*.name.required' => 'Поле названия необходимо для заполнения',
                'hackatonDocuments.*.description.required' => 'Поле описания необходимо для заполнения',
                'hackatonDocuments.*.file_url.required' => 'Необходимо загрузить документ',
                'hackatonDocuments.*.filling_by_team_member.required' => 'Выберите тип документа',
            ],
            default => [],
        };
    }

    public function save(): mixed
    {
        try {
            $this->validate();
            $this->validate([
                'photo' => ['required', 'image', 'max:4096'],
            ], [
                'photo.required' => 'Изображение обязательно',
                'photo.image' => 'Файл должен быть валидным изображением',
                'photo.max' => 'Размер изображения не может быть больше 4 Мбайт',
            ]);
        } catch (ValidationException $e) {
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
            'prize_fund' => $this->prize_fund !== '' ? $this->prize_fund : null,
            'prize_places_count' => $this->prize_places_count !== '' ? $this->prize_places_count : null,
            'level' => $this->level !== '' ? $this->level : null,
            'registration_deadline_at' => $this->registration_deadline_at !== '' ? $this->registration_deadline_at : null,
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

        return $this->redirect(route('organizer.dashboard'));
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

    public function render(): View
    {
        return view('pages.hackatons.create');
    }
}
