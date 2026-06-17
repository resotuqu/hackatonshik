<?php

use App\Support\PublicStorageUrl;

test('returns null for empty path', function () {
    expect(PublicStorageUrl::for(null))->toBeNull();
    expect(PublicStorageUrl::for(''))->toBeNull();
});

test('returns absolute urls unchanged', function () {
    expect(PublicStorageUrl::for('https://cdn.example.com/a.png'))
        ->toBe('https://cdn.example.com/a.png');
});

test('builds storage asset url for relative paths', function () {
    expect(PublicStorageUrl::for('hackaton_photos/cover.jpg'))
        ->toBe(asset('storage/hackaton_photos/cover.jpg'));
});
