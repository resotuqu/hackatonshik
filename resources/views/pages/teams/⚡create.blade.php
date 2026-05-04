<?php

use App\Models\Hackaton;
use App\Models\Team;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Layout('layouts::app', ['title' => 'Создание команды'])] class extends Component {
    use WithFileUploads;

    public int $step = 1;

    public const int TOTAL_STEPS = 4;

    public string $title = '';

    public string $description = '';

    public $photo;

    public $hackaton_id = null;

    public bool $is_public = true;

    public $roles = [];

    public $socialLinks = [];

    public $config = [
        'toolbar' => ['heading', 'bold', 'italic', '|', 'preview'],
        'uploadImage' => false,
    ];

    /**
     * @return array<string, list<string>|\Illuminate\Contracts\Validation\Rule|string>
     */
    protected function rulesForStep(int $step): array
    {
        return match ($step) {
            1 => [
                'title' => 'required',
                'description' => 'required',
            ],
            2 => [
                'photo' => ['required', 'image', 'max:4096'],
                'hackaton_id' => ['required', 'exists:hackatons,id'],
            ],
            3 => [
                'socialLinks.*.name' => ['required', 'min:2'],
                'socialLinks.*.url' => ['required'],
            ],
            4 => [
                'roles' => ['required', 'array', 'min:1'],
                'roles.*.title' => ['required', 'min:3'],
                'roles.*.description' => ['required', 'max:255'],
                'roles.*.role' => ['required', 'exists:roles,id'],
            ],
            default => [],
        };
    }

    /**
     * @return array<string, list<string>|\Illuminate\Contracts\Validation\Rule|string>
     */
    protected function allRules(): array
    {
        return array_merge(
            $this->rulesForStep(1),
            $this->rulesForStep(2),
            $this->rulesForStep(3),
            $this->rulesForStep(4)
        );
    }

    /**
     * @return array<string, string>
     */
    protected function validationMessages(): array
    {
        return [
            'title.required' => 'Заголовок должен быть заполнен',
            'description.required' => 'Описание должно быть заполнено',
            'photo.required' => 'Изображение обязательно',
            'photo.image' => 'Файл должен быть валидным изображением',
            'photo.max' => 'Размер изображения не может быть больше 4 Мбайт',
            'hackaton_id.required' => 'Выберите хакатон',
            'hackaton_id.exists' => 'Указанный хакатон не найден',
            'roles.required' => 'Добавьте хотя бы одну роль',
            'roles.min' => 'Добавьте хотя бы одну роль',
            'roles.*.title.required' => 'Название роли обязательно для заполнения',
            'roles.*.title.min' => 'Название роли должно содержать минимум 3 символа',
            'roles.*.description.required' => 'Описание роли обязательно',
            'roles.*.description.max' => 'Длина описания роли не может быть больше 255 символов',
            'roles.*.role.required' => 'Категория роли должна быть выбрана',
            'roles.*.role.exists' => 'ОШИБКА 19755. Напишите по этому поводу в техподдержку',
            'socialLinks.*.name.required' => 'Имя обязательно',
            'socialLinks.*.name.min' => 'Длина имени должна быть не менее 2 символов',
            'socialLinks.*.url.required' => 'Ссылка обязательна',
        ];
    }

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

    public function addSocialLink(): void
    {
        $this->socialLinks[] = [
            'id' => uniqid(),
            'name' => '',
            'url' => '',
        ];
    }

    public function addSocialPreset(string $key): void
    {
        $presets = [
            'telegram' => ['name' => 'Telegram', 'url' => 'https://t.me/'],
            'vk' => ['name' => 'ВКонтакте', 'url' => 'https://vk.com/'],
            'github' => ['name' => 'GitHub', 'url' => 'https://github.com/'],
            'discord' => ['name' => 'Discord', 'url' => 'https://discord.gg/'],
            'youtube' => ['name' => 'YouTube', 'url' => 'https://youtube.com/'],
            'linkedin' => ['name' => 'LinkedIn', 'url' => 'https://linkedin.com/in/'],
        ];
        if (! isset($presets[$key])) {
            return;
        }
        $this->socialLinks[] = [
            'id' => uniqid(),
            'name' => $presets[$key]['name'],
            'url' => $presets[$key]['url'],
        ];
    }

    public function removeSocialLink($index): void
    {
        unset($this->socialLinks[$index]);
        $this->socialLinks = array_values($this->socialLinks);
    }

    public function removePhoto(): void
    {
        $this->photo = null;
    }

    public function fillRoleTitle(int $index, string $title): void
    {
        if (! isset($this->roles[$index])) {
            return;
        }
        $this->roles[$index]['title'] = $title;
    }

    /**
     * Iconify icon id for social link row (brand icons via simple-icons).
     *
     * @param  array{name?: string, url?: string}  $link
     */
    public function socialLinkIcon(array $link): string
    {
        $url = mb_strtolower((string) ($link['url'] ?? ''));
        $name = mb_strtolower((string) ($link['name'] ?? ''));

        $matches = static function (string $blob, array $needles): bool {
            foreach ($needles as $n) {
                if ($n !== '' && str_contains($blob, $n)) {
                    return true;
                }
            }

            return false;
        };

        $blob = $url.' '.$name;

        if ($matches($blob, ['t.me/', 'telegram.me/', 'telegram.org', 'телеграм', 'telegram'])) {
            return 'simple-icons:telegram';
        }
        if ($matches($blob, ['vk.com', 'vk.ru', 'вконтакте', 'vkontakte'])) {
            return 'simple-icons:vk';
        }
        if ($matches($blob, ['github.com', 'гитхаб', 'github'])) {
            return 'simple-icons:github';
        }
        if ($matches($blob, ['discord.gg', 'discord.com', 'discordapp.com', 'дискорд', 'discord'])) {
            return 'simple-icons:discord';
        }
        if ($matches($blob, ['youtube.com', 'youtu.be', 'youtube'])) {
            return 'simple-icons:youtube';
        }
        if ($matches($blob, ['twitch.tv', 'twitch'])) {
            return 'simple-icons:twitch';
        }
        if ($matches($blob, ['linkedin.com', 'linkedin'])) {
            return 'simple-icons:linkedin';
        }
        if ($matches($blob, ['twitter.com', 'x.com', 'твиттер', 'twitter'])) {
            return 'simple-icons:x';
        }
        if ($matches($blob, ['instagram.com', 'инстаграм', 'instagram'])) {
            return 'simple-icons:instagram';
        }
        if ($matches($blob, ['slack.com', 'slack'])) {
            return 'simple-icons:slack';
        }
        if (str_contains($url, 'mailto:')) {
            return 'heroicons:envelope';
        }

        return 'heroicons:link';
    }

    public function nextStep(): void
    {
        if ($this->step >= self::TOTAL_STEPS) {
            return;
        }

        $this->validate($this->rulesForStep($this->step), $this->validationMessages());

        $this->step++;
    }

    public function previousStep(): void
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    public function save(): void
    {
        if ($this->step < self::TOTAL_STEPS) {
            $this->nextStep();

            return;
        }

        $this->validate($this->allRules(), $this->validationMessages());

        $photo = $this->photo->storePublicly(path: 'team_photos', options: 'public');

        $team = Team::create([
            'user_id' => \Illuminate\Support\Facades\Auth::user()->id,
            'title' => $this->title,
            'description' => $this->description,
            'image_url' => $photo,
            'hackaton_id' => $this->hackaton_id,
            'is_public' => $this->is_public,
        ]);

        foreach ($this->socialLinks as $socialLink) {
            $team->socialLinks()->create([
                'name' => $socialLink['name'],
                'url' => $socialLink['url'],
            ]);
        }

        foreach ($this->roles as $role) {
            $newRole = $team->roles()->create([
                'title' => $role['title'],
                'description' => $role['description'],
                'team_id' => $team->id,
                'role_id' => $role['role'],
                'user_id' => null,
            ]);

            if (! empty($role['skills'])) {
                $newRole->skills()->sync($role['skills']);
            }
        }

        $this->redirect('/profile/teams');
    }

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

    /**
     * @return list<array{key: string, label: string, icon: string}>
     */
    #[Computed]
    public function socialPresets(): array
    {
        return [
            ['key' => 'telegram', 'label' => 'Telegram', 'icon' => 'simple-icons:telegram'],
            ['key' => 'vk', 'label' => 'VK', 'icon' => 'simple-icons:vk'],
            ['key' => 'github', 'label' => 'GitHub', 'icon' => 'simple-icons:github'],
            ['key' => 'discord', 'label' => 'Discord', 'icon' => 'simple-icons:discord'],
            ['key' => 'youtube', 'label' => 'YouTube', 'icon' => 'simple-icons:youtube'],
            ['key' => 'linkedin', 'label' => 'LinkedIn', 'icon' => 'simple-icons:linkedin'],
        ];
    }

    /**
     * @return list<string>
     */
    #[Computed]
    public function popularRoleTitles(): array
    {
        return [
            'Backend-разработчик',
            'Frontend-разработчик',
            'UI/UX дизайнер',
            'Project manager',
            'DevOps-инженер',
            'QA-инженер',
            'Системный аналитик',
        ];
    }

    #[Computed]
    public function currentStepMeta(): array
    {
        return match ($this->step) {
            1 => ['title' => 'Основное', 'subtitle' => 'Название и описание команды'],
            2 => ['title' => 'Обложка', 'subtitle' => 'Фото и хакатон'],
            3 => ['title' => 'Ссылки', 'subtitle' => 'Соцсети и контакты'],
            4 => ['title' => 'Роли', 'subtitle' => 'Вакансии в команде'],
            default => ['title' => '', 'subtitle' => ''],
        };
    }

    public function mount(): void
    {
        $this->addRole();
        $this->addSocialLink();
    }
};
?>

@php
    $wizardLabels = [1 => 'Основное', 2 => 'Обложка', 3 => 'Ссылки', 4 => 'Роли'];
    $progressPercent = (int) round((($step - 1) / 3) * 100);
@endphp

<div class="mx-auto w-full max-w-6xl space-y-6 pb-8">
    <div class="text-sm breadcrumbs">
        <ul>
            <li><a href="/">Главная</a></li>
            <li><a href="/profile/teams">Мои команды</a></li>
            <li class="opacity-70">Создание команды</li>
        </ul>
    </div>

    <x-mary-card
        class="card card-border border-base-200/80 bg-base-100 shadow-sm transition motion-safe:duration-200 hover:shadow-md"
    >
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div class="min-w-0 space-y-1">
                <h1 class="text-2xl font-semibold tracking-tight sm:text-3xl">Создание команды</h1>
                <p class="text-sm leading-relaxed text-base-content/60 sm:text-base">
                    Заполните профиль команды, добавьте роли и ссылки на ваши ресурсы — шаг за шагом.
                </p>
            </div>
            <div
                class="flex shrink-0 flex-col items-stretch gap-2 rounded-2xl border border-base-200 bg-base-200/40 px-4 py-3 sm:items-end sm:text-right"
            >
                <span class="text-xs font-semibold uppercase tracking-wide text-base-content/50">Прогресс</span>
                <span class="text-sm font-semibold text-base-content">
                    Шаг {{ $step }} из {{ \count($wizardLabels) }}
                    <span class="text-base-content/50">—</span>
                    {{ $this->currentStepMeta['title'] }}
                </span>
                <span class="text-xs text-base-content/55">{{ $this->currentStepMeta['subtitle'] }}</span>
            </div>
        </div>
    </x-mary-card>

    <x-mary-card
        class="card card-border w-full justify-self-center border-base-200/80 bg-base-100 shadow-sm transition motion-safe:duration-200 hover:shadow-md"
    >
        <x-maryform wire:submit.prevent="{{ $step < 4 ? 'nextStep' : 'save' }}" class="space-y-8">
            <div class="space-y-4">
                <progress
                    class="progress progress-primary h-2 w-full"
                    max="100"
                    value="{{ $progressPercent }}"
                ></progress>

                <div class="-mx-1 overflow-x-auto pb-1">
                    <div class="grid min-w-[18rem] grid-cols-4 gap-1 px-1 sm:gap-3">
                        @foreach ($wizardLabels as $n => $label)
                            <div class="flex min-w-0 flex-col items-center gap-2 text-center">
                                <div
                                    @class([
                                        'flex h-9 w-9 shrink-0 items-center justify-center rounded-full border-2 text-xs font-bold transition motion-safe:duration-200 sm:h-11 sm:w-11 sm:text-sm',
                                        'border-primary bg-primary text-primary-content shadow-md shadow-primary/25 ring-4 ring-primary/15 motion-safe:scale-105' => $step === $n,
                                        'border-primary/60 bg-primary/12 text-primary' => $step > $n,
                                        'border-base-300 bg-base-200/80 text-base-content/40' => $step < $n,
                                    ])
                                >
                                    @if ($step > $n)
                                        <x-mary-icon name="o-check" class="h-4 w-4 sm:h-5 sm:w-5" />
                                    @else
                                        {{ $n }}
                                    @endif
                                </div>
                                <span
                                    class="line-clamp-2 max-w-full text-[0.6rem] font-semibold uppercase leading-tight tracking-wide text-base-content/70 sm:text-xs"
                                >
                                    {{ $label }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div wire:key="wizard-step-{{ $step }}" class="space-y-6">
                @if ($step === 1)
                    <div
                        class="card rounded-2xl border border-base-200 bg-base-100/60 p-5 shadow-inner shadow-base-300/20 sm:p-7"
                    >
                        <div class="space-y-1 border-b border-base-200/80 pb-4">
                            <h2 class="text-xl font-semibold tracking-tight">Основная информация</h2>
                            <p class="text-sm text-base-content/65">Как вас увидят участники и организаторы.</p>
                        </div>

                        <div class="space-y-5 pt-6">
                            <x-mary-input
                                wire:model="title"
                                label="Название команды"
                                placeholder="Например, Team Phoenix"
                            />

                            <div class="space-y-2">
                                <div
                                    class="[&_.CodeMirror]:min-h-[12.5rem] [&_.EasyMDEContainer]:min-h-[12.5rem] [&_.editor-toolbar]:rounded-t-xl"
                                >
                                    <x-marymarkdown
                                        wire:model="description"
                                        label="Описание команды"
                                        :config="$this->config"
                                    />
                                </div>
                                <div
                                    class="rounded-2xl border border-base-200 bg-base-200/40 px-4 py-3 text-sm leading-relaxed text-base-content/70"
                                >
                                    <p class="mb-2 font-medium text-base-content/80">Пример структуры</p>
                                    <p class="mb-1 font-mono text-xs text-base-content/60">
                                        **Мы** делаем _MVP за 48 часов_.<br />
                                        - фокус на UX<br />
                                        - стек: Laravel + Livewire<br />
                                        Сайт: https://example.org
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if ($step === 2)
                    <div
                        class="card rounded-2xl border border-base-200 bg-base-100/60 p-5 shadow-inner shadow-base-300/20 sm:p-7"
                    >
                        <div class="space-y-1 border-b border-base-200/80 pb-4">
                            <h2 class="text-xl font-semibold tracking-tight">Обложка и хакатон</h2>
                            <p class="text-sm text-base-content/65">Яркая обложка помогает выделиться в каталоге команд.</p>
                        </div>

                        <div class="space-y-6 pt-6">
                            <x-maryfile
                                class="rounded-2xl border-2 border-dashed border-base-300 bg-base-200/30 p-4 transition motion-safe:duration-200 hover:border-primary/35 focus-within:border-primary/50 focus-within:ring-2 focus-within:ring-primary/20 sm:p-5"
                                label="Обложка команды"
                                wire:model="photo"
                                accept="image/png, image/jpeg, image/webp"
                                hint="PNG / JPEG / WebP, до 4 МБ. Перетащите файл или нажмите для выбора."
                            />

                            @if ($photo)
                                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                    <div
                                        class="group relative overflow-hidden rounded-2xl shadow-lg shadow-base-300/35 ring-1 ring-base-200 transition motion-safe:duration-300 motion-safe:hover:-translate-y-0.5 motion-safe:hover:shadow-xl"
                                    >
                                        <img
                                            class="aspect-video max-h-72 w-full object-cover sm:max-h-none"
                                            src="{{ $photo->temporaryUrl() }}"
                                            alt="Превью обложки команды"
                                        />
                                        <div
                                            class="pointer-events-none absolute inset-0 bg-linear-to-t from-base-300/50 to-transparent opacity-0 transition group-hover:opacity-100"
                                        ></div>
                                    </div>
                                    <x-mary-button
                                        type="button"
                                        wire:click="removePhoto"
                                        label="Убрать файл"
                                        class="btn-ghost btn-sm shrink-0 text-error/80 hover:bg-error/10 hover:text-error"
                                        icon="o-trash"
                                    />
                                </div>
                            @endif

                            <div class="divider my-0 text-xs text-base-content/50">Хакатон</div>

                            <x-maryselect label="Хакатон" wire:model="hackaton_id" :options="$this->hackatons" />
                        </div>
                    </div>
                @endif

                @if ($step === 3)
                    <div
                        class="card rounded-2xl border border-base-200 bg-base-100/60 p-5 shadow-inner shadow-base-300/20 sm:p-7"
                    >
                        <div class="flex flex-col gap-4 border-b border-base-200/80 pb-4 sm:flex-row sm:items-start sm:justify-between">
                            <div class="min-w-0 space-y-1">
                                <h2 class="text-xl font-semibold tracking-tight">Социальные ссылки</h2>
                                <p class="text-sm text-base-content/65">
                                    Добавьте контакты — с пресетами или вручную. Иконка подставится по ссылке.
                                </p>
                            </div>
                            <x-mary-button
                                type="button"
                                wire:click="addSocialLink"
                                label="Пустая ссылка"
                                class="btn-primary btn-sm shrink-0 gap-2 shadow-md shadow-primary/15 motion-safe:transition-transform motion-safe:active:scale-[0.98]"
                                icon="o-plus"
                            />
                        </div>

                        <div class="space-y-3 pt-5">
                            <p class="text-xs font-semibold uppercase tracking-wide text-base-content/45">Быстро добавить</p>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($this->socialPresets as $preset)
                                    <button
                                        type="button"
                                        wire:click="addSocialPreset('{{ $preset['key'] }}')"
                                        class="btn btn-outline btn-sm gap-2 border-base-300 bg-base-100/80 motion-safe:transition-all motion-safe:duration-200 motion-safe:hover:border-primary/40 motion-safe:hover:bg-primary/5 motion-safe:active:scale-[0.98]"
                                    >
                                        <x-app-icon icon="{{ $preset['icon'] }}" class="h-4 w-4" />
                                        {{ $preset['label'] }}
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        @if (empty($socialLinks))
                            <div
                                class="mt-6 rounded-2xl border border-dashed border-base-300 bg-base-200/25 px-4 py-6 text-center text-sm text-base-content/65"
                            >
                                Пока нет социальных ссылок — выберите пресет выше или добавьте пустую строку.
                            </div>
                        @endif

                        <div class="mt-6 space-y-4">
                            @foreach ($socialLinks as $index => $socialLink)
                                <x-mary-card
                                    class="border border-base-200 bg-base-200/50 shadow-sm transition motion-safe:duration-200 motion-safe:hover:border-primary/20 motion-safe:hover:shadow-md"
                                    wire:key="socialLink-{{ $socialLink['id'] }}"
                                >
                                    <div class="flex flex-wrap items-start gap-4">
                                        <div
                                            class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-base-100 shadow-inner ring-1 ring-base-200"
                                            title="{{ $socialLink['name'] ?: 'Ссылка' }}"
                                        >
                                            <x-app-icon
                                                icon="{{ $this->socialLinkIcon($socialLink) }}"
                                                class="h-6 w-6 text-base-content/80"
                                            />
                                        </div>
                                        <div class="min-w-0 flex-1 space-y-3">
                                            <div class="flex flex-wrap items-center justify-between gap-2">
                                                <x-marybadge
                                                    class="badge-ghost badge-sm font-medium"
                                                    value="Ссылка #{{ $index + 1 }}"
                                                />
                                                <x-mary-button
                                                    type="button"
                                                    class="btn-ghost btn-xs gap-1 text-base-content/50 hover:bg-error/10 hover:text-error"
                                                    wire:click="removeSocialLink({{ $index }})"
                                                    label="Удалить"
                                                    icon="o-x-mark"
                                                />
                                            </div>
                                            <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                                                <x-mary-input
                                                    wire:model.live.debounce.400ms="socialLinks.{{ $index }}.name"
                                                    label="Название"
                                                    placeholder="Например, Telegram"
                                                />
                                                <x-mary-input
                                                    wire:model.live.debounce.400ms="socialLinks.{{ $index }}.url"
                                                    label="Ссылка"
                                                    placeholder="https://..."
                                                />
                                            </div>
                                        </div>
                                    </div>
                                </x-mary-card>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($step === 4)
                    <div
                        class="card rounded-2xl border border-base-200 bg-base-100/60 p-5 shadow-inner shadow-base-300/20 sm:p-7"
                    >
                        <div class="flex flex-col gap-4 border-b border-base-200/80 pb-4 sm:flex-row sm:items-start sm:justify-between">
                            <div class="min-w-0 space-y-1">
                                <h2 class="text-xl font-semibold tracking-tight">Роли в команде</h2>
                                <p class="text-sm text-base-content/65">
                                    Добавьте вакансии и навыки — участники подадут заявки на выбранные роли.
                                </p>
                            </div>
                            <x-mary-button
                                type="button"
                                class="btn-primary btn-sm shrink-0 gap-2 shadow-md shadow-primary/15 motion-safe:transition-transform motion-safe:active:scale-[0.98]"
                                wire:click="addRole"
                                label="Добавить роль"
                                icon="o-plus"
                            />
                        </div>

                        @if (empty($roles))
                            <div
                                class="mt-6 rounded-2xl border border-dashed border-base-300 bg-base-200/25 px-4 py-6 text-center text-sm text-base-content/65"
                            >
                                Пока нет ролей. Добавьте хотя бы одну роль для набора участников.
                            </div>
                        @endif

                        <div class="mt-6 space-y-6">
                            @foreach ($roles as $index => $role)
                                <x-mary-card
                                    class="border border-base-200 bg-base-200/50 shadow-sm transition motion-safe:duration-200 motion-safe:hover:border-primary/20 motion-safe:hover:shadow-md"
                                    wire:key="role-{{ $role['id'] }}"
                                >
                                    <div class="flex flex-wrap items-center justify-between gap-2">
                                        <x-marybadge
                                            class="badge-ghost badge-sm font-medium"
                                            value="Роль #{{ $index + 1 }}"
                                        />
                                        <x-mary-button
                                            type="button"
                                            wire:click="removeRole({{ $index }})"
                                            label="Удалить"
                                            class="btn-ghost btn-xs gap-1 text-base-content/50 hover:bg-error/10 hover:text-error"
                                            icon="o-x-mark"
                                        />
                                    </div>

                                    <div class="mt-4 space-y-4 border-t border-base-200/70 pt-4">
                                        <div>
                                            <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-base-content/45">
                                                Быстрый выбор названия
                                            </p>
                                            <div class="flex flex-wrap gap-2">
                                                @foreach ($this->popularRoleTitles as $roleTitle)
                                                    <button
                                                        type="button"
                                                        wire:click="fillRoleTitle({{ $index }}, @js($roleTitle))"
                                                        class="btn btn-ghost btn-xs rounded-full border border-transparent px-3 font-normal text-base-content/80 motion-safe:transition-all motion-safe:hover:border-primary/25 motion-safe:hover:bg-primary/5 motion-safe:active:scale-[0.98]"
                                                    >
                                                        {{ $roleTitle }}
                                                    </button>
                                                @endforeach
                                            </div>
                                        </div>

                                        <x-mary-input wire:model="roles.{{ $index }}.title" label="Название роли" />
                                        <x-marymarkdown
                                            disk="public"
                                            folder="team_markdown"
                                            wire:model="roles.{{ $index }}.description"
                                            label="Описание роли"
                                            :config="$this->config"
                                        />
                                        <x-maryselect
                                            label="Категория роли"
                                            wire:model="roles.{{ $index }}.role"
                                            :options="$this->rolesData"
                                        />
                                        <div
                                            class="rounded-xl border border-base-200 bg-base-100 p-2 focus-within:ring-2 focus-within:ring-primary/25 motion-safe:transition-shadow"
                                        >
                                            <x-marychoices-offline
                                                label="Навыки роли"
                                                wire:model="roles.{{ $index }}.skills"
                                                :options="$this->skillsData"
                                                placeholder="Начните вводить название навыка…"
                                                hint="Поиск по списку навыков платформы. Можно выбрать несколько."
                                                clearable
                                                searchable
                                            />
                                        </div>
                                    </div>
                                </x-mary-card>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <x-slot:actions
                class="flex w-full flex-col gap-3 border-t border-base-200/80 pt-6 sm:flex-row sm:flex-wrap sm:items-center sm:justify-between"
            >
                <x-mary-button
                    link="/profile/teams"
                    label="Отмена"
                    class="btn-ghost order-2 min-h-11 sm:order-1"
                    icon="o-x-mark"
                />
                <div
                    class="flex w-full flex-col gap-2 sm:order-2 sm:ml-auto sm:w-auto sm:flex-row sm:gap-3"
                >
                    @if ($step > 1)
                        <x-mary-button
                            type="button"
                            label="Назад"
                            class="btn-outline order-2 min-h-11 motion-safe:transition-transform motion-safe:active:scale-[0.98]"
                            wire:click="previousStep"
                            icon="o-arrow-left"
                        />
                    @endif
                    @if ($step < 4)
                        <x-mary-button
                            type="submit"
                            label="Далее"
                            class="btn-primary order-1 min-h-11 shadow-lg shadow-primary/20 motion-safe:transition-transform motion-safe:active:scale-[0.98] sm:order-3 sm:min-w-40"
                            icon-right="o-arrow-right"
                        />
                    @else
                        <x-mary-button
                            type="submit"
                            label="Создать команду"
                            class="btn-primary order-1 min-h-11 shadow-lg shadow-primary/20 motion-safe:transition-transform motion-safe:active:scale-[0.98] sm:order-3 sm:min-w-40"
                            spinner="save"
                            wire:loading.attr="disabled"
                            icon="o-sparkles"
                        />
                    @endif
                </div>
            </x-slot:actions>
        </x-maryform>
    </x-mary-card>
</div>
