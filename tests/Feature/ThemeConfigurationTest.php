<?php

declare(strict_types=1);

use App\Support\ThemeResolver;

use function Pest\Laravel\get;

test('theme config uses dim for dark and cmyk for light', function () {
    expect(config('theme.dark'))->toBe('dim')
        ->and(config('theme.light'))->toBe('cmyk');
});

test('theme resolver maps legacy cookie values to new themes', function () {
    expect(ThemeResolver::fromCookie('hackatonshik'))->toBe('dim')
        ->and(ThemeResolver::fromCookie('hackatonshik-light'))->toBe('cmyk')
        ->and(ThemeResolver::fromCookie('dim'))->toBe('dim')
        ->and(ThemeResolver::fromCookie('cmyk'))->toBe('cmyk')
        ->and(ThemeResolver::fromCookie(null))->toBe('dim')
        ->and(ThemeResolver::fromCookie('unknown'))->toBe('dim');
});

test('home page renders with default dim theme', function () {
    $response = get('/');

    $response->assertSuccessful();
    expect($response->getContent())->toContain('data-theme="dim"');
});

test('home page resolves legacy light theme cookie to cmyk', function () {
    $response = $this->withUnencryptedCookie('theme', 'hackatonshik-light')->get('/');

    $response->assertSuccessful();
    expect($response->getContent())->toContain('data-theme="cmyk"');
});

test('home page respects dim theme cookie', function () {
    $response = $this->withUnencryptedCookie('theme', 'dim')->get('/');

    $response->assertSuccessful();
    expect($response->getContent())->toContain('data-theme="dim"');
});

test('home page respects client-set unencrypted light theme cookie', function () {
    $response = $this->withUnencryptedCookie('theme', 'cmyk')->get('/');

    $response->assertSuccessful();
    expect($response->getContent())->toContain('data-theme="cmyk"');
});
