<?php

declare(strict_types=1);

use App\Models\Hackaton;
use App\Models\HackatonApplication;
use App\Models\Team;
use App\Models\User;
use App\Policies\HackatonApplicationPolicy;

it('allows any user to create hackaton application', function () {
    $user = User::factory()->create();

    expect((new HackatonApplicationPolicy)->create($user))->toBeTrue();
});

it('allows only the hackaton organizer to update an application', function () {
    $organizer = User::factory()->partner()->create();
    $captain = User::factory()->create();
    $outsider = User::factory()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create();
    $team = Team::factory()->for($captain)->for($hackaton)->create();
    $application = HackatonApplication::factory()->create([
        'hackaton_id' => $hackaton->id,
        'team_id' => $team->id,
    ]);

    $policy = new HackatonApplicationPolicy;
    expect($policy->update($organizer, $application))->toBeTrue();
    expect($policy->update($captain, $application))->toBeFalse();
    expect($policy->update($outsider, $application))->toBeFalse();
});

it('allows organizer or team captain to delete an application', function () {
    $organizer = User::factory()->partner()->create();
    $captain = User::factory()->create();
    $outsider = User::factory()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create();
    $team = Team::factory()->for($captain)->for($hackaton)->create();
    $application = HackatonApplication::factory()->create([
        'hackaton_id' => $hackaton->id,
        'team_id' => $team->id,
    ]);

    $policy = new HackatonApplicationPolicy;
    expect($policy->delete($organizer, $application))->toBeTrue();
    expect($policy->delete($captain, $application))->toBeTrue();
    expect($policy->delete($outsider, $application))->toBeFalse();
});
