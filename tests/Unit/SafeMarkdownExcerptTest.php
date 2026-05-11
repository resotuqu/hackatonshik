<?php

declare(strict_types=1);

use App\Support\SafeMarkdown;

test('toPlainExcerpt returns empty string for null and empty markdown', function () {
    expect(SafeMarkdown::toPlainExcerpt(null))->toBe('');
    expect(SafeMarkdown::toPlainExcerpt(''))->toBe('');
    expect(SafeMarkdown::toPlainExcerpt('   '))->toBe('');
});

test('toHtml renders GitHub-flavored pipe tables as html tables', function () {
    $html = SafeMarkdown::toHtml("| Колонка A | Колонка B |\n| --- | --- |\n| 1 | 2 |");

    expect($html)->toContain('<table>');
    expect($html)->toContain('<thead>');
    expect($html)->toContain('<tbody>');
    expect($html)->toContain('Колонка A');
});

test('toPlainExcerpt strips markdown syntax from rendered output', function () {
    $excerpt = SafeMarkdown::toPlainExcerpt('Intro **bold** tail');

    expect($excerpt)->toContain('bold');
    expect($excerpt)->not->toContain('**');
});

test('toPlainExcerpt truncates long plain text', function () {
    $long = str_repeat('a', 200);
    $excerpt = SafeMarkdown::toPlainExcerpt($long, 50);

    expect(mb_strlen($excerpt))->toBeLessThanOrEqual(51);
    expect($excerpt)->toEndWith('…');
});
