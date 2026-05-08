<?php

declare(strict_types=1);

use App\Models\Team;
use App\Models\TeamRole;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

test('guest can open public team show page', function () {
    $owner = User::factory()->create();
    $team = Team::factory()->for($owner)->create([
        'is_public' => true,
        'title' => 'PublicTeamShowUnique',
    ]);

    get(route('teams.show', $team))
        ->assertOk()
        ->assertSee('PublicTeamShowUnique', false);
});

test('guest cannot open private team show page', function () {
    $owner = User::factory()->create();
    $team = Team::factory()->for($owner)->create([
        'is_public' => false,
    ]);

    get(route('teams.show', $team))
        ->assertForbidden();
});

test('owner can open own private team show page', function () {
    $owner = User::factory()->create();
    $team = Team::factory()->for($owner)->create([
        'is_public' => false,
        'title' => 'OwnerOnlyTeamUnique',
    ]);

    actingAs($owner)
        ->get(route('teams.show', $team))
        ->assertOk()
        ->assertSee('OwnerOnlyTeamUnique', false);
});

test('team role member can open private team show page', function () {
    $owner = User::factory()->create();
    $member = User::factory()->create();
    $team = Team::factory()->for($owner)->create([
        'is_public' => false,
    ]);

    TeamRole::factory()->for($team)->create(['user_id' => $member->id]);

    actingAs($member)
        ->get(route('teams.show', $team))
        ->assertOk();
});

test('outsider cannot open private team show page', function () {
    $owner = User::factory()->create();
    $outsider = User::factory()->create();
    $team = Team::factory()->for($owner)->create([
        'is_public' => false,
    ]);

    actingAs($outsider)
        ->get(route('teams.show', $team))
        ->assertForbidden();
});

test('team show page renders seo description meta from team description', function () {
    $owner = User::factory()->create();
    $team = Team::factory()->for($owner)->create([
        'is_public' => true,
        'description' => 'Команда мечтателей и инженеров, которая собирается на хакатоны.',
    ]);

    get(route('teams.show', $team))
        ->assertOk()
        ->assertSee('Команда мечтателей и инженеров', false);
});
