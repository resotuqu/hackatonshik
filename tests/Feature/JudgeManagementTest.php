<?php

declare(strict_types=1);

use App\Models\Hackaton;
use App\Models\HackatonJudge;
use App\Models\JudgeInvitation;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

use function Pest\Laravel\actingAs;

test('non organizer cannot invite a judge', function () {
    $organizer = User::factory()->partner()->create();
    $outsider = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create();

    actingAs($outsider)
        ->from(route('hackatons.show', $hackaton))
        ->post(route('hackatons.judges.invite', $hackaton), [
            'email' => 'second-judge-test@gmail.com',
        ])
        ->assertForbidden();

    expect(JudgeInvitation::query()->count())->toBe(0);
});

test('inviting same email twice while pending fails validation', function () {
    Mail::fake();

    $organizer = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create();

    actingAs($organizer)
        ->from(route('hackatons.show', $hackaton))
        ->post(route('hackatons.judges.invite', $hackaton), [
            'email' => 'pending-judge-test@gmail.com',
        ])
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    actingAs($organizer)
        ->from(route('hackatons.show', $hackaton))
        ->post(route('hackatons.judges.invite', $hackaton), [
            'email' => 'pending-judge-test@gmail.com',
        ])
        ->assertSessionHasErrors('email');
});

test('organizer can assign existing judge', function () {
    $organizer = User::factory()->partner()->create();
    $judge = User::factory()->judge()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create();

    actingAs($organizer)
        ->from(route('hackatons.show', $hackaton))
        ->post(route('hackatons.judges.assign', $hackaton), [
            'user_id' => $judge->id,
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    expect(HackatonJudge::query()
        ->where('hackaton_id', $hackaton->id)
        ->where('user_id', $judge->id)
        ->exists())->toBeTrue();
});

test('cannot assign a non-judge user as judge', function () {
    $organizer = User::factory()->partner()->create();
    $regular = User::factory()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create();

    actingAs($organizer)
        ->from(route('hackatons.show', $hackaton))
        ->post(route('hackatons.judges.assign', $hackaton), [
            'user_id' => $regular->id,
        ])
        ->assertSessionHasErrors('user_id');
});

test('non organizer cannot assign a judge', function () {
    $organizer = User::factory()->partner()->create();
    $outsider = User::factory()->partner()->create();
    $judge = User::factory()->judge()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create();

    actingAs($outsider)
        ->from(route('hackatons.show', $hackaton))
        ->post(route('hackatons.judges.assign', $hackaton), [
            'user_id' => $judge->id,
        ])
        ->assertForbidden();
});

test('organizer can unassign a judge', function () {
    $organizer = User::factory()->partner()->create();
    $judge = User::factory()->judge()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create();
    HackatonJudge::factory()->create([
        'hackaton_id' => $hackaton->id,
        'user_id' => $judge->id,
    ]);

    actingAs($organizer)
        ->from(route('hackatons.show', $hackaton))
        ->delete(route('hackatons.judges.unassign', [$hackaton, $judge]))
        ->assertRedirect()
        ->assertSessionHas('success');

    expect(HackatonJudge::query()
        ->where('hackaton_id', $hackaton->id)
        ->where('user_id', $judge->id)
        ->exists())->toBeFalse();
});

test('non organizer cannot unassign a judge', function () {
    $organizer = User::factory()->partner()->create();
    $outsider = User::factory()->partner()->create();
    $judge = User::factory()->judge()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create();
    HackatonJudge::factory()->create([
        'hackaton_id' => $hackaton->id,
        'user_id' => $judge->id,
    ]);

    actingAs($outsider)
        ->delete(route('hackatons.judges.unassign', [$hackaton, $judge]))
        ->assertForbidden();
});

test('show accept page returns 200 for pending invitation', function () {
    $organizer = User::factory()->partner()->create();
    $invitee = User::factory()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create();
    $invitation = JudgeInvitation::factory()->create([
        'hackaton_id' => $hackaton->id,
        'invited_by' => $organizer->id,
        'status' => JudgeInvitation::STATUS_PENDING,
    ]);

    actingAs($invitee)
        ->get(route('judges.invitations.accept', ['token' => $invitation->token]))
        ->assertOk();
});

test('show accept page returns 404 for accepted invitation', function () {
    $organizer = User::factory()->partner()->create();
    $invitee = User::factory()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create();
    $invitation = JudgeInvitation::factory()->create([
        'hackaton_id' => $hackaton->id,
        'invited_by' => $organizer->id,
        'status' => JudgeInvitation::STATUS_ACCEPTED,
    ]);

    actingAs($invitee)
        ->get(route('judges.invitations.accept', ['token' => $invitation->token]))
        ->assertNotFound();
});

test('user can accept invitation when authenticated email matches and is promoted to judge', function () {
    $organizer = User::factory()->partner()->create();
    $invitee = User::factory()->create([
        'email' => 'invited-judge@example.com',
        'role' => 'user',
    ]);
    $hackaton = Hackaton::factory()->for($organizer)->create();
    $invitation = JudgeInvitation::factory()->create([
        'hackaton_id' => $hackaton->id,
        'invited_by' => $organizer->id,
        'invited_email' => 'invited-judge@example.com',
        'status' => JudgeInvitation::STATUS_PENDING,
    ]);

    actingAs($invitee)
        ->post(route('judges.invitations.accept.store', ['token' => $invitation->token]))
        ->assertRedirect(route('hackatons.show', $hackaton));

    expect($invitee->fresh()->isJudge())->toBeTrue();
    expect($invitation->fresh()->status)->toBe(JudgeInvitation::STATUS_ACCEPTED);
    expect(HackatonJudge::query()
        ->where('hackaton_id', $hackaton->id)
        ->where('user_id', $invitee->id)
        ->exists())->toBeTrue();
});

test('accepting invitation with mismatched email returns 403', function () {
    $organizer = User::factory()->partner()->create();
    $imposter = User::factory()->create([
        'email' => 'imposter@example.com',
    ]);
    $hackaton = Hackaton::factory()->for($organizer)->create();
    $invitation = JudgeInvitation::factory()->create([
        'hackaton_id' => $hackaton->id,
        'invited_by' => $organizer->id,
        'invited_email' => 'real-judge@example.com',
        'status' => JudgeInvitation::STATUS_PENDING,
    ]);

    actingAs($imposter)
        ->post(route('judges.invitations.accept.store', ['token' => $invitation->token]))
        ->assertForbidden();

    expect($invitation->fresh()->status)->toBe(JudgeInvitation::STATUS_PENDING);
});
