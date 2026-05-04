<?php

use App\Models\Hackaton;
use App\Models\Role;
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

    public string $teamInitials = '';

    //----------------------------------------------------------------
    #[Validate(['title' => 'required'], message: ['title.required' => 'Заголовок должен быть заполнен'])]
    public string $title = '';

    #[Validate(['description' => 'required'], message: ['description.required' => 'Описание должно быть заполнено'])]
    public string $description = '';

    #[Validate(['photo' => ['sometimes', 'max:4096']], message: [
        'photo.image' => 'Файл должен быть валидным изображением',
        'photo.max' => 'Размер изображения не может быть больше 4 Мбайт',
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

    public function fillRoleTitle(int $index, string $title): void
    {
        if (! isset($this->roles[$index])) {
            return;
        }
        $this->roles[$index]['title'] = $title;
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

    public function addSocialLink(): void
    {
        $this->socialLinks[] = [
            'id' => uniqid(),
            'db_id' => null,
            'name' => '',
            'url' => '',
        ];
    }

    public function removeSocialLink($index): void
    {
        unset($this->socialLinks[$index]);
        $this->socialLinks = array_values($this->socialLinks);
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
            'db_id' => null,
            'name' => $presets[$key]['name'],
            'url' => $presets[$key]['url'],
        ];
    }

    public function removePhoto(): void
    {
        $this->photo = null;
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

    //----------------------------------------------------------------

    public function save()
    {
        if (Auth::id() !== $this->team->user_id) {
            $this->redirect('/profile/teams');

            return;
        }

        $this->validate();

        $data = [
            'title' => $this->title,
            'description' => $this->description,
            'hackaton_id' => $this->hackaton_id,
            'is_public' => $this->is_public,
        ];

        if ($this->photo) {
            $path = $this->photo->storePublicly('team_photos', 'public');
            $data['image_url'] = $path;
        }

        $this->team->update($data);

        $savedSocialLinkIds = [];
        foreach ($this->socialLinks as $socialLink) {
            $existingSocialLinkId = $socialLink['db_id'] ?? null;
            if (! empty($existingSocialLinkId)) {
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
        if (! empty($savedSocialLinkIds)) {
            $socialLinksToDelete->whereNotIn('id', $savedSocialLinkIds);
        }
        $socialLinksToDelete->delete();

        $teamRolesById = $this->team->roles()->with('skills')->get()->keyBy('id');
        $savedRoleIds = [];
        foreach ($this->roles as $role) {
            $existingRoleId = $role['db_id'] ?? null;
            if (! empty($existingRoleId) && $teamRolesById->has($existingRoleId)) {
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
        if (! empty($savedRoleIds)) {
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
        foreach (Role::all() as $role) {
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

    //----------------------------------------------------------------

    private function teamInitialsFromTitle(string $title): string
    {
        $title = trim($title);
        if ($title === '') {
            return 'T';
        }

        $parts = preg_split('/\s+/u', $title, -1, PREG_SPLIT_NO_EMPTY);
        $initials = '';
        foreach (array_slice($parts ?? [], 0, 2) as $part) {
            $initials .= mb_strtoupper(mb_substr((string) $part, 0, 1));
        }

        if ($initials !== '') {
            return $initials;
        }

        return mb_strtoupper(mb_substr($title, 0, min(2, mb_strlen($title))));
    }

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
        $this->teamInitials = $this->teamInitialsFromTitle((string) $team->title);

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

<div class="mx-auto w-full max-w-6xl space-y-6 pb-8">
    @php
        $hasFilledRole = collect($roles)->contains(
            fn ($role) => filled($role['title'] ?? null)
                && filled($role['description'] ?? null)
                && filled($role['role'] ?? null)
        );
        $hasPhoto = ! empty($photo) || filled($team->image_url ?? null);

        $progressLabels = ['Название', 'Описание', 'Обложка', 'Хакатон', 'Роль'];
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

    <div
        class="card card-border relative overflow-hidden border-base-200/80 bg-linear-to-br from-primary/15 via-base-100 to-secondary/10 shadow-sm ring-1 ring-primary/20 motion-safe:transition motion-safe:duration-200 hover:shadow-md"
    >
        <div
            class="pointer-events-none absolute -right-24 -top-24 h-48 w-48 rounded-full bg-accent/10 blur-3xl motion-safe:animate-pulse motion-safe:[animation-duration:4s]"
            aria-hidden="true"
        ></div>
        <div class="relative flex flex-col gap-5 p-5 sm:flex-row sm:items-center sm:justify-between sm:p-7">
            <div class="flex min-w-0 flex-col gap-4 sm:flex-row sm:items-start sm:gap-5">
                <span
                    class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-primary/15 shadow-inner ring-1 ring-primary/35 motion-safe:transition motion-safe:hover:ring-primary/50"
                    aria-hidden="true"
                >
                    <x-app-icon icon="heroicons:pencil-square" class="h-6 w-6 text-primary" />
                </span>
                <div class="min-w-0 space-y-2">
                    <h1 class="font-display text-2xl font-bold tracking-tight text-base-content sm:text-3xl">
                        Редактирование команды
                    </h1>
                    <p class="max-w-2xl text-sm leading-relaxed text-base-content/65 sm:text-base">
                        Обновите профиль команды, социальные контакты и вакансии — чтобы участники увидели актуальную
                        картину. Занятые роли удалить нельзя.
                    </p>
                </div>
            </div>
            <div
                class="flex shrink-0 flex-col items-stretch gap-3 rounded-2xl border border-base-200/80 bg-base-100/70 px-5 py-4 shadow-inner backdrop-blur-sm sm:items-end sm:text-right"
            >
                <span class="text-[10px] font-semibold uppercase tracking-[0.22em] text-base-content/45">Команда</span>
                <div class="flex flex-wrap items-center justify-end gap-3">
                    <span
                        class="font-display text-3xl font-black tabular-nums tracking-tight text-secondary drop-shadow-[0_0_18px_rgba(163,230,53,0.45)] [html[data-theme=hackatonshik-light]_&]:text-primary [html[data-theme=hackatonshik-light]_&]:drop-shadow-[0_0_12px_rgba(81,112,255,0.35)]"
                        aria-hidden="true"
                    >
                        {{ $teamInitials }}
                    </span>
                    <span
                        class="max-w-[14rem] truncate rounded-xl border border-base-300/70 bg-base-200/50 px-3 py-1.5 text-sm font-semibold text-base-content"
                        title="{{ $team->title }}"
                    >
                        {{ $team->title }}
                    </span>
                </div>
                <p
                    class="max-w-full truncate rounded-full border border-cyan-400/35 bg-base-100/10 px-3 py-0.5 font-mono text-[9px] font-bold uppercase tracking-[0.28em] text-cyan-200 shadow-[0_0_16px_rgba(34,211,238,0.25)] backdrop-blur-[2px] sm:text-[10px] [html[data-theme=hackatonshik-light]_&]:border-primary/30 [html[data-theme=hackatonshik-light]_&]:text-primary [html[data-theme=hackatonshik-light]_&]:shadow-[0_0_14px_rgba(81,112,255,0.22)]"
                >
                    <span class="text-cyan-400/90 [html[data-theme=hackatonshik-light]_&]:text-primary/80">&gt;</span>
                    <span class="mx-0.5 tracking-[0.32em]">ХАКАТОНЩИК</span>
                    <span class="text-cyan-400/90 [html[data-theme=hackatonshik-light]_&]:text-primary/80">&lt;</span>
                </p>
            </div>
        </div>
    </div>

    <div
        class="card card-border border-base-200/80 bg-base-100 shadow-sm motion-safe:transition motion-safe:duration-200 hover:border-primary/20 hover:shadow-md"
    >
        <div class="space-y-5 p-5 sm:p-6">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div class="flex min-w-0 items-center gap-3">
                    <span
                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-primary/12 ring-1 ring-primary/25"
                        aria-hidden="true"
                    >
                        <x-app-icon icon="heroicons:chart-bar" class="h-5 w-5 text-primary" />
                    </span>
                    <div>
                        <p class="font-display text-base font-semibold tracking-tight text-base-content">Прогресс заполнения</p>
                        <p class="text-sm text-base-content/55">Закройте все пункты — так карточка команды станет сильнее в каталоге.</p>
                    </div>
                </div>
                <span
                    class="shrink-0 rounded-full border border-base-300/70 bg-base-200/50 px-3 py-1 text-sm font-semibold tabular-nums text-base-content"
                >
                    {{ $completedSteps }}/{{ $totalSteps }}
                </span>
            </div>

            <div class="-mx-1 overflow-x-auto pb-1">
                <div class="flex min-w-[20rem] gap-2 px-1 sm:grid sm:min-w-0 sm:grid-cols-5 sm:gap-3">
                    @foreach ($progressLabels as $i => $label)
                        @php $done = $progressSteps[$i] ?? false; @endphp
                        <div class="flex min-w-[5.5rem] flex-col items-center gap-2 text-center sm:min-w-0">
                            <div
                                @class([
                                    'flex h-9 w-9 shrink-0 items-center justify-center rounded-full border-2 text-xs font-bold motion-safe:transition motion-safe:duration-200 sm:h-10 sm:w-10 sm:text-sm',
                                    'border-primary bg-primary/15 text-primary shadow-md shadow-primary/20 ring-2 ring-primary/20 motion-safe:scale-[1.02]' => $done,
                                    'border-base-300/80 bg-base-200/60 text-base-content/35' => ! $done,
                                ])
                            >
                                @if ($done)
                                    <x-mary-icon name="o-check" class="h-4 w-4 sm:h-5 sm:w-5" />
                                @else
                                    {{ $i + 1 }}
                                @endif
                            </div>
                            <span
                                class="line-clamp-2 max-w-full text-[0.6rem] font-semibold uppercase leading-tight tracking-wide text-base-content/70 sm:text-[0.65rem]"
                            >
                                {{ $label }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="space-y-2">
                <div class="h-2 w-full overflow-hidden rounded-full bg-base-200/90 ring-1 ring-base-300/60">
                    <div
                        class="h-full rounded-full bg-linear-to-r from-primary via-accent to-secondary shadow-[0_0_18px_rgba(81,112,255,0.45)] motion-safe:transition-[width] motion-safe:duration-500 motion-safe:ease-out"
                        style="width: {{ $progressPercent }}%"
                        role="progressbar"
                        aria-valuenow="{{ $progressPercent }}"
                        aria-valuemin="0"
                        aria-valuemax="100"
                    ></div>
                </div>
                <p class="font-mono text-xs text-base-content/55">{{ $progressPercent }}% заполнено</p>
            </div>
        </div>
    </div>

    <div
        class="card card-border w-full justify-self-center border-base-200/80 bg-base-100 shadow-sm motion-safe:transition motion-safe:duration-200 hover:shadow-md"
    >
        <x-maryform wire:submit="save" class="space-y-8 p-5 sm:p-7">
            <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
                {{-- Основная информация --}}
                <section
                    class="card rounded-2xl border border-base-200 bg-base-100/60 p-5 shadow-inner shadow-base-300/20 motion-safe:animate-card-enter motion-safe:transition motion-safe:duration-200 motion-safe:hover:border-primary/25 motion-safe:hover:shadow-md sm:p-7"
                >
                    <div class="flex items-start gap-3 border-b border-base-200/80 pb-4">
                        <span
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-primary/15 ring-1 ring-primary/30"
                            aria-hidden="true"
                        >
                            <x-app-icon icon="heroicons:identification" class="h-5 w-5 text-primary" />
                        </span>
                        <div class="min-w-0 space-y-1">
                            <h2 class="text-xl font-semibold tracking-tight text-base-content">Основная информация</h2>
                            <p class="text-sm text-base-content/65">Как вас увидят участники и организаторы.</p>
                        </div>
                    </div>

                    <div class="space-y-5 pt-6">
                        <x-mary-input
                            wire:model="title"
                            label="Название команды"
                            placeholder="Например, Team Phoenix"
                        />

                        <div class="space-y-2">
                            <div
                                class="[&_.CodeMirror]:min-h-[14rem] [&_.EasyMDEContainer]:min-h-[14rem] [&_.editor-toolbar]:rounded-t-xl"
                            >
                                <x-marymarkdown wire:model="description" label="Описание команды" :config="$config" />
                            </div>
                            <div
                                class="rounded-2xl border border-base-200 bg-base-200/40 px-4 py-3 text-sm leading-relaxed text-base-content/70"
                            >
                                <p class="mb-2 font-medium text-base-content/80">Подсказка</p>
                                <p class="font-mono text-xs text-base-content/60">
                                    **Мы** делаем _MVP за 48 часов_.<br />
                                    - фокус на UX<br />
                                    - стек: Laravel + Livewire
                                </p>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- Обложка и хакатон --}}
                <section
                    class="card rounded-2xl border border-base-200 bg-base-100/60 p-5 shadow-inner shadow-base-300/20 motion-safe:animate-card-enter motion-safe:transition motion-safe:duration-200 motion-safe:hover:border-primary/25 motion-safe:hover:shadow-md sm:p-7"
                >
                    <div class="flex items-start gap-3 border-b border-base-200/80 pb-4">
                        <span
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-primary/15 ring-1 ring-primary/30"
                            aria-hidden="true"
                        >
                            <x-app-icon icon="heroicons:photo" class="h-5 w-5 text-primary" />
                        </span>
                        <div class="min-w-0 space-y-1">
                            <h2 class="text-xl font-semibold tracking-tight text-base-content">Обложка и хакатон</h2>
                            <p class="text-sm text-base-content/65">
                                Яркая обложка и правильный хакатон помогают выделиться в каталоге команд.
                            </p>
                        </div>
                    </div>

                    <div class="cover-upload-root space-y-6 pt-6">
                        @if ($photo)
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                <div
                                    class="group relative w-full overflow-hidden rounded-2xl ring-1 ring-secondary/35 shadow-[0_0_30px_rgba(163,230,53,0.22)] motion-safe:transition motion-safe:duration-300 motion-safe:hover:shadow-[0_0_40px_rgba(163,230,53,0.35)] sm:max-w-xl"
                                >
                                    <img
                                        class="aspect-video max-h-72 w-full object-cover"
                                        src="{{ $photo->temporaryUrl() }}"
                                        alt="Превью новой обложки команды"
                                    />
                                    <div
                                        class="pointer-events-none absolute inset-0 bg-linear-to-t from-base-300/75 via-transparent to-transparent"
                                    ></div>
                                    <span
                                        class="absolute left-3 top-3 badge badge-sm border-0 bg-secondary/95 text-[10px] font-bold uppercase tracking-widest text-secondary-content shadow-lg shadow-secondary/25"
                                    >
                                        Новая обложка
                                    </span>
                                </div>
                                <x-mary-button
                                    type="button"
                                    wire:click="removePhoto"
                                    label="Отменить замену"
                                    class="btn-ghost btn-sm shrink-0 gap-2 text-error/85 hover:bg-error/10 hover:text-error motion-safe:transition-transform motion-safe:active:scale-[0.98]"
                                    icon="o-arrow-uturn-left"
                                />
                            </div>
                        @elseif (! empty($team->image_url))
                            <div class="relative">
                                <div
                                    class="group relative overflow-hidden rounded-2xl ring-1 ring-primary/30 shadow-[0_0_30px_rgba(81,112,255,0.25)] motion-safe:transition motion-safe:duration-300 motion-safe:hover:shadow-[0_0_40px_rgba(163,230,53,0.3)]"
                                >
                                    <img
                                        class="aspect-video max-h-72 w-full object-cover"
                                        src="{{ asset('storage/' . $team->image_url) }}"
                                        alt="Текущая обложка команды"
                                    />
                                    <div
                                        class="pointer-events-none absolute inset-0 bg-linear-to-t from-base-300/70 via-transparent to-transparent"
                                    ></div>
                                    <span
                                        class="absolute left-3 top-3 badge badge-sm border-0 bg-base-100/85 text-xs font-medium text-base-content backdrop-blur-sm"
                                    >
                                        Текущая обложка
                                    </span>
                                </div>
                                <div class="mt-4 flex flex-wrap gap-2">
                                    <button
                                        type="button"
                                        class="btn btn-primary btn-sm gap-2 shadow-md shadow-primary/20 motion-safe:transition-transform motion-safe:active:scale-[0.98]"
                                        @click="$el.closest('.cover-upload-root').querySelector('input[type=file]')?.click()"
                                    >
                                        <x-app-icon icon="heroicons:arrow-path" class="h-4 w-4" />
                                        Заменить обложку
                                    </button>
                                </div>
                            </div>
                        @else
                            <div
                                class="flex flex-col items-center justify-center gap-2 rounded-2xl border-2 border-dashed border-primary/35 bg-base-200/30 px-6 py-10 text-center motion-safe:transition motion-safe:duration-200"
                            >
                                <x-app-icon
                                    icon="heroicons:cloud-arrow-up"
                                    class="h-10 w-10 text-primary/80 drop-shadow-[0_0_12px_rgba(81,112,255,0.35)]"
                                />
                                <p class="text-sm font-medium text-base-content/85">Перетащите файл сюда или выберите ниже</p>
                                <p class="text-xs text-base-content/55">PNG / JPEG / WebP, до 4 МБ</p>
                            </div>
                        @endif

                        <x-maryfile
                            class="rounded-2xl border-2 border-dashed border-primary/30 bg-base-200/30 p-4 motion-safe:transition motion-safe:duration-200 hover:border-primary/60 hover:bg-primary/5 focus-within:border-primary focus-within:ring-2 focus-within:ring-primary/30 sm:p-5"
                            label="Загрузка обложки"
                            wire:model="photo"
                            accept="image/png, image/jpeg, image/webp"
                            hint="PNG / JPEG / WebP, до 4 МБ. Перетащите файл или нажмите для выбора."
                        />

                        <div class="divider my-0 text-xs text-base-content/50">Хакатон</div>

                        <x-maryselect label="Хакатон" wire:model="hackaton_id" :options="$this->hackatons" />
                    </div>
                </section>
            </div>

            {{-- Социальные ссылки --}}
            <section
                class="card rounded-2xl border border-base-200 bg-base-100/60 p-5 shadow-inner shadow-base-300/20 motion-safe:animate-card-enter motion-safe:transition motion-safe:duration-200 motion-safe:hover:border-primary/25 motion-safe:hover:shadow-md sm:p-7"
            >
                <div class="flex flex-col gap-4 border-b border-base-200/80 pb-4 sm:flex-row sm:items-start sm:justify-between">
                    <div class="flex min-w-0 items-start gap-3">
                        <span
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-primary/15 ring-1 ring-primary/30"
                            aria-hidden="true"
                        >
                            <x-app-icon icon="heroicons:link" class="h-5 w-5 text-primary" />
                        </span>
                        <div class="min-w-0 space-y-1">
                            <h2 class="text-xl font-semibold tracking-tight text-base-content">Социальные ссылки</h2>
                            <p class="text-sm text-base-content/65">
                                Добавьте контакты — с пресетами или вручную. Иконка подставится по ссылке и названию.
                            </p>
                        </div>
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
                            class="motion-safe:animate-card-enter border border-base-200 bg-base-200/50 shadow-sm motion-safe:transition motion-safe:duration-200 motion-safe:hover:border-primary/20 motion-safe:hover:shadow-md"
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
                                            class="btn-ghost btn-xs gap-1 text-base-content/50 hover:bg-error/10 hover:text-error motion-safe:transition-colors"
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
            </section>

            {{-- Роли в команде --}}
            <section
                class="card rounded-2xl border border-base-200 bg-base-100/60 p-5 shadow-inner shadow-base-300/20 motion-safe:animate-card-enter motion-safe:transition motion-safe:duration-200 motion-safe:hover:border-primary/25 motion-safe:hover:shadow-md sm:p-7"
            >
                <div class="flex flex-col gap-4 border-b border-base-200/80 pb-4 sm:flex-row sm:items-start sm:justify-between">
                    <div class="flex min-w-0 items-start gap-3">
                        <span
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-primary/15 ring-1 ring-primary/30"
                            aria-hidden="true"
                        >
                            <x-app-icon icon="heroicons:user-group" class="h-5 w-5 text-primary" />
                        </span>
                        <div class="min-w-0 space-y-1">
                            <h2 class="text-xl font-semibold tracking-tight text-base-content">Роли в команде</h2>
                            <p class="text-sm text-base-content/65">
                                Управляйте вакансиями и навыками — участники подадут заявки на выбранные роли.
                            </p>
                        </div>
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
                            class="motion-safe:animate-card-enter border border-base-200 bg-base-200/50 shadow-sm motion-safe:transition motion-safe:duration-200 motion-safe:hover:border-primary/20 motion-safe:hover:shadow-md"
                            wire:key="role-{{ $role['id'] }}"
                        >
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <div class="flex flex-wrap items-center gap-2">
                                    <x-marybadge
                                        class="badge-ghost badge-sm font-medium"
                                        value="Роль #{{ $index + 1 }}"
                                    />
                                    @if ($role['is_occupied'] ?? false)
                                        <span
                                            class="badge badge-sm gap-1 border-0 bg-error/15 text-error ring-1 ring-error/25"
                                        >
                                            <x-app-icon icon="heroicons:lock-closed" class="h-3.5 w-3.5" />
                                            Роль занята
                                        </span>
                                    @endif
                                </div>

                                @if (! ($role['is_occupied'] ?? false))
                                    <x-mary-button
                                        type="button"
                                        wire:click="removeRole({{ $index }})"
                                        label="Удалить"
                                        class="btn-ghost btn-xs gap-1 text-base-content/50 hover:bg-error/10 hover:text-error motion-safe:transition-colors"
                                        icon="o-x-mark"
                                    />
                                @endif
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

                                <x-mary-input
                                    wire:model="roles.{{ $index }}.title"
                                    label="Название роли"
                                />
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
                                <div class="space-y-2">
                                    <p class="text-xs font-medium text-base-content/70">Подберите технологии и компетенции</p>
                                    <div
                                        class="team-edit-skills rounded-xl border border-base-200 bg-base-100 p-2 focus-within:ring-2 focus-within:ring-primary/25 motion-safe:transition-shadow [&_span.mary-choices-element]:rounded-full [&_span.mary-choices-element]:border [&_span.mary-choices-element]:border-primary/30 [&_span.mary-choices-element]:bg-primary/12 [&_span.mary-choices-element]:font-medium [&_span.mary-choices-element]:text-primary"
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
                            </div>
                        </x-mary-card>
                    @endforeach
                </div>
            </section>

            <x-slot:actions
                class="flex w-full flex-col gap-3 border-t border-base-200/80 pt-6 sm:flex-row sm:flex-wrap sm:items-center sm:justify-between"
            >
                <x-mary-button
                    link="/profile/teams"
                    label="Отмена"
                    class="btn-ghost order-2 min-h-11 w-full sm:order-1 sm:w-auto"
                    icon="o-x-mark"
                />
                <x-mary-button
                    type="submit"
                    label="Сохранить изменения"
                    class="btn-primary order-1 min-h-11 w-full shadow-lg shadow-primary/20 motion-safe:transition-transform motion-safe:active:scale-[0.98] sm:order-2 sm:ml-auto sm:min-w-48 sm:w-auto"
                    spinner="save"
                    wire:loading.attr="disabled"
                    icon="o-check"
                />
            </x-slot:actions>
        </x-maryform>
    </div>
</div>
