<?php

use App\Models\Hackaton;
use App\Models\NewsPost;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\get;

uses(RefreshDatabase::class);

test('home page shows primary cta buttons', function () {
    $response = get('/');

    $response->assertOk();
    $response->assertSee('Найти команду');
    $response->assertSee('Хакатоны');
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
    $response->assertSee(config('app.rss_channel_title'), false);
    $response->assertDontSee('Herd News', false);
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

test('home page exposes seo meta tags', function () {
    $response = get('/');

    $response->assertOk();
    $response->assertSee('<meta name="description"', false);
    $response->assertSee('og:title', false);
    $response->assertSee('og:description', false);
    $response->assertSee('og:image', false);
    $response->assertSee('<meta name="twitter:card"', false);
    $response->assertSee('twitter:title', false);
    $response->assertSee('twitter:description', false);
    $response->assertSee('<link rel="canonical"', false);
    $response->assertSee('<meta name="robots"', false);
});

test('hackaton public page exposes seo meta tags', function () {
    $hackaton = Hackaton::factory()->create(['is_public' => true]);

    $response = get(route('hackatons.show', $hackaton));

    $response->assertOk();
    $response->assertSee(e($hackaton->title), false);
    $response->assertSee('<meta name="description"', false);
    $response->assertSee('og:title', false);
    $response->assertSee('og:description', false);
    $response->assertSee('og:image', false);
    $response->assertSee('<meta name="twitter:card"', false);
    $response->assertSee('twitter:title', false);
    $response->assertSee('twitter:description', false);
    $response->assertSee('<link rel="canonical"', false);
    $response->assertSee('<meta name="robots"', false);
});

test('team public page exposes seo meta tags', function () {
    $team = Team::factory()->create(['is_public' => true]);

    $response = get(route('teams.show', $team));

    $response->assertOk();
    $response->assertSee(e($team->title), false);
    $response->assertSee('<meta name="description"', false);
    $response->assertSee('og:title', false);
    $response->assertSee('og:description', false);
    $response->assertSee('<link rel="canonical"', false);
});

test('public profile page exposes seo meta tags', function () {
    $user = User::factory()->create([
        'is_profile_public' => true,
        'nickname' => 'seo_public_nick',
        'fio' => 'SEO Профиль',
    ]);

    $response = get(route('profile.public.show', ['user' => $user->nickname]));

    $response->assertOk();
    $response->assertSee(e($user->fio), false);
    $response->assertSee('<meta name="description"', false);
    $response->assertSee('og:title', false);
    $response->assertSee('og:description', false);
    $response->assertSee('og:image', false);
    $response->assertSee('<link rel="canonical"', false);
});
