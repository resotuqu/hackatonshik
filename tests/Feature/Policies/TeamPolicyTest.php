<?php

declare(strict_types=1);

use App\Models\Team;
use App\Models\TeamRole;
use App\Models\User;
use App\Policies\TeamPolicy;

it('allows guest to view a public team', function () {
    $team = Team::factory()->create(['is_public' => true]);

    expect((new TeamPolicy)->view(null, $team))->toBeTrue();
});

it('denies guest from viewing a private team', function () {
    $team = Team::factory()->create(['is_public' => false]);

    expect((new TeamPolicy)->view(null, $team))->toBeFalse();
});

it('allows captain to view their own private team', function () {
    $captain = User::factory()->create();
    $team = Team::factory()->for($captain)->create(['is_public' => false]);

    expect((new TeamPolicy)->view($captain, $team))->toBeTrue();
});

it('allows team role member to view a private team', function () {
    $captain = User::factory()->create();
    $member = User::factory()->create();
    $team = Team::factory()->for($captain)->create(['is_public' => false]);
    TeamRole::factory()->for($team)->create(['user_id' => $member->id]);

    expect((new TeamPolicy)->view($member, $team))->toBeTrue();
});

it('denies outsider from viewing a private team', function () {
    $captain = User::factory()->create();
    $outsider = User::factory()->create();
    $team = Team::factory()->for($captain)->create(['is_public' => false]);

    expect((new TeamPolicy)->view($outsider, $team))->toBeFalse();
});

it('allows only captain to update a team', function () {
    $captain = User::factory()->create();
    $outsider = User::factory()->create();
    $team = Team::factory()->for($captain)->create();

    expect((new TeamPolicy)->update($captain, $team))->toBeTrue();
    expect((new TeamPolicy)->update($outsider, $team))->toBeFalse();
});
