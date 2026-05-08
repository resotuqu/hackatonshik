<?php

declare(strict_types=1);

use App\Enums\ApplicationStatus;
use App\Models\Hackaton;
use App\Models\HackatonApplication;
use App\Models\HackatonCase;
use App\Models\HackatonCaseSubmission;
use App\Models\Team;
use App\Models\TeamRole;
use App\Models\User;
use App\Policies\HackatonCaseSubmissionPolicy;

it('always allows organizer to create a case submission', function () {
    $organizer = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create();
    $case = HackatonCase::factory()->create(['hackaton_id' => $hackaton->id]);

    expect((new HackatonCaseSubmissionPolicy)->create($organizer, $case))->toBeTrue();
});

it('allows team member to create a case submission only when application is accepted', function () {
    $organizer = User::factory()->partner()->create();
    $captain = User::factory()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create();
    $case = HackatonCase::factory()->create(['hackaton_id' => $hackaton->id]);
    $team = Team::factory()->for($captain)->for($hackaton)->create();

    $policy = new HackatonCaseSubmissionPolicy;

    expect($policy->create($captain, $case))->toBeFalse();

    HackatonApplication::factory()->create([
        'hackaton_id' => $hackaton->id,
        'team_id' => $team->id,
        'status' => ApplicationStatus::PENDING,
    ]);

    expect($policy->create($captain->fresh(), $case))->toBeFalse();

    HackatonApplication::query()
        ->where('team_id', $team->id)
        ->update(['status' => ApplicationStatus::ACCEPTED]);

    expect($policy->create($captain->fresh(), $case))->toBeTrue();
});

it('denies outsider from creating a case submission', function () {
    $organizer = User::factory()->partner()->create();
    $outsider = User::factory()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create();
    $case = HackatonCase::factory()->create(['hackaton_id' => $hackaton->id]);

    expect((new HackatonCaseSubmissionPolicy)->create($outsider, $case))->toBeFalse();
});

it('allows organizer, captain and team member to view a submission', function () {
    $organizer = User::factory()->partner()->create();
    $captain = User::factory()->create();
    $member = User::factory()->create();
    $outsider = User::factory()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create();
    $case = HackatonCase::factory()->create(['hackaton_id' => $hackaton->id]);
    $team = Team::factory()->for($captain)->for($hackaton)->create();
    TeamRole::factory()->for($team)->create(['user_id' => $member->id]);
    $submission = HackatonCaseSubmission::factory()->create([
        'hackaton_case_id' => $case->id,
        'team_id' => $team->id,
    ]);

    $policy = new HackatonCaseSubmissionPolicy;
    expect($policy->view($organizer, $submission))->toBeTrue();
    expect($policy->view($captain, $submission))->toBeTrue();
    expect($policy->view($member, $submission))->toBeTrue();
    expect($policy->view($outsider, $submission))->toBeFalse();
});

it('allows only the organizer to delete a case submission', function () {
    $organizer = User::factory()->partner()->create();
    $captain = User::factory()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create();
    $case = HackatonCase::factory()->create(['hackaton_id' => $hackaton->id]);
    $team = Team::factory()->for($captain)->for($hackaton)->create();
    $submission = HackatonCaseSubmission::factory()->create([
        'hackaton_case_id' => $case->id,
        'team_id' => $team->id,
    ]);

    $policy = new HackatonCaseSubmissionPolicy;
    expect($policy->delete($organizer, $submission))->toBeTrue();
    expect($policy->delete($captain, $submission))->toBeFalse();
});
