<?php

declare(strict_types=1);

use App\Models\Hackaton;
use App\Models\Team;
use App\Models\User;
use App\Support\SafeMarkdown;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('guest cannot view non-public hackaton', function () {
    $organizer = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create(['is_public' => false]);

    $this->get(route('hackatons.show', $hackaton))->assertForbidden();
});

test('guest cannot view non-public team', function () {
    $owner = User::factory()->create();
    $team = Team::factory()->for($owner)->create(['is_public' => false]);

    $this->get(route('teams.show', $team))->assertForbidden();
});

test('public catalog caps per_page at 60', function () {
    $response = $this->getJson('/api/v1/hackatons?per_page=500');

    $response->assertOk();
    expect($response->json('meta.per_page'))->toBe(60);
});

test('public catalog profiles omit internal user id', function () {
    User::factory()->create([
        'is_profile_public' => true,
        'nickname' => 'public_catalog_nick',
        'fio' => 'Иван Иванов',
    ]);

    $response = $this->getJson('/api/v1/profiles');

    $response->assertOk();
    $first = $response->json('data.0');
    expect($first)->toHaveKeys(['nickname', 'display_name', 'role', 'description']);
    expect($first)->not->toHaveKey('id');
});

test('safe markdown strips raw html', function () {
    $html = SafeMarkdown::toHtml('Hello <script>alert(1)</script> **world**');

    expect($html)->not->toContain('<script>');
    expect($html)->toContain('world');
});

test('hackaton public page does not echo raw script from description markdown', function () {
    $organizer = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create([
        'is_public' => true,
        'description' => 'Intro **bold** <script>hackatonDescXssProbeZ9q</script> tail',
    ]);

    $response = $this->get(route('hackatons.show', $hackaton));

    $response->assertSuccessful();
    $response->assertDontSee('<script>hackatonDescXssProbeZ9q', false);
    $response->assertSee('<strong>bold</strong>', false);
});
