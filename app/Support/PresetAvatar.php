<?php

namespace App\Support;

use App\Models\AvatarPreset;
use App\Models\AvatarPresetPack;
use Illuminate\Support\Facades\Storage;

final class PresetAvatar
{
    public const DIRECTORY = 'preset_avatars';

    public const PACKS_PREFIX = 'preset_avatars/packs';

    public const LEGACY_GROUP_LABEL = 'Классические аватары';

    private const ALLOWED_EXTENSIONS = ['svg', 'png', 'jpg', 'jpeg', 'webp'];

    private const SAFE_BASENAME_PATTERN = '/^[a-zA-Z0-9._-]+$/';

    private const PACK_SLUG_PATTERN = '/^[a-z0-9]+(?:-[a-z0-9]+)*$/';

    public static function packSlugIsValid(string $slug): bool
    {
        return $slug !== '' && (bool) preg_match(self::PACK_SLUG_PATTERN, $slug);
    }

    /**
     * @return list<string> full storage paths on public disk
     */
    public static function legacyAllowedPaths(): array
    {
        if (! Storage::disk('public')->exists(self::DIRECTORY)) {
            return [];
        }

        $paths = Storage::disk('public')->files(self::DIRECTORY);
        $out = [];

        foreach ($paths as $path) {
            if (! str_starts_with($path, self::DIRECTORY.'/')) {
                continue;
            }
            $relativeAfterRoot = substr($path, strlen(self::DIRECTORY) + 1);
            if (str_contains($relativeAfterRoot, '/')) {
                continue;
            }
            $base = basename($path);
            if (! self::isSafeBasename($base)) {
                continue;
            }
            $ext = strtolower((string) pathinfo($base, PATHINFO_EXTENSION));
            if (! in_array($ext, self::ALLOWED_EXTENSIONS, true)) {
                continue;
            }
            $out[] = $path;
        }

        sort($out, SORT_STRING);

        return array_values(array_unique($out));
    }

    /**
     * @return list<string>
     */
    public static function packedPathsFromDatabase(): array
    {
        return AvatarPreset::query()
            ->whereHas('pack', fn ($q) => $q->where('is_active', true))
            ->orderBy('storage_path')
            ->pluck('storage_path')
            ->all();
    }

    /**
     * @return list<string>
     */
    public static function allowedPaths(): array
    {
        $paths = array_merge(self::packedPathsFromDatabase(), self::legacyAllowedPaths());
        $paths = array_values(array_unique($paths));
        sort($paths, SORT_STRING);

        return $paths;
    }

    public static function isAllowedPath(string $path): bool
    {
        if (! self::isSafeStoragePath($path)) {
            return false;
        }

        if (AvatarPreset::query()
            ->where('storage_path', $path)
            ->whereHas('pack', fn ($q) => $q->where('is_active', true))
            ->exists()) {
            return true;
        }

        return in_array($path, self::legacyAllowedPaths(), true);
    }

    public static function storagePathForDb(string $path): string
    {
        return $path;
    }

    /**
     * @return list<array{id: int|null, name: string, presets: list<array{path: string, url: string}>}>
     */
    public static function activePacksWithPresets(): array
    {
        $groups = [];

        $packs = AvatarPresetPack::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->with(['presets' => fn ($q) => $q->orderBy('sort_order')->orderBy('id')])
            ->get();

        foreach ($packs as $pack) {
            $presets = [];
            foreach (AvatarPreset::query()->where('avatar_preset_pack_id', $pack->id)->get() as $preset) {
                $presets[] = [
                    'path' => $preset->storage_path,
                    'url' => asset('storage/'.$preset->storage_path),
                ];
            }
            if ($presets !== []) {
                $groups[] = [
                    'id' => (int) $pack->id,
                    'name' => $pack->name,
                    'presets' => $presets,
                ];
            }
        }

        $legacyItems = [];
        foreach (self::legacyAllowedPaths() as $path) {
            $legacyItems[] = [
                'path' => $path,
                'url' => asset('storage/'.$path),
            ];
        }
        if ($legacyItems !== []) {
            $groups[] = [
                'id' => null,
                'name' => self::LEGACY_GROUP_LABEL,
                'presets' => $legacyItems,
            ];
        }

        return $groups;
    }

    public static function packStorageDirectory(string $slug): string
    {
        return self::PACKS_PREFIX.'/'.$slug;
    }

    public static function isPathUnderPack(string $path, string $slug): bool
    {
        $prefix = self::packStorageDirectory($slug).'/';

        return str_starts_with($path, $prefix);
    }

    private static function isSafeStoragePath(string $path): bool
    {
        if ($path === '' || str_contains($path, '..') || str_contains($path, '\\')) {
            return false;
        }

        if (! str_starts_with($path, self::DIRECTORY.'/')) {
            return false;
        }

        if (str_starts_with($path, self::PACKS_PREFIX.'/')) {
            $rest = substr($path, strlen(self::PACKS_PREFIX) + 1);
            $segments = explode('/', $rest);
            if (count($segments) < 2) {
                return false;
            }
            $packSlug = $segments[0];
            $file = $segments[count($segments) - 1];
            if (count($segments) !== 2) {
                return false;
            }

            return self::packSlugIsValid($packSlug) && self::isSafeBasename($file);
        }

        $relativeAfterRoot = substr($path, strlen(self::DIRECTORY) + 1);

        return ! str_contains($relativeAfterRoot, '/') && self::isSafeBasename($relativeAfterRoot);
    }

    private static function isSafeBasename(string $basename): bool
    {
        if ($basename === '' || str_contains($basename, '..') || str_contains($basename, '/')) {
            return false;
        }

        return (bool) preg_match(self::SAFE_BASENAME_PATTERN, $basename);
    }
}
