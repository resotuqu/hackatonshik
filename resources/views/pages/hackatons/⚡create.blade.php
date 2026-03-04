<?php

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
            'is_public' => true,
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

<div>
    <x-marytoast/>
    <head>
        <link rel="stylesheet" href="https://unpkg.com/easymde/dist/easymde.min.css">
        <script src="https://unpkg.com/easymde/dist/easymde.min.js"></script>
    </head>
    <x-marycard title="Создание хакатона" class="w-full md:w-1/2 justify-self-center card card-border bg-base-100">
        <x-maryform>
            {{--    Title    --}}
            <x-mary-input label="Заголовок" wire:model="title"/>

            {{--    Description    --}}
            <x-marymarkdown wire:model="description" :config="$this->config"/>

            {{--    Photo    --}}
            <x-maryfile label="Фотография" wire:model="photo"/>
            @if ($photo)
                <img class="w-auto object-contain h-64 mt-2" src="{{ $photo->temporaryUrl() }}" alt="">
            @endif

            {{--    StartAt    --}}
            <x-marydatetime label="Дата начала" wire:model="start_at"/>

            {{--    EndAt    --}}
            <x-marydatetime lavel="Дата конца" wire:model="end_at"/>

            {{--    Documents    --}}
            <div class="flex flex-col mt-4 w-full">

                <x-marybutton type="button"
                              class="btn-primary"
                              wire:click="addHackatonDocument">
                    Добавить документ
                </x-marybutton>

                <div class="space-y-2 mt-4">
                    @foreach($hackatonDocuments as $index => $hackatonDocument)
                        <x-marycard class="bg-base-200"
                                    wire:key="hackatonDocument-{{ $hackatonDocument['id'] }}">
                            <div class="flex flex-row space-x-4 items-center">
                                <x-marybutton type="button" wire:click="removeHackatonDocument({{ $index }})"
                                              class="btn-error">
                                    Удалить
                                </x-marybutton>
                            </div>

                            <div>
                                <div class="text-black">


                                    {{--Title--}}
                                    <x-mary-input wire:model="hackatonDocuments.{{$index}}.name" label="Название"/>

                                    {{--Description--}}
                                    <x-marymarkdown label="Описание"
                                                    wire:model="hackatonDocuments.{{$index}}.description"
                                                    :config="$this->config"/>

                                    {{--File--}}
                                    <x-maryfile wire:model="hackatonDocuments.{{$index}}.file_url"/>

                                    {{--FillingByTeamMember--}}
                                    <x-maryradio label="Выберите тип документа"
                                                 wire:model="hackatonDocuments.{{$index}}.filling_by_team_member"
                                                 :options="$documentTypes" inline/>

                                </div>

                            </div>
                        </x-marycard>
                    @endforeach
                </div>

            </div>

            <x-slot:actions>
                <x-marybutton label="Создать хакатон" wire:click="save"/>
            </x-slot:actions>

        </x-maryform>
    </x-marycard>
</div>
