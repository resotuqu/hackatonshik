<?php

use App\Models\NewsPost;
use App\Models\Hackaton;
use App\Models\Team;
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

test('team page renders tab navigation and panels', function () {
    $team = Team::factory()->create();

    $response = get(route('teams.show', $team));

    $response->assertOk();
    $response->assertSee('data-tab-list="team"', false);
    $response->assertSee('data-tab-panel="team"', false);
    $response->assertSee('Обзор');
    $response->assertSee('Роли');
    $response->assertSee('Состав');
});

test('hackaton page renders tab navigation and panels', function () {
    $hackaton = Hackaton::factory()->create();

    $response = get(route('hackatons.show', $hackaton));

    $response->assertOk();
    $response->assertSee('data-tab-list="hackaton"', false);
    $response->assertSee('data-tab-panel="hackaton"', false);
    $response->assertSee('Описание');
    $response->assertSee('Кейсы');
    $response->assertSee('Участники');
});
