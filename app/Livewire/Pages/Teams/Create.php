<?php

namespace App\Livewire\Pages\Teams;

use App\Models\Hackaton;
use App\Models\Role;
use App\Models\Skill;
use App\Models\Team;
use App\Models\TeamRole;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts::app', ['title' => 'Создание команды'])]
class Create extends Component
{
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
     * @return array<string, list<string>|Rule|string>
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
     * @return array<string, list<string>|Rule|string>
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
            'user_id' => Auth::id(),
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
            $newRole = TeamRole::query()->create([
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

    public function render()
    {
        return view('pages.teams.create');
    }
}
