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

    use WithFileUploads, \Mary\Traits\Toast;

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
        if (Auth::id() !== $this->team->user_id) {
            $this->redirect('/profile/teams');
            return;
        }

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
        $this->success('Команда обновлена!', position: 'toast-center toast-top');
        $this->redirect('/profile/teams');
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
        if (Auth::id() !== $team->user_id) {
            $this->redirect('/profile/teams');
            return;
        }

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

<div class="mx-auto w-full max-w-6xl space-y-4">
    @php
        $hasFilledRole = collect($roles)->contains(
            fn ($role) => filled($role['title'] ?? null)
                && filled($role['description'] ?? null)
                && filled($role['role'] ?? null)
        );
        $hasPhoto = !empty($photo) || filled($team->image_url ?? null);

        $progressSteps = [
            filled($title),
            filled($description),
            $hasPhoto,
            filled($hackaton_id),
            $hasFilledRole,
        ];
        $completedSteps = collect($progressSteps)->filter()->count();
        $totalSteps = count($progressSteps);
        $progressPercent = (int) round(($completedSteps / max($totalSteps, 1)) * 100);
    @endphp

    <div class="text-sm breadcrumbs">
        <ul>
            <li><a href="/">Главная</a></li>
            <li><a href="/profile/teams">Мои команды</a></li>
            <li class="opacity-70">Редактирование команды</li>
        </ul>
    </div>

    <x-mary-card class="card card-border bg-base-100">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold">Редактирование команды</h1>
                <p class="text-sm text-base-content/70">
                    Обновите данные команды, роли и контакты. Занятые роли удалить нельзя.
                </p>
            </div>
            <x-marybadge class="badge-neutral" value="{{ $team->title }}" />
        </div>
    </x-mary-card>

    <x-mary-card class="card card-border bg-base-100">
        <div class="space-y-2">
            <div class="flex items-center justify-between gap-2">
                <p class="text-sm font-medium">Прогресс заполнения</p>
                <span class="text-sm text-base-content/70">{{ $completedSteps }}/{{ $totalSteps }}</span>
            </div>
            <progress class="progress progress-primary w-full" value="{{ $progressPercent }}" max="100"></progress>
            <p class="text-xs text-base-content/70">{{ $progressPercent }}% заполнено</p>
        </div>
    </x-mary-card>

    <x-mary-card class="w-full justify-self-center card card-border bg-base-100">
        <x-maryform wire:submit="save" class="space-y-6">
            <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                <div class="card border border-base-300 bg-base-100/50 p-4 sm:p-5 space-y-4">
                    <h2 class="text-lg font-semibold">Основная информация</h2>
                    <x-mary-input label="Название команды" wire:model="title" />
                    <x-marymarkdown wire:model="description" :config="$config" label="Описание команды" />
                </div>

                <div class="card border border-base-300 bg-base-100/50 p-4 sm:p-5 space-y-4">
                    <h2 class="text-lg font-semibold">Обложка и хакатон</h2>
                    <x-maryfile label="Обложка команды" wire:model="photo"
                        hint="Загрузите файл только если хотите заменить текущий" />
                    @if ($photo)
                        <div class="rounded-xl border border-base-300 bg-base-200 p-2">
                            <img class="w-full object-contain h-64 rounded-lg"
                                src="{{ $photo->temporaryUrl() }}" alt="Превью обложки команды">
                        </div>
                    @elseif(!empty($team->image_url))
                        <div class="rounded-xl border border-base-300 bg-base-200 p-2">
                            <img class="w-full object-contain h-64 rounded-lg"
                                src="{{ asset('storage/' . $team->image_url) }}" alt="Текущая обложка команды">
                        </div>
                    @endif

                    <x-maryselect label="Хакатон" :options="$this->hackatons" wire:model="hackaton_id" />
                </div>
            </div>

            <div class="card border border-base-300 bg-base-100/50 p-4 sm:p-5 space-y-4">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-semibold">Социальные ссылки</h2>
                        <p class="text-sm text-base-content/70">Обновите площадки, где с вами можно связаться.</p>
                    </div>
                    <x-mary-button type="button" wire:click="addSocialLink" label="Добавить ссылку" class="btn-primary btn-sm" />
                </div>

                @if (empty($socialLinks))
                    <div class="rounded-xl border border-dashed border-base-300 p-4 text-sm text-base-content/70">
                        Пока нет социальных ссылок.
                    </div>
                @endif

                <div class="space-y-3">
                    @foreach($socialLinks as $index => $socialLink)
                        <x-mary-card class="bg-base-200" wire:key="socialLink-{{ $socialLink['id'] }}">
                            <div class="flex items-center justify-between gap-2">
                                <x-marybadge class="badge-neutral" value="Ссылка #{{ $index + 1 }}" />
                                <x-mary-button type="button" class="btn-error btn-sm" wire:click="removeSocialLink({{ $index }})">
                                    Удалить
                                </x-mary-button>
                            </div>

                            <div class="mt-3 grid grid-cols-1 gap-3">
                                <x-mary-input wire:model="socialLinks.{{$index}}.name" label="Название" />
                                <x-mary-input wire:model="socialLinks.{{$index}}.url" label="Ссылка" />
                            </div>
                        </x-mary-card>
                    @endforeach
                </div>
            </div>

            <div class="card border border-base-300 bg-base-100/50 p-4 sm:p-5 space-y-4">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-semibold">Роли в команде</h2>
                        <p class="text-sm text-base-content/70">Управляйте ролями и требованиями к кандидатам.</p>
                    </div>
                    <x-mary-button type="button" class="btn-primary btn-sm" wire:click="addRole" label="Добавить роль" />
                </div>

                @if (empty($roles))
                    <div class="rounded-xl border border-dashed border-base-300 p-4 text-sm text-base-content/70">
                        Пока нет ролей.
                    </div>
                @endif

                <div class="space-y-3">
                    @foreach($roles as $index => $role)
                        <x-mary-card class="bg-base-200" wire:key="role-{{ $role['id'] }}">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <div class="flex items-center gap-2">
                                    <x-marybadge class="badge-neutral" value="Роль #{{ $index + 1 }}" />
                                    @if($role['is_occupied'] ?? false)
                                        <x-marybadge value="Роль занята" class="badge-error text-white" />
                                    @endif
                                </div>

                                <x-mary-button
                                    type="button"
                                    wire:click="removeRole({{ $index }})"
                                    label="Удалить"
                                    class="btn-error btn-sm"
                                    :disabled="$role['is_occupied'] ?? false"
                                />
                            </div>

                            <div class="mt-3 space-y-3">
                                <x-mary-input wire:model="roles.{{$index}}.title" label="Название роли" />
                                <x-marymarkdown disk="public" folder="team_markdown"
                                    wire:model="roles.{{$index}}.description" label="Описание роли" :config="$this->config" />
                                <x-maryselect label="Категория роли" wire:model="roles.{{$index}}.role" :options="$this->rolesData" />
                                <x-marychoices-offline
                                    label="Навыки роли"
                                    wire:model="roles.{{$index}}.skills"
                                    :options="$this->skillsData"
                                    placeholder="Навыки..."
                                    clearable
                                    searchable />
                            </div>
                        </x-mary-card>
                    @endforeach
                </div>
            </div>

            <x-slot:actions>
                <a href="/profile/teams">
                    <x-mary-button type="button" label="Отмена" class="btn-ghost" />
                </a>
                <x-mary-button type="submit" label="Сохранить изменения" class="btn-primary" spinner="save"
                    wire:loading.attr="disabled" />
            </x-slot:actions>
        </x-maryform>
    </x-mary-card>
</div>
