<?php

declare(strict_types=1);

use App\Models\Hackaton;
use App\Models\HackatonApplication;
use App\Models\HackatonJudge;
use App\Models\Team;
use App\Models\TeamRole;
use App\Models\User;

use function Pest\Laravel\actingAs;

test('participant can open personal hub when they own a team', function () {
    $organizer = User::factory()->partner()->create();
    $captain = User::factory()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create();
    $team = Team::factory()->for($captain)->for($hackaton)->create();
    HackatonApplication::factory()->create([
        'team_id' => $team->id,
        'hackaton_id' => $hackaton->id,
    ]);

    actingAs($captain)
        ->get(route('participant.hackatons.hub', $hackaton))
        ->assertOk()
        ->assertSee('Личный кабинет участника', false);
});

test('team member can open personal hub for hackaton via team role', function () {
    $organizer = User::factory()->partner()->create();
    $captain = User::factory()->create();
    $member = User::factory()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create();
    $team = Team::factory()->for($captain)->for($hackaton)->create();
    TeamRole::factory()->for($team)->create(['user_id' => $member->id]);

    actingAs($member)
        ->get(route('participant.hackatons.hub', $hackaton))
        ->assertOk();
});

test('organizer without participating team gets 403 from hub', function () {
    $organizer = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create();

    actingAs($organizer)
        ->get(route('participant.hackatons.hub', $hackaton))
        ->assertForbidden();
});

test('judge without participating team gets 403 from hub', function () {
    $organizer = User::factory()->partner()->create();
    $judge = User::factory()->judge()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create();
    HackatonJudge::factory()->create([
        'hackaton_id' => $hackaton->id,
        'user_id' => $judge->id,
    ]);

    actingAs($judge)
        ->get(route('participant.hackatons.hub', $hackaton))
        ->assertForbidden();
});

test('outsider without team membership cannot open hub', function () {
    $organizer = User::factory()->partner()->create();
    $outsider = User::factory()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create();

    actingAs($outsider)
        ->get(route('participant.hackatons.hub', $hackaton))
        ->assertForbidden();
});
