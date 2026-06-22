<?php

declare(strict_types=1);

use App\Models\Hackaton;
use App\Models\Team;
use App\Models\TeamRole;
use App\Models\User;

test('admin can access admin panel but not participant routes', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get('/admin')
        ->assertOk();

    $this->actingAs($admin)
        ->get(route('teams.create'))
        ->assertForbidden();

    $this->actingAs($admin)
        ->get(route('participant.hackatons'))
        ->assertForbidden();
});

test('judge can access judge panel but not participant routes', function () {
    $judge = User::factory()->judge()->create();

    $this->actingAs($judge)
        ->get(route('judge.dashboard'))
        ->assertOk();

    $this->actingAs($judge)
        ->get(route('teams.create'))
        ->assertForbidden();

    $this->actingAs($judge)
        ->get(route('participant.hackatons'))
        ->assertForbidden();
});

test('organizer can access organizer panel but not participant or judge routes', function () {
    $organizer = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create();

    $hackaton->judgeAssignments()->create([
        'user_id' => $organizer->id,
        'assigned_by' => $organizer->id,
        'assigned_at' => now(),
    ]);

    $this->actingAs($organizer)
        ->get(route('organizer.dashboard'))
        ->assertOk();

    $this->actingAs($organizer)
        ->get(route('teams.create'))
        ->assertForbidden();

    $this->actingAs($organizer)
        ->get(route('participant.hackatons'))
        ->assertForbidden();

    $this->actingAs($organizer)
        ->get(route('judge.dashboard'))
        ->assertForbidden();
});

test('participant can access participant routes but not staff routes', function () {
    $participant = User::factory()->create();

    $this->actingAs($participant)
        ->get(route('participant.hackatons'))
        ->assertOk();

    $this->actingAs($participant)
        ->get(route('teams.create'))
        ->assertOk();

    $this->actingAs($participant)
        ->get(route('organizer.dashboard'))
        ->assertForbidden();

    $this->actingAs($participant)
        ->get(route('judge.dashboard'))
        ->assertForbidden();

    $this->actingAs($participant)
        ->get('/admin')
        ->assertForbidden();
});

test('judge cannot submit team application', function () {
    $teamOwner = User::factory()->create();
    $judge = User::factory()->judge()->create();
    $team = Team::factory()->for($teamOwner)->create();
    $role = TeamRole::factory()->for($team)->create(['user_id' => null]);

    $this->actingAs($judge)
        ->post(route('team.applications.store'), [
            'team_role_id' => $role->id,
        ])
        ->assertSessionHasErrors('team_role_id');
});

test('moderator can access admin panel but not user management', function () {
    $moderator = User::factory()->moderator()->create();

    $this->actingAs($moderator)
        ->get('/admin')
        ->assertOk();

    $this->actingAs($moderator)
        ->get(route('filament.admin.resources.users.index'))
        ->assertForbidden();
});

test('admin cannot submit team application', function () {
    $teamOwner = User::factory()->create();
    $admin = User::factory()->admin()->create();
    $team = Team::factory()->for($teamOwner)->create();
    $role = TeamRole::factory()->for($team)->create(['user_id' => null]);

    $this->actingAs($admin)
        ->post(route('team.applications.store'), [
            'team_role_id' => $role->id,
        ])
        ->assertSessionHasErrors('team_role_id');
});
