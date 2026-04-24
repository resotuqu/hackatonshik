<?php

use App\Models\Hackaton;
use App\Models\HackatonCase;
use App\Models\HackatonCaseSubmission;
use App\Models\JudgeInvitation;
use App\Models\Team;
use App\Models\TeamApplication;
use App\Models\TeamRole;
use App\Models\User;

test('organizer cannot create team application', function () {
    $teamOwner = User::factory()->create();
    $organizer = User::factory()->partner()->create();
    $team = Team::factory()->for($teamOwner)->create();
    $role = TeamRole::factory()->for($team)->create(['user_id' => null]);

    $response = $this
        ->actingAs($organizer)
        ->post(route('team.applications.store'), [
            'team_role_id' => $role->id,
        ]);

    $response->assertSessionHasErrors('team_role_id');
    expect(TeamApplication::query()->where('user_id', $organizer->id)->exists())->toBeFalse();
});

test('public profile route renders for visible profile', function () {
    $user = User::factory()->create([
        'nickname' => 'public_profile_user',
        'is_profile_public' => true,
    ]);

    $response = $this->get(route('profile.public.show', ['user' => $user->nickname]));

    $response->assertOk()->assertSee($user->fio);
});

test('public profile route returns 404 for hidden profile', function () {
    $user = User::factory()->create([
        'nickname' => 'hidden_profile_user',
        'is_profile_public' => false,
    ]);

    $response = $this->get(route('profile.public.show', ['user' => $user->nickname]));

    $response->assertNotFound();
});

test('organizer can assign registered judge to hackaton', function () {
    $organizer = User::factory()->partner()->create();
    $judge = User::factory()->judge()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create();

    $response = $this
        ->actingAs($organizer)
        ->post(route('hackatons.judges.assign', $hackaton), [
            'user_id' => $judge->id,
        ]);

    $response->assertRedirect();
    expect($hackaton->fresh()->judges()->where('users.id', $judge->id)->exists())->toBeTrue();
});

test('judge can accept invitation and gets assigned', function () {
    $organizer = User::factory()->partner()->create();
    $judge = User::factory()->create(['role' => 'user']);
    $hackaton = Hackaton::factory()->for($organizer)->create();

    $invitation = JudgeInvitation::query()->create([
        'hackaton_id' => $hackaton->id,
        'invited_email' => mb_strtolower($judge->email),
        'invited_by' => $organizer->id,
        'token' => 'test-judge-token',
        'status' => JudgeInvitation::STATUS_PENDING,
    ]);

    $response = $this
        ->actingAs($judge)
        ->get(route('judges.invitations.accept', $invitation->token));

    $response->assertRedirect(route('hackatons.show', $hackaton));
    expect($judge->fresh()->role)->toBe('judge');
    expect($hackaton->fresh()->judges()->where('users.id', $judge->id)->exists())->toBeTrue();
});

test('assigned judge can score submission', function () {
    $organizer = User::factory()->partner()->create();
    $judge = User::factory()->judge()->create();
    $participant = User::factory()->create();

    $hackaton = Hackaton::factory()->for($organizer)->create();
    $hackaton->judgeAssignments()->create([
        'user_id' => $judge->id,
        'assigned_by' => $organizer->id,
        'assigned_at' => now(),
    ]);

    $case = HackatonCase::factory()->for($hackaton)->create();
    $submission = HackatonCaseSubmission::factory()->for($case, 'case')->create([
        'user_id' => $participant->id,
        'submitted_by_user_id' => $participant->id,
        'team_id' => null,
    ]);

    $response = $this
        ->actingAs($judge)
        ->post(route('hackatons.scores.store', $hackaton), [
            'hackaton_case_submission_id' => $submission->id,
            'score' => 88,
            'max_score' => 100,
            'comment' => 'Отличная структура решения.',
        ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('hackaton_case_scores', [
        'hackaton_case_submission_id' => $submission->id,
        'reviewed_by' => $judge->id,
        'score' => 88,
    ]);
});
