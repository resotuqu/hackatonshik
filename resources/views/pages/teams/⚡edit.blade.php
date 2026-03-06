<?php

use App\Models\Hackaton;
use App\Models\Team;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Layout('layouts::app', ['title' => 'Изменение команды'])]
class extends Component {

    use WithFileUploads;

    public Team $team;

    //----------------------------------------------------------------
    #[Validate(['title' => 'required'], message: ['title.required' => 'Заголовок должен быть заполнен'])]
    public string $title = '';

    #[Validate(['description' => 'required'], message: ['description.required' => 'Описание должно быть заполнено'])]
    public string $description = '';

    #[Validate(['photo' => ['sometimes', 'max:4096']], message: [
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
            'db_id' => null,
            'title' => '',
            'skills' => [],
            'description' => '',
            'role' => '',
            'is_occupied' => false,
        ];
    }

    public function removeRole($index): void
    {
        if (($this->roles[$index]['is_occupied'] ?? false) === true) {
            return;
        }

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
            'db_id' => null,
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

        $data = [
            'title'       => $this->title,
            'description' => $this->description,
            'hackaton_id' => $this->hackaton_id,
            'is_public'   => $this->is_public,
        ];

        if ($this->photo) {
            $path = $this->photo->storePublicly('team_photos', 'public');
            $data['image_url'] = $path;
        }

        $this->team->update($data);

        $savedSocialLinkIds = [];
        foreach ($this->socialLinks as $socialLink) {
            $existingSocialLinkId = $socialLink['db_id'] ?? null;
            if (!empty($existingSocialLinkId)) {
                $existingSocialLink = $this->team->socialLinks()->whereKey($existingSocialLinkId)->first();
                if ($existingSocialLink) {
                    $existingSocialLink->update([
                        'name' => $socialLink['name'],
                        'url' => $socialLink['url'],
                    ]);
                    $savedSocialLinkIds[] = $existingSocialLink->id;
                    continue;
                }
            }

            $newSocialLink = $this->team->socialLinks()->create([
                'name' => $socialLink['name'],
                'url' => $socialLink['url'],
            ]);
            $savedSocialLinkIds[] = $newSocialLink->id;
        }

        $socialLinksToDelete = $this->team->socialLinks();
        if (!empty($savedSocialLinkIds)) {
            $socialLinksToDelete->whereNotIn('id', $savedSocialLinkIds);
        }
        $socialLinksToDelete->delete();

        $teamRolesById = $this->team->roles()->with('skills')->get()->keyBy('id');
        $savedRoleIds = [];
        foreach ($this->roles as $role) {
            $existingRoleId = $role['db_id'] ?? null;
            if (!empty($existingRoleId) && $teamRolesById->has($existingRoleId)) {
                $existingRole = $teamRolesById->get($existingRoleId);
                $existingRole->update([
                    'title' => $role['title'],
                    'description' => $role['description'],
                    'role_id' => $role['role'],
                ]);
                $existingRole->skills()->sync($role['skills'] ?? []);
                $savedRoleIds[] = $existingRole->id;
                continue;
            }

            $newRole = $this->team->roles()->create([
                'title' => $role['title'],
                'description' => $role['description'],
                'team_id' => $this->team->id,
                'role_id' => $role['role'],
                'user_id' => null,
            ]);
            $newRole->skills()->sync($role['skills'] ?? []);
            $savedRoleIds[] = $newRole->id;
        }

        $rolesToDelete = $this->team->roles()->whereNull('user_id');
        if (!empty($savedRoleIds)) {
            $rolesToDelete->whereNotIn('id', $savedRoleIds);
        }
        $rolesToDelete->delete();

    }

    //----------------------------------------------------------------
    #[Computed]
    public function hackatons()
    {
        $hackatons = Hackaton::query()->where('is_public', '=', '1')->get();

        $hackatons_array = [
            [
                'name' => 'Выберите хакатон'
            ]
        ];
        foreach ($hackatons as $hackaton) {
            $hackatons_array[] = [
                'id' => $hackaton->id,
                'name' => $hackaton->title,
            ];
        }

        return $hackatons_array;
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


    public function mount(Team $team)
    {
        $this->team = $team;
        $this->title = $team->title;
        $this->description = $team->description;
        $this->hackaton_id = $team->hackaton_id;
        foreach ($team->socialLinks as $socialLink) {
            $this->socialLinks[] = [
                'id' => uniqid(),
                'db_id' => $socialLink->id,
                'name' => $socialLink->name,
                'url' => $socialLink->url,
            ];
        }
        foreach ($team->roles as $role) {
            $skillIds = $role->skills->pluck('id')->toArray();

            $this->roles[] = [
                'id' => uniqid(),
                'db_id' => $role->id,
                'title' => $role->title,
                'skills' => $skillIds,
                'description' => $role->description,
                'role' => $role->role_id,
                'is_occupied' => $role->user_id !== null,
            ];
        }
    }

    public $config = [
        'toolbar' => ['heading', 'bold', 'italic', '|', 'preview'],
        'uploadImage' => false,
    ];


};
?>

<div>


    <x-mary-card title="Изменение команды" class="w-full lg:w-1/2 justify-self-center card card-border bg-base-100">
    <x-maryform  class="">
        {{--    Title    --}}
        <x-mary-input label="Заголовок" wire:model="title"/>

        {{--    Description    --}}
        <x-marymarkdown wire:model="description" :config="$config" label="Описание"/>

        {{--    Photo    --}}
        <x-maryfile label="Фотография" wire:model="photo"/>

        {{--    HackatonId    --}}
        <x-maryselect label="Хакатон" :options="$this->hackatons" wire:model="hackaton_id" />

        {{--SocialLinks--}}
        <div class="flex flex-col card">

            <x-mary-button wire:click="addSocialLink" label="Добавить социальную ссылку" />

            <div class="space-y-2 mt-4">
                @foreach($socialLinks as $index => $socialLink)
                    <x-mary-card class="bg-base-200" wire:key="socialLink-{{ $socialLink['id'] }}" title="Социальная ссылка">
                        <x-mary-button class="btn-error" wire:click="removeSocialLink({{ $index }})">
                            Удалить
                        </x-mary-button>

                        <div>
                            <x-mary-input wire:model="socialLinks.{{$index}}.name" label="Название социальной ссылки" />
                            <x-mary-input wire:model="socialLinks.{{$index}}.url" label="Ссылка" />
                        </div>
                    </x-mary-card>
                @endforeach
            </div>

        </div>

        {{--    Roles    --}}
        <div class="flex flex-col mt-4 w-full">

            <x-mary-button class="btn" wire:click="addRole" label="Добавить роль" />

            <div class="space-y-2 mt-4">
                @foreach($roles as $index => $role)
                    <x-mary-card title="Роль" class="bg-base-200" wire:key="role-{{ $role['id'] }}">
                        <div class="flex flex-col sm:flex-row gap-2 sm:gap-4 items-start sm:items-center">
                            @if($role['is_occupied'] ?? false)
                                <x-marybadge value="Роль занята" class="badge-error text-white" />
                            @endif

                            <x-mary-button
                                wire:click="removeRole({{ $index }})"
                                label="Удалить"
                                class="btn-error"
                                :disabled="$role['is_occupied'] ?? false"
                            />
                        </div>

                        <div>
                            <div class="text-black">
                                {{--Title--}}
                                <x-mary-input wire:model="roles.{{$index}}.title" label="Название"/>

                                {{--Description--}}
                                <x-marymarkdown disk="public" folder="team_markdown" wire:model="roles.{{$index}}.description" label="Описание" :config="$this->config" />

                                {{--Role--}}
                                <x-maryselect label="Категория роли" wire:model="roles.{{$index}}.role" :options="$this->rolesData" />

                                {{--Skills--}}
                                <x-marychoices-offline
                                    label="Навыки роли"
                                    wire:model="roles.{{$index}}.skills"
                                    :options="$this->skillsData"
                                    placeholder="Навыки..."
                                    clearable
                                    searchable />

                            </div>
                        </div>
                    </x-mary-card>
                @endforeach
            </div>

        </div>

        <x-slot:actions>
            <x-mary-button wire:click="save" label="Сохранить измененения" class="btn-primary" />
        </x-slot:actions>
    </x-maryform>
    </x-mary-card>
</div>
