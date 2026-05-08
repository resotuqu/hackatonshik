<?php

declare(strict_types=1);

use App\Models\Team;
use App\Models\TeamApplication;
use App\Models\TeamRole;
use App\Models\User;
use App\Policies\TeamApplicationPolicy;

it('denies organizer from creating a team application', function () {
    $organizer = User::factory()->partner()->create();
    $regular = User::factory()->create();

    $policy = new TeamApplicationPolicy;
    expect($policy->create($organizer))->toBeFalse();
    expect($policy->create($regular))->toBeTrue();
});

it('allows only the team captain to update a team application', function () {
    $captain = User::factory()->create();
    $applicant = User::factory()->create();
    $outsider = User::factory()->create();
    $team = Team::factory()->for($captain)->create();
    $teamRole = TeamRole::factory()->for($team)->create(['user_id' => null]);
    $application = TeamApplication::factory()->create([
        'team_role_id' => $teamRole->id,
        'user_id' => $applicant->id,
    ]);

    $policy = new TeamApplicationPolicy;
    expect($policy->update($captain, $application))->toBeTrue();
    expect($policy->update($applicant, $application))->toBeFalse();
    expect($policy->update($outsider, $application))->toBeFalse();
});

it('allows captain or applicant author to delete a team application', function () {
    $captain = User::factory()->create();
    $applicant = User::factory()->create();
    $outsider = User::factory()->create();
    $team = Team::factory()->for($captain)->create();
    $teamRole = TeamRole::factory()->for($team)->create(['user_id' => null]);
    $application = TeamApplication::factory()->create([
        'team_role_id' => $teamRole->id,
        'user_id' => $applicant->id,
    ]);

    $policy = new TeamApplicationPolicy;
    expect($policy->delete($captain, $application))->toBeTrue();
    expect($policy->delete($applicant, $application))->toBeTrue();
    expect($policy->delete($outsider, $application))->toBeFalse();
});
