<?php

declare(strict_types=1);

use App\Models\Hackaton;
use App\Models\HackatonJudge;
use App\Models\Team;
use App\Models\TeamRole;
use App\Models\User;
use App\Policies\HackatonPolicy;

it('allows guest to view a public hackaton', function () {
    $hackaton = Hackaton::factory()->create(['is_public' => true]);

    expect((new HackatonPolicy)->view(null, $hackaton))->toBeTrue();
});

it('denies guest from viewing a private hackaton', function () {
    $hackaton = Hackaton::factory()->create(['is_public' => false]);

    expect((new HackatonPolicy)->view(null, $hackaton))->toBeFalse();
});

it('allows organizer to view their own private hackaton', function () {
    $organizer = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create(['is_public' => false]);

    expect((new HackatonPolicy)->view($organizer, $hackaton))->toBeTrue();
});

it('allows assigned judge to view a private hackaton', function () {
    $organizer = User::factory()->partner()->create();
    $judge = User::factory()->judge()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create(['is_public' => false]);

    HackatonJudge::factory()->create([
        'hackaton_id' => $hackaton->id,
        'user_id' => $judge->id,
    ]);

    expect((new HackatonPolicy)->view($judge, $hackaton))->toBeTrue();
});

it('allows team captain to view a private hackaton', function () {
    $organizer = User::factory()->partner()->create();
    $captain = User::factory()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create(['is_public' => false]);
    Team::factory()->for($captain)->for($hackaton)->create();

    expect((new HackatonPolicy)->view($captain, $hackaton))->toBeTrue();
});

it('allows team role member to view a private hackaton', function () {
    $organizer = User::factory()->partner()->create();
    $captain = User::factory()->create();
    $member = User::factory()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create(['is_public' => false]);
    $team = Team::factory()->for($captain)->for($hackaton)->create();
    TeamRole::factory()->for($team)->create(['user_id' => $member->id]);

    expect((new HackatonPolicy)->view($member, $hackaton))->toBeTrue();
});

it('denies outsider from viewing a private hackaton', function () {
    $organizer = User::factory()->partner()->create();
    $outsider = User::factory()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create(['is_public' => false]);

    expect((new HackatonPolicy)->view($outsider, $hackaton))->toBeFalse();
});

it('allows only the organizer to update a hackaton', function () {
    $organizer = User::factory()->partner()->create();
    $other = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create();

    expect((new HackatonPolicy)->update($organizer, $hackaton))->toBeTrue();
    expect((new HackatonPolicy)->update($other, $hackaton))->toBeFalse();
});

it('allows owner and admin to delete a hackaton', function () {
    $organizer = User::factory()->partner()->create();
    $other = User::factory()->partner()->create();
    $admin = User::factory()->admin()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create();

    $policy = new HackatonPolicy;
    expect($policy->delete($organizer, $hackaton))->toBeTrue();
    expect($policy->delete($other, $hackaton))->toBeFalse();
    expect($policy->delete($admin, $hackaton))->toBeTrue();
});

it('viewOrganizationDashboard allows organizer and assigned judge', function () {
    $organizer = User::factory()->partner()->create();
    $judge = User::factory()->judge()->create();
    $outsider = User::factory()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create();

    HackatonJudge::factory()->create([
        'hackaton_id' => $hackaton->id,
        'user_id' => $judge->id,
    ]);

    $policy = new HackatonPolicy;
    expect($policy->viewOrganizationDashboard($organizer, $hackaton))->toBeTrue();
    expect($policy->viewOrganizationDashboard($judge, $hackaton))->toBeTrue();
    expect($policy->viewOrganizationDashboard($outsider, $hackaton))->toBeFalse();
});
