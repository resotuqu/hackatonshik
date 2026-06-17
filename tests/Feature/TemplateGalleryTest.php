<?php

declare(strict_types=1);

use App\Livewire\Pages\Admin\Index as AdminIndex;
use App\Models\HackatonTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

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

test('admin can publish and unpublish templates', function () {
    $admin = User::factory()->admin()->create();
    $template = HackatonTemplate::factory()->create([
        'is_public' => false,
        'published_at' => null,
    ]);

    Livewire::actingAs($admin)
        ->test(AdminIndex::class)
        ->call('publishTemplate', $template->id);

    expect($template->fresh()->is_public)->toBeTrue();

    Livewire::actingAs($admin)
        ->test(AdminIndex::class)
        ->call('unpublishTemplate', $template->id);

    expect($template->fresh()->is_public)->toBeFalse();
});
