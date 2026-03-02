<?php

use App\Models\Hackaton;
use App\Models\Team;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Layout('layouts::app', ['title' => 'Создание команды'])]
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

    #[Validate(['required', 'exists:hackatons,id'])]
    public $hackaton_id = null;

    public bool $is_public = true;

    //----------------------------------------------------------------
    #[Validate([
        'roles.*.title' => ['required', 'min:3'],
        'roles.*.description' => ['required', 'max:255'],
        'roles.*.role' => ['required', 'exists:roles,id'],
    ],
        message: [
            'roles.*.title.required' => 'Название роли обязательно для заполнения',
            'roles.*.title.min' => 'Название роли должно содержать минимум 3 символа',
            'roles.*.description.required' => 'Описание роли обязательно',
            'roles.*.description.max' => 'Длина описания роли не может быть больше 255 символов',
            'roles.*.role.required' => 'Категория роли должна быть выбрана',
            'roles.*.role.exists' => 'ОШИБКА 19755. Напишите по этому поводу в техподдержку',
        ])]
    public $roles = [];

    public function addRole(): void
    {
        $this->roles[] = [
            'id' => uniqid(),
            'title' => '',
            'skills' => [],
            'description' => '',
            'role' => '',
        ];
    }

    public function removeRole($index): void
    {
        unset($this->roles[$index]);
        $this->roles = array_values($this->roles);
    }

    //----------------------------------------------------------------
    #[Validate(rule: [
        'socialLinks.*.name' => ['required', 'min:2'],
        'socialLinks.*.url' => ['required'],
    ], message: [
        'socialLinks.*.name.required' => 'Имя обязательно',
        'socialLinks.*.name.min' => 'Длина имени должна быть не менее 2 символов',
        'socialLinks.*.url.required' => 'Ссылка обязательна',

    ])]
    public $socialLinks = [];

    public function addSocialLink()
    {
        $this->socialLinks[] = [
            'id' => uniqid(),
            'name' => '',
            'url' => '',
        ];
    }

    public function removeSocialLink($index)
    {
        unset($this->socialLinks[$index]);
        $this->socialLinks = array_values($this->socialLinks);
    }

    //----------------------------------------------------------------

    public function save()
    {
        $this->validate();

        $photo = $this->photo->storePublicly(path: 'team_photos', options: 'public');

        // Team
        $team = Team::create([
            'user_id' => \Illuminate\Support\Facades\Auth::user()->id,
            'title' => $this->title,
            'description' => $this->description,
            'image_url' => $photo,
            'hackaton_id' => $this->hackaton_id,
            'is_public' => $this->is_public,
        ]);

        //Social Links Link
        foreach ($this->socialLinks as $socialLink) {
            $team->socialLinks()->create([
                'name' => $socialLink['name'],
                'url' => $socialLink['url'],
            ]);
        }

        //Roles
        foreach ($this->roles as $role) {
            $role = $team->roles()->create([
                'title' => $role['title'],
                'description' => $role['description'],
                'team_id' => $team->id,
                'role_id' => $role['role'],
                'user_id' => null,
            ]);
            foreach ($role['skills'] as $skill) {
                $role->skills()->attach(\App\Models\Skill::create([
                    'name' => $skill->name
                ]));
            }
        }

        $this->redirect('/profile/teams');

    }

    //----------------------------------------------------------------
    #[Computed]
    public function hackatons()
    {
        $hackatons = Hackaton::query()->where('is_public', '=', '1')->get();
        $this->hackaton_id = $hackatons[0]->id;
        return $hackatons;
    }

    #[Computed]
    public function rolesData()
    {
        return \App\Models\Role::all();
    }

    #[Computed]
    public function skillsData()
    {
        return \App\Models\Skill::all();
    }

    //----------------------------------------------------------------


    public function mount()
    {
        $this->addRole();
        $this->addSocialLink();
    }


};
?>

<div>

    <x-livewire-form-layout submit-button-title="Создать команду" title="Создание команды">
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
            <img class="w-auto object-contain h-64 mt-2" src="{{ $photo->temporaryUrl() }}">
        @endif

        {{--    HackatonId    --}}
        <div class="flex flex-col mt-4 w-full">
            <label for="hackaton_id" class="text-white">Хакатон</label>
            <select id="hackaton_id" wire:model="hackaton_id" class="bg-white rounded-sm py-2 mt-2">
                <option disabled value="">Выберите хакатон...</option>

                @foreach($this->hackatons as $hackaton)
                    {{--@foreach($hackatons as $hackaton)--}}
                    <option value="{{$hackaton->id}}">{{$hackaton->title}}</option>
                @endforeach
            </select>
            @error('hackaton_id')
            <p class="mt-2 text-red-500">{{$message}}</p>
            @enderror
        </div>

        {{--SocialLinks--}}
        <div class="flex flex-col mt-4 w-full">
            <label for="roles" class="text-white">Социальные ссылки</label>
            <div class="space-y-2">
                @foreach($socialLinks as $index => $socialLink)
                    <div class="bg-slate-800 rounded-sm py-2 px-4 text-white"
                         wire:key="socialLink-{{ $socialLink['id'] }}">
                        <div class="flex flex-row space-x-4 items-center">
                            <button type="button" wire:click="removeSocialLink({{ $index }})"
                                    class="px-4 py-2 bg-red-500 hover:bg-red-400 rounded-sm cursor-pointer">
                                Удалить
                            </button>
                        </div>

                        <div>
                            <div class="text-black">
                                {{--Title--}}
                                <x-livewire-form-input model="socialLinks.{{$index}}.name"
                                                       name="socialLinks.{{$index}}.name"
                                                       label="Название" type="text"/>
                                {{--Url--}}
                                <x-livewire-form-input model="socialLinks.{{$index}}.url"
                                                       name="socialLinks.{{$index}}.url"
                                                       label="Ссылка" type="text"/>
                            </div>

                        </div>
                    </div>
                @endforeach
            </div>

            {{--NewSocialLinkButton--}}
            <div>
                <button type="button"
                        class="px-4 mt-4 py-2 bg-blue-500 hover:bg-blue-400 rounded-sm cursor-pointer text-white"
                        wire:click="addSocialLink">
                    Добавить роль
                </button>
            </div>
        </div>

        {{--    Roles    --}}
        <div class="flex flex-col mt-4 w-full">
            <label for="roles" class="text-white">Роли</label>
            <div class="space-y-2">
                @foreach($roles as $index => $role)
                    <div class="bg-slate-800 rounded-sm py-2 px-4 text-white" wire:key="role-{{ $role['id'] }}">
                        <div class="flex flex-row space-x-4 items-center">
                            <button type="button" wire:click="removeRole({{ $index }})"
                                    class="px-4 py-2 bg-red-500 hover:bg-red-400 rounded-sm cursor-pointer">
                                Удалить
                            </button>
                        </div>

                        <div>
                            <div class="text-black">


                                {{--Title--}}
                                <x-livewire-form-input model="roles.{{$index}}.title" name="roles.{{$index}}.title"
                                                       label="Название" type="text"/>

                                {{--Description--}}
                                <div class="flex flex-col mt-4 w-full">
                                    <label for="roles.{{$index}}.description" class="text-white">Описание</label>
                                    <textarea id="description.{{$index}}.description"
                                              wire:model="roles.{{$index}}.description"
                                              class="bg-white rounded-sm py-2 mt-2">{{old('roles.' . $index . '.description', '')}}</textarea>
                                    @error('roles.' . $index . '.description')
                                    <p class="mt-2 text-red-500">{{$message}}</p>
                                    @enderror
                                </div>


                                {{--Role--}}
                                <div class="flex flex-col mt-4 w-full">
                                    <label for="roles.{{$index}}.role" class="text-white">Категория роли</label>
                                    <select id="roles.{{$index}}.role" wire:model="roles.{{$index}}.role"
                                            class="bg-white rounded-sm py-2 mt-2">
                                        <option disabled value="">Выберите роль...</option>
                                        @foreach($this->rolesData as $role)
                                            <option value="{{$role->id}}">{{$role->name}}</option>
                                        @endforeach
                                    </select>
                                    @error('roles.' . $index . '.role')
                                    <p class="mt-2 text-red-500">{{$message}}</p>
                                    @enderror
                                </div>

                                {{--Skills--}}
                                <div class="flex flex-col mt-4 w-full">
                                    <label for="roles.{{$index}}.skills" class="text-white">Навыки роли</label>
                                    <select multiple id="roles.{{$index}}.skills" wire:model="roles.{{$index}}.skills"
                                            class="bg-white rounded-sm py-2 mt-2">
                                        <option disabled value="">Выберите навыки...</option>
                                        @foreach($this->skillsData as $skill)
                                            <option value="{{$skill->id}}">{{$skill->name}}</option>
                                        @endforeach
                                    </select>
                                    @error('roles.' . $index . '.skills')
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
                        wire:click="addRole">
                    Добавить роль
                </button>
            </div>
        </div>

    </x-livewire-form-layout>
</div>
