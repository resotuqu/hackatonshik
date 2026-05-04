<?php

declare(strict_types=1);

use App\Models\Hackaton;
use App\Models\Team;
use App\Models\User;
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
    expect($response->json('per_page'))->toBe(60);
});

test('safe markdown strips raw html', function () {
    $html = \App\Support\SafeMarkdown::toHtml('Hello <script>alert(1)</script> **world**');

    expect($html)->not->toContain('<script>');
    expect($html)->toContain('world');
});
