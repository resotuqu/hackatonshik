<?php

use App\Models\NewsPost;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\get;

uses(RefreshDatabase::class);

test('home page shows primary cta buttons', function () {
    $response = get('/');

    $response->assertOk();
    $response->assertSee('Найти команду');
    $response->assertSee('Создать хакатон');
});

test('news page renders published posts', function () {
    $post = NewsPost::factory()->create([
        'title' => 'Новый релиз платформы',
        'slug' => 'novyy-reliz-platformy',
        'is_published' => true,
        'published_at' => now()->subHour(),
    ]);

    $response = get('/news');

    $response->assertOk();
    $response->assertSee($post->title);
});

test('news rss endpoint returns xml feed', function () {
    NewsPost::factory()->create([
        'title' => 'RSS проверка',
        'slug' => 'rss-proverka',
        'is_published' => true,
        'published_at' => now()->subHour(),
    ]);

    $response = get('/news/rss');

    $response->assertOk();
    $response->assertHeader('content-type', 'application/rss+xml; charset=UTF-8');
    $response->assertSee('<rss version="2.0">', false);
});
