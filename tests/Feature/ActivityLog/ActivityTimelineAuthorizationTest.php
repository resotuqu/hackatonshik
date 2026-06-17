<?php

use App\Models\Hackaton;
use App\Models\Team;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

test('team captain can see activity timeline on team page', function () {
    $captain = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $captain->id, 'title' => 'Alpha']);

    actingAs($captain);

    $team->update(['title' => 'Beta']);

    get(route('teams.show', $team))
        ->assertOk()
        ->assertSee('История изменений')
        ->assertSee('название');
});

test('stranger cannot see team activity timeline', function () {
    $captain = User::factory()->create();
    $stranger = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $captain->id]);

    actingAs($stranger);

    get(route('teams.show', $team))
        ->assertOk()
        ->assertDontSee('История изменений');
});

test('organizer can see hackaton activity timeline on edit page', function () {
    $organizer = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->create(['user_id' => $organizer->id, 'title' => 'Draft']);

    actingAs($organizer);

    $hackaton->update(['title' => 'Published']);

    get(route('hackatons.edit', $hackaton))
        ->assertOk()
        ->assertSee('История изменений');
});

test('non organizer cannot see hackaton activity timeline on edit page', function () {
    $organizer = User::factory()->partner()->create();
    $otherOrganizer = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->create(['user_id' => $organizer->id]);

    actingAs($otherOrganizer)
        ->get(route('hackatons.edit', $hackaton))
        ->assertForbidden();
});
