<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Support\Str;

final class InitialsGenerator
{
    /**
     * Generate initials from a given string (e.g., "Ivan Ivanov" -> "II").
     * Supports Cyrillic and multiple spaces.
     */
    public static function generate(?string $text, int $limit = 2): string
    {
        $text = trim((string) $text);

        if ($text === '') {
            return '?';
        }

        return Str::of($text)
            ->explode(' ')
            ->filter()
            ->take($limit)
            ->map(fn (string $word) => Str::upper(Str::substr($word, 0, 1)))
            ->implode('');
    }
}
