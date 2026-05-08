<?php

namespace App\Livewire\Pages\Teams;

use App\Models\Hackaton;
use App\Models\Role;
use App\Models\Skill;
use App\Models\Team;
use App\Models\TeamRole;
use App\Models\TeamSocialLink;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;

#[Layout('layouts::app', ['title' => 'Изменение команды'])]
class Edit extends Component
{
    use Toast, WithFileUploads;

    public Team $team;

    public string $teamInitials = '';

    // ----------------------------------------------------------------
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

    // ----------------------------------------------------------------
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

    // ----------------------------------------------------------------
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

    // ----------------------------------------------------------------

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
                $existingSocialLink = TeamSocialLink::query()
                    ->where('team_id', $this->team->id)
                    ->find($existingSocialLinkId);
                if ($existingSocialLink instanceof TeamSocialLink) {
                    $existingSocialLink->update([
                        'name' => $socialLink['name'],
                        'url' => $socialLink['url'],
                    ]);
                    $savedSocialLinkIds[] = $existingSocialLink->id;

                    continue;
                }
            }

            $newSocialLink = TeamSocialLink::query()->create([
                'team_id' => $this->team->id,
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

        $teamRolesById = TeamRole::query()
            ->where('team_id', $this->team->id)
            ->with('skills')
            ->get()
            ->keyBy('id');
        $savedRoleIds = [];
        foreach ($this->roles as $role) {
            $existingRoleId = $role['db_id'] ?? null;
            if (! empty($existingRoleId) && $teamRolesById->has($existingRoleId)) {
                $existingRole = $teamRolesById->get($existingRoleId);
                if (! $existingRole instanceof TeamRole) {
                    continue;
                }
                $existingRole->update([
                    'title' => $role['title'],
                    'description' => $role['description'],
                    'role_id' => $role['role'],
                ]);
                $existingRole->skills()->sync($role['skills'] ?? []);
                $savedRoleIds[] = $existingRole->id;

                continue;
            }

            $newRole = TeamRole::query()->create([
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

    // ----------------------------------------------------------------
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
        return Skill::all();
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

    // ----------------------------------------------------------------

    private function teamInitialsFromTitle(string $title): string
    {
        $title = trim($title);
        if ($title === '') {
            return 'T';
        }

        $parts = preg_split('/\s+/u', $title, -1, PREG_SPLIT_NO_EMPTY);
        if ($parts === false) {
            $parts = [];
        }
        $initials = '';
        foreach (array_slice($parts, 0, 2) as $part) {
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

        foreach (TeamSocialLink::query()->where('team_id', $team->id)->get(['id', 'name', 'url']) as $socialLink) {
            $this->socialLinks[] = [
                'id' => uniqid(),
                'db_id' => $socialLink->id,
                'name' => $socialLink->name,
                'url' => $socialLink->url,
            ];
        }
        foreach (TeamRole::query()->with('skills')->where('team_id', $team->id)->get() as $role) {
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

    public function render()
    {
        return view('pages.teams.edit');
    }
}
