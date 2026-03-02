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

    use WithFileUploads;

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

        $this->validate();

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

        $this->redirect('/profile/hackatons');



    }

    //----------------------------------------------------------------


    public function mount()
    {
    }


};
?>

<div>

    <x-livewire-form-layout submit-button-title="Создать хакатон" title="Создание хакатона">
        {{--    Title    --}}
        <x-livewire-form-input label="Заголовок" name="title" type="text" model="title"/>

        {{--    Description    --}}
        <div class="flex flex-col mt-4 w-full">
            <label for="description" class="text-white">Описание</label>
            <textarea id="description" wire:model="description"
                      class="bg-white rounded-sm py-2 mt-2">{{old('description', '')}}</textarea>
            @error('description')
            <p class="mt-2 text-red-500">{{$message}}</p>
            @enderror
        </div>

        {{--    Photo    --}}
        <x-livewire-form-input label="Фотография" name="photo" type="file" model="photo"/>
        @if ($photo)
            <img class="w-auto object-contain h-64 mt-2" src="{{ $photo->temporaryUrl() }}" alt="">
        @endif

        {{--    StartAt    --}}
        <x-livewire-form-input label="Дата начала" name="start_at" type="date" model="start_at"/>

        {{--    EndAt    --}}
        <x-livewire-form-input label="Дата конца" name="end_at" type="date" model="end_at"/>

        {{--    Documents    --}}
        <div class="flex flex-col mt-4 w-full">
            <label for="hackatonDocuments" class="text-white">Документы</label>
            <div class="space-y-2">
                @foreach($hackatonDocuments as $index => $hackatonDocument)
                    <div class="bg-slate-800 rounded-sm py-2 px-4 text-white"
                         wire:key="hackatonDocument-{{ $hackatonDocument['id'] }}">
                        <div class="flex flex-row space-x-4 items-center">
                            <button type="button" wire:click="removeHackatonDocument({{ $index }})"
                                    class="px-4 py-2 bg-red-500 hover:bg-red-400 rounded-sm cursor-pointer">
                                Удалить
                            </button>
                        </div>

                        <div>
                            <div class="text-black">


                                {{--Title--}}
                                <x-livewire-form-input model="hackatonDocuments.{{$index}}.name"
                                                       name="hackatonDocuments.{{$index}}.name"
                                                       label="Название" type="text"/>

                                {{--Description--}}
                                <div class="flex flex-col mt-4 w-full">
                                    <label for="hackatonDocuments.{{$index}}.description"
                                           class="text-white">Описание</label>
                                    <textarea id="hackatonDocuments.{{$index}}.description"
                                              wire:model="hackatonDocuments.{{$index}}.description"
                                              class="bg-white rounded-sm py-2 mt-2">{{old('hackatonDocuments.' . $index . '.description', '')}}</textarea>
                                    @error('hackatonDocuments.' . $index . '.description')
                                    <p class="mt-2 text-red-500">{{$message}}</p>
                                    @enderror
                                </div>

                                {{--File--}}
                                <x-livewire-form-input type="file" model="hackatonDocuments.{{$index}}.file_url"
                                                       name="hackatonDocuments.{{$index}}.file_url"
                                                       label="Файл документа"/>

                                {{--FillingByTeamMember--}}
                                <div class="flex flex-col mt-4 w-full">
                                    <p class="text-white mb-2">Тип документа</p>
                                    <label for="hackatonDocuments.{{$index}}.info" class="text-white">Информационный
                                        документ</label>
                                    <input id="hackatonDocuments.{{$index}}.info" value="0" type="radio"
                                           wire:model="hackatonDocuments.{{$index}}.filling_by_team_member"/>
                                    <label for="hackatonDocuments.{{$index}}.blueprint" class="text-white">Заполняется
                                        участником</label>
                                    <input id="hackatonDocuments.{{$index}}.blueprint" value="1" type="radio"
                                           wire:model="hackatonDocuments.{{$index}}.filling_by_team_member"/>
                                    @error('hackatonDocuments.' . $index . '.filling_by_team_member')
                                    <p class="mt-2 text-red-500">{{$message}}</p>
                                    @enderror
                                </div>


                            </div>

                        </div>
                    </div>
                @endforeach
            </div>
            <div>
                <button type="button"
                        class="px-4 mt-4 py-2 bg-blue-500 hover:bg-blue-400 rounded-sm cursor-pointer text-white"
                        wire:click="addHackatonDocument">
                    Добавить документ
                </button>
            </div>
        </div>


    </x-livewire-form-layout>
</div>
