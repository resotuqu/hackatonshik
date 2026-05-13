<?php

namespace App\Livewire\Pages\Teams\Concerns;

use Livewire\Attributes\Computed;

trait HasSocialLinks
{
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
}
