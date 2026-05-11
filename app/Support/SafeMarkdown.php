<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Support\Str;

/**
 * GitHub-flavored markdown with raw HTML stripped and unsafe links disabled.
 *
 * @see https://commonmark.thephpleague.com/2.4/security/
 */
final class SafeMarkdown
{
    /**
     * @var array<string, mixed>
     */
    private const OPTIONS = [
        'html_input' => 'strip',
        'allow_unsafe_links' => false,
    ];

    public static function toHtml(?string $markdown): string
    {
        $text = $markdown ?? '';

        if ($text === '') {
            return '';
        }

        return Str::markdown($text, self::OPTIONS);
    }

    /**
     * Plain-text excerpt for cards and meta (Markdown rendered then tags stripped).
     */
    public static function toPlainExcerpt(?string $markdown, int $maxChars = 160): string
    {
        $html = self::toHtml($markdown ?? '');
        if ($html === '') {
            return '';
        }

        $plain = strip_tags($html);
        $plain = preg_replace('/\s+/u', ' ', $plain) ?? '';
        $plain = trim($plain);

        if ($plain === '') {
            return '';
        }

        return Str::limit($plain, $maxChars, '…');
    }
}
