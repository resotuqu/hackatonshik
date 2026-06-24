<?php

declare(strict_types=1);

use App\Models\HackatonTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('guest can browse public template gallery', function () {
    HackatonTemplate::factory()->create([
        'title' => 'Public Template',
        'slug' => 'public-template',
        'is_public' => true,
        'published_at' => now()->subHour(),
    ]);

    $this->get(route('templates.index'))
        ->assertSuccessful()
        ->assertSee('Public Template');
});

test('private templates are hidden from gallery', function () {
    HackatonTemplate::factory()->create([
        'title' => 'Hidden Template',
        'slug' => 'hidden-template',
        'is_public' => false,
    ]);

    $this->get(route('templates.index'))
        ->assertSuccessful()
        ->assertDontSee('Hidden Template');

    $this->get(route('templates.show', 'hidden-template'))
        ->assertNotFound();
});

test('template gallery supports locale and level filters', function () {
    HackatonTemplate::factory()->create([
        'title' => 'RU Junior',
        'slug' => 'ru-junior',
        'locale' => 'ru',
        'level' => 'junior',
        'is_public' => true,
        'published_at' => now()->subHour(),
    ]);

    HackatonTemplate::factory()->create([
        'title' => 'EN Senior',
        'slug' => 'en-senior',
        'locale' => 'en',
        'level' => 'senior',
        'is_public' => true,
        'published_at' => now()->subHour(),
    ]);

    $this->get(route('templates.index', ['locale' => 'ru', 'level' => 'junior']))
        ->assertSuccessful()
        ->assertSee('RU Junior')
        ->assertDontSee('EN Senior');
});

test('create wizard preselects template from query string', function () {
    $organizer = User::factory()->partner()->create();
    $template = HackatonTemplate::factory()->create([
        'title' => 'Gallery Template',
        'slug' => 'gallery-template',
        'is_public' => true,
        'published_at' => now()->subHour(),
    ]);

    $this->actingAs($organizer)
        ->get(route('hackatons.create', ['template' => $template->slug]))
        ->assertSuccessful()
        ->assertSee('Gallery Template');
});

test('template publish state controls gallery visibility', function () {
    $template = HackatonTemplate::factory()->create([
        'title' => 'Toggle Template',
        'slug' => 'toggle-template',
        'is_public' => false,
        'published_at' => null,
        'is_active' => true,
    ]);

    $this->get(route('templates.index'))
        ->assertSuccessful()
        ->assertDontSee('Toggle Template');

    $template->update([
        'is_public' => true,
        'published_at' => now()->subMinute(),
    ]);

    $this->get(route('templates.index'))
        ->assertSuccessful()
        ->assertSee('Toggle Template');
});
