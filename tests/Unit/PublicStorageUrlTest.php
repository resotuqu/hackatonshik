<?php

use App\Support\PublicStorageUrl;

test('returns null for empty path', function () {
    expect(PublicStorageUrl::for(null))->toBeNull();
    expect(PublicStorageUrl::for(''))->toBeNull();
});

test('allows external urls from app host only', function () {
    config(['app.url' => 'https://hackatonshik.ru']);

    expect(PublicStorageUrl::for('https://hackatonshik.ru/a.png'))
        ->toBe('https://hackatonshik.ru/a.png');
    expect(PublicStorageUrl::for('https://evil.example/a.png'))->toBeNull();
});

test('builds storage asset url for relative paths', function () {
    expect(PublicStorageUrl::for('hackaton_photos/cover.jpg'))
        ->toBe(asset('storage/hackaton_photos/cover.jpg'));
});
