<?php

use App\Models\Hackaton;
use App\Models\Team;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Layout('layouts::app', ['title' => 'Создание команды'])] class extends Component {
    use WithFileUploads;

    //----------------------------------------------------------------
    #[Validate(['title' => 'required'], message: ['title.required' => 'Заголовок должен быть заполнен'])]
    public string $title = '';

    #[Validate(['description' => 'required'], message: ['description.required' => 'Описание должно быть заполнено'])]
    public string $description = '';

    #[
        Validate(
            ['photo' => ['required', 'image', 'max:4096']],
            message: [
                'photo.required' => 'Изображение обязательно',
                'photo.image' => 'Файл должен быть валидным изображением',
                'photo.max' => 'Размер изображения не может быть больше 4 Мбайт',
            ],
        ),
    ]
    public $photo;

    #[Validate(['required', 'exists:hackatons,id'])]
    public $hackaton_id = null;

    public bool $is_public = true;

    //----------------------------------------------------------------
    #[
        Validate(
            [
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
            ],
        ),
    ]
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
    #[
        Validate(
            rule: [
                'socialLinks.*.name' => ['required', 'min:2'],
                'socialLinks.*.url' => ['required'],
            ],
            message: [
                'socialLinks.*.name.required' => 'Имя обязательно',
                'socialLinks.*.name.min' => 'Длина имени должна быть не менее 2 символов',
                'socialLinks.*.url.required' => 'Ссылка обязательна',
            ],
        ),
    ]
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
            $newRole = $team->roles()->create([
                'title' => $role['title'],
                'description' => $role['description'],
                'team_id' => $team->id,
                'role_id' => $role['role'],
                'user_id' => null,
            ]);

            if (!empty($role['skills'])) {
                $newRole->skills()->sync($role['skills']);
            }

        }

        $this->redirect('/profile/teams');
    }

    //----------------------------------------------------------------
    #[Computed]
    public function hackatons()
    {
        $hackatons = Hackaton::query()->where('is_public', '=', '1')->get();

        $hackatons_array = [
            [
                'name' => 'Выберите хакатон',
            ],
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
        $roles = [];
        $roles[] = [
            'name' => 'Выберите категорию роли',
        ];
        foreach (\App\Models\Role::all() as $role) {
            $roles[] = [
                'id' => $role->id,
                'name' => $role->name,
            ];
        }
        return $roles;
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

        $progressSteps = [
            filled($title),
            filled($description),
            !empty($photo),
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
            <li class="opacity-70">Создание команды</li>
        </ul>
    </div>

    <x-mary-card class="card card-border bg-base-100">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold">Создание команды</h1>
                <p class="text-sm text-base-content/70">
                    Заполните профиль команды, добавьте роли и ссылки на ваши ресурсы.
                </p>
            </div>
            <x-marybadge class="badge-primary" value="Шаг 1 из 1" />
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
                    <x-mary-input wire:model="title" label="Название команды" placeholder="Например, Team Phoenix" />
                    <x-marymarkdown wire:model="description" label="Описание команды" :config="$this->config" />
                </div>

                <div class="card border border-base-300 bg-base-100/50 p-4 sm:p-5 space-y-4">
                    <h2 class="text-lg font-semibold">Обложка и хакатон</h2>
                    <x-maryfile label="Обложка команды" wire:model="photo" accept="image/png, image/jpeg, image/webp"
                        hint="PNG/JPEG/WebP, до 4 МБ" />
                    @if ($photo)
                        <div class="rounded-xl border border-base-300 bg-base-200 p-2">
                            <img class="w-full object-contain h-64 rounded-lg"
                                src="{{ $photo->temporaryUrl() }}" alt="Превью обложки команды">
                        </div>
                    @endif

                    <x-maryselect label="Хакатон" wire:model="hackaton_id" :options="$this->hackatons" />
                </div>
            </div>

            <div class="card border border-base-300 bg-base-100/50 p-4 sm:p-5 space-y-4">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-semibold">Социальные ссылки</h2>
                        <p class="text-sm text-base-content/70">Добавьте контакты, чтобы участники могли быстро связаться с вами.</p>
                    </div>
                    <x-mary-button type="button" wire:click="addSocialLink" label="Добавить ссылку" class="btn-primary btn-sm" />
                </div>

                @if (empty($socialLinks))
                    <div class="rounded-xl border border-dashed border-base-300 p-4 text-sm text-base-content/70">
                        Пока нет социальных ссылок.
                    </div>
                @endif

                <div class="space-y-3">
                    @foreach ($socialLinks as $index => $socialLink)
                        <x-mary-card class="bg-base-200" wire:key="socialLink-{{ $socialLink['id'] }}">
                            <div class="flex items-center justify-between gap-2">
                                <x-marybadge class="badge-neutral" value="Ссылка #{{ $index + 1 }}" />
                                <x-marybutton type="button" class="btn-error btn-sm" wire:click="removeSocialLink({{ $index }})">
                                    Удалить
                                </x-marybutton>
                            </div>

                            <div class="mt-3 grid grid-cols-1 gap-3">
                                <x-mary-input wire:model="socialLinks.{{ $index }}.name"
                                    label="Название" placeholder="Например, Telegram" />
                                <x-mary-input wire:model="socialLinks.{{ $index }}.url" label="Ссылка"
                                    placeholder="https://..." />
                            </div>
                        </x-mary-card>
                    @endforeach
                </div>
            </div>

            <div class="card border border-base-300 bg-base-100/50 p-4 sm:p-5 space-y-4">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-semibold">Роли в команде</h2>
                        <p class="text-sm text-base-content/70">Добавьте роли, на которые будут подавать заявки участники.</p>
                    </div>
                    <x-marybutton type="button" class="btn-primary btn-sm" wire:click="addRole" label="Добавить роль" />
                </div>

                @if (empty($roles))
                    <div class="rounded-xl border border-dashed border-base-300 p-4 text-sm text-base-content/70">
                        Пока нет ролей. Добавьте хотя бы одну роль для набора участников.
                    </div>
                @endif

                <div class="space-y-3">
                    @foreach ($roles as $index => $role)
                        <x-marycard class="bg-base-200" wire:key="role-{{ $role['id'] }}">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <x-marybadge class="badge-neutral" value="Роль #{{ $index + 1 }}" />
                                <x-marybutton type="button" wire:click="removeRole({{ $index }})" label="Удалить"
                                    class="btn-error btn-sm" />
                            </div>

                            <div class="mt-3 space-y-3">
                                <x-mary-input wire:model="roles.{{ $index }}.title" label="Название роли" />
                                <x-marymarkdown disk="public" folder="team_markdown"
                                    wire:model="roles.{{ $index }}.description" label="Описание роли"
                                    :config="$this->config" />
                                <x-maryselect label="Категория роли" wire:model="roles.{{ $index }}.role"
                                    :options="$this->rolesData" />
                                <x-marychoices-offline label="Навыки роли"
                                    wire:model="roles.{{ $index }}.skills" :options="$this->skillsData"
                                    placeholder="Навыки..." clearable searchable />
                            </div>
                        </x-marycard>
                    @endforeach
                </div>
            </div>

            <x-slot:actions>
                <a href="/profile/teams">
                    <x-marybutton type="button" label="Отмена" class="btn-ghost" />
                </a>
                <x-marybutton type="submit" label="Создать команду" class="btn-primary" spinner="save"
                    wire:loading.attr="disabled" />
            </x-slot:actions>
        </x-maryform>
    </x-mary-card>
</div>
