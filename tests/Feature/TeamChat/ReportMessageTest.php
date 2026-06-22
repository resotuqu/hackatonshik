<?php

use App\Enums\ReportStatus;
use App\Livewire\TeamChat;
use App\Models\Report;
use App\Models\Team;
use App\Models\TeamMessage;
use App\Models\TeamRole;
use App\Models\User;
use Livewire\Livewire;

test('team member can report another user message', function () {
    $owner = User::factory()->create();
    $reporter = User::factory()->create();
    $team = Team::factory()->for($owner)->create();
    TeamRole::factory()->for($team)->create(['user_id' => $reporter->id]);
    $message = TeamMessage::factory()->create([
        'team_id' => $team->id,
        'user_id' => $owner->id,
        'content' => 'Bad message',
        'type' => 'text',
    ]);

    $this->actingAs($reporter);

    Livewire::test(TeamChat::class, ['team' => $team])
        ->call('reportMessage', $message->id)
        ->assertHasNoErrors();

    expect(Report::query()
        ->where('reportable_type', TeamMessage::class)
        ->where('reportable_id', $message->id)
        ->where('reporter_id', $reporter->id)
        ->where('status', ReportStatus::Pending)
        ->exists()
    )->toBeTrue();
});

test('user cannot report their own message', function () {
    $user = User::factory()->create();
    $team = Team::factory()->for($user)->create();
    $message = TeamMessage::factory()->create([
        'team_id' => $team->id,
        'user_id' => $user->id,
        'content' => 'My message',
        'type' => 'text',
    ]);

    $this->actingAs($user);

    Livewire::test(TeamChat::class, ['team' => $team])
        ->call('reportMessage', $message->id)
        ->assertHasNoErrors();

    expect(Report::query()
        ->where('reportable_type', TeamMessage::class)
        ->where('reportable_id', $message->id)
        ->exists()
    )->toBeFalse();
});

test('duplicate report by same user does not create two records', function () {
    $owner = User::factory()->create();
    $reporter = User::factory()->create();
    $team = Team::factory()->for($owner)->create();
    TeamRole::factory()->for($team)->create(['user_id' => $reporter->id]);
    $message = TeamMessage::factory()->create([
        'team_id' => $team->id,
        'user_id' => $owner->id,
        'content' => 'Bad message',
        'type' => 'text',
    ]);

    $this->actingAs($reporter);

    $component = Livewire::test(TeamChat::class, ['team' => $team]);
    $component->call('reportMessage', $message->id);
    $component->call('reportMessage', $message->id);

    expect(Report::query()
        ->where('reportable_type', TeamMessage::class)
        ->where('reportable_id', $message->id)
        ->where('reporter_id', $reporter->id)
        ->count()
    )->toBe(1);
});
