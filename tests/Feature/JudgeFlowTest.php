<?php

use App\Mail\JudgeInvitationMail;
use App\Models\Hackaton;
use App\Models\HackatonCase;
use App\Models\HackatonCaseSubmission;
use App\Models\JudgeInvitation;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

test('organizer can invite a judge by email', function () {
    $organizer = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->create(['user_id' => $organizer->id]);

    Mail::fake();

    $this->actingAs($organizer)
        ->post(route('hackatons.judges.invite', $hackaton), [
            'email' => 'judge@gmail.com',
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect();

    $this->assertDatabaseHas('judge_invitations', [
        'hackaton_id' => $hackaton->id,
        'invited_email' => 'judge@gmail.com',
        'status' => JudgeInvitation::STATUS_PENDING,
    ]);

    Mail::assertSent(JudgeInvitationMail::class, function ($mail) {
        return $mail->hasTo('judge@gmail.com');
    });
});

test('user can accept judge invitation and become a judge', function () {
    $organizer = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->create(['user_id' => $organizer->id]);
    $invitedUser = User::factory()->create(['email' => 'judge@gmail.com']);

    $invitation = JudgeInvitation::factory()->create([
        'hackaton_id' => $hackaton->id,
        'invited_email' => 'judge@gmail.com',
        'invited_by' => $organizer->id,
        'status' => JudgeInvitation::STATUS_PENDING,
        'token' => Str::random(64),
    ]);

    $this->actingAs($invitedUser)
        ->post(route('judges.invitations.accept.store', $invitation->token))
        ->assertRedirect(route('hackatons.show', $hackaton));

    $invitedUser->refresh();
    expect($invitedUser->isJudge())->toBeTrue();

    $this->assertDatabaseHas('hackaton_judges', [
        'hackaton_id' => $hackaton->id,
        'user_id' => $invitedUser->id,
    ]);

    expect($invitation->refresh()->status)->toBe(JudgeInvitation::STATUS_ACCEPTED);
});

test('judge can grade a team submission', function () {
    $organizer = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->create(['user_id' => $organizer->id]);
    $judge = User::factory()->create(['role' => 'judge']);
    $hackaton->judges()->attach($judge->id, ['assigned_by' => $organizer->id, 'assigned_at' => now()]);

    $team = Team::factory()->create(['hackaton_id' => $hackaton->id]);
    $case = HackatonCase::factory()->create(['hackaton_id' => $hackaton->id]);
    $submission = HackatonCaseSubmission::factory()->create([
        'team_id' => $team->id,
        'hackaton_case_id' => $case->id,
    ]);

    $this->actingAs($judge)
        ->post(route('hackatons.scores.store', $hackaton), [
            'hackaton_case_submission_id' => $submission->id,
            'score' => 85,
            'comment' => 'Great work!',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('hackaton_case_scores', [
        'hackaton_case_submission_id' => $submission->id,
        'reviewed_by' => $judge->id,
        'score' => 85,
        'general_comment' => 'Great work!',
        'is_final' => 1,
    ]);
});
