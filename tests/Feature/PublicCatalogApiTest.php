<?php

declare(strict_types=1);

use App\Models\Hackaton;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

uses(RefreshDatabase::class);

test('public catalog hackatons returns only public items in data array', function () {
    $organizer = User::factory()->partner()->create();
    Hackaton::factory()->for($organizer)->create(['is_public' => true, 'title' => 'Public Hack']);
    Hackaton::factory()->for($organizer)->create(['is_public' => false, 'title' => 'Secret Hack']);

    $response = $this->getJson('/api/v1/hackatons');

    $response->assertOk();
    $titles = collect($response->json('data'))->pluck('title')->all();
    expect($titles)->toContain('Public Hack')->not->toContain('Secret Hack');
});

test('public catalog hackatons upcoming filter excludes past starts', function () {
    $organizer = User::factory()->partner()->create();
    Hackaton::factory()->for($organizer)->create([
        'is_public' => true,
        'title' => 'Future',
        'start_at' => now()->addDays(5),
    ]);
    Hackaton::factory()->for($organizer)->create([
        'is_public' => true,
        'title' => 'Past',
        'start_at' => now()->subDays(10),
    ]);

    $response = $this->getJson('/api/v1/hackatons?upcoming=1');

    $response->assertOk();
    $titles = collect($response->json('data'))->pluck('title')->all();
    expect($titles)->toContain('Future')->not->toContain('Past');
});

test('public catalog teams returns only public teams', function () {
    $organizer = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create(['is_public' => true]);
    Team::factory()->for($organizer)->for($hackaton)->create(['is_public' => true, 'title' => 'Open Team']);
    Team::factory()->for($organizer)->for($hackaton)->create(['is_public' => false, 'title' => 'Closed Team']);

    $response = $this->getJson('/api/v1/teams');

    $response->assertOk();
    $titles = collect($response->json('data'))->pluck('title')->all();
    expect($titles)->toContain('Open Team')->not->toContain('Closed Team');
});

test('hackaton policy allows judge to view non-public hackaton', function () {
    $organizer = User::factory()->partner()->create();
    $judge = User::factory()->create(['role' => 'judge']);
    $hackaton = Hackaton::factory()->for($organizer)->create(['is_public' => false]);
    $hackaton->judges()->attach($judge->id, ['assigned_by' => $organizer->id, 'assigned_at' => now()]);

    $this->actingAs($judge)
        ->get(route('hackatons.show', $hackaton))
        ->assertSuccessful();
});

test('hackaton policy allows team member to view non-public hackaton', function () {
    $organizer = User::factory()->partner()->create();
    $member = User::factory()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create(['is_public' => false]);
    $team = Team::factory()->for($member)->for($hackaton)->create();

    $this->actingAs($member)
        ->get(route('hackatons.show', $hackaton))
        ->assertSuccessful();
});

test('saving public hackaton bumps catalog cache version key', function () {
    Cache::put('api:v1:catalog:hackatons:version', 1);

    $organizer = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create([
        'is_public' => true,
        'title' => 'Before',
    ]);

    expect(Cache::get('api:v1:catalog:hackatons:version'))->toBe(2);

    $hackaton->update(['title' => 'After']);

    expect(Cache::get('api:v1:catalog:hackatons:version'))->toBe(3);
});
