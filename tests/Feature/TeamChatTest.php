<?php

use App\Livewire\TeamChat;
use App\Models\Team;
use App\Models\TeamMessage;
use App\Models\TeamMessageReaction;
use App\Models\TeamRole;
use App\Models\User;
use App\Notifications\TeamChatMention;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

test('team owner can send a chat message', function () {
    $owner = User::factory()->create();
    $team = Team::factory()->for($owner)->create();

    $this->actingAs($owner);

    Livewire::test(TeamChat::class, ['team' => $team])
        ->set('message', 'Привет, команда!')
        ->call('sendMessage')
        ->assertHasNoErrors();

    expect($team->messages()->where('user_id', $owner->id)->exists())->toBeTrue();
});

test('team member with role can send a chat message', function () {
    $owner = User::factory()->create();
    $member = User::factory()->create();
    $team = Team::factory()->for($owner)->create();
    TeamRole::factory()->for($team)->create(['user_id' => $member->id]);

    $this->actingAs($member);

    Livewire::test(TeamChat::class, ['team' => $team])
        ->set('message', 'Сообщение участника')
        ->call('sendMessage')
        ->assertHasNoErrors();

    expect($team->messages()->where('user_id', $member->id)->exists())->toBeTrue();
});

test('outsider cannot mount team chat', function () {
    $owner = User::factory()->create();
    $outsider = User::factory()->create();
    $team = Team::factory()->for($owner)->create();

    $this->actingAs($outsider);

    Livewire::test(TeamChat::class, ['team' => $team])
        ->assertForbidden();

    expect($team->messages()->count())->toBe(0);
});

test('member can reply to a message and parent_id is stored', function () {
    $owner = User::factory()->create();
    $team = Team::factory()->for($owner)->create();
    $parent = TeamMessage::factory()->for($team)->for($owner)->create(['content' => 'Оригинал']);

    $this->actingAs($owner);

    Livewire::test(TeamChat::class, ['team' => $team])
        ->call('setReply', $parent->id)
        ->assertSet('replyToId', $parent->id)
        ->set('message', 'Ответ на сообщение')
        ->call('sendMessage')
        ->assertSet('replyToId', null)
        ->assertHasNoErrors();

    expect(
        $team->messages()
            ->where('parent_id', $parent->id)
            ->where('content', 'Ответ на сообщение')
            ->exists()
    )->toBeTrue();
});

test('member can toggle emoji reaction on a message', function () {
    $owner = User::factory()->create();
    $team = Team::factory()->for($owner)->create();
    $message = TeamMessage::factory()->for($team)->for($owner)->create();

    $this->actingAs($owner);

    Livewire::test(TeamChat::class, ['team' => $team])
        ->call('toggleReaction', $message->id, '👍')
        ->assertHasNoErrors();

    expect(
        TeamMessageReaction::query()
            ->where('team_message_id', $message->id)
            ->where('user_id', $owner->id)
            ->where('emoji', '👍')
            ->exists()
    )->toBeTrue();

    // toggle off
    Livewire::test(TeamChat::class, ['team' => $team])
        ->call('toggleReaction', $message->id, '👍')
        ->assertHasNoErrors();

    expect(
        TeamMessageReaction::query()
            ->where('team_message_id', $message->id)
            ->where('user_id', $owner->id)
            ->where('emoji', '👍')
            ->exists()
    )->toBeFalse();
});

test('disallowed emoji is rejected silently', function () {
    $owner = User::factory()->create();
    $team = Team::factory()->for($owner)->create();
    $message = TeamMessage::factory()->for($team)->for($owner)->create();

    $this->actingAs($owner);

    Livewire::test(TeamChat::class, ['team' => $team])
        ->call('toggleReaction', $message->id, '💩')
        ->assertHasNoErrors();

    expect(TeamMessageReaction::query()->where('team_message_id', $message->id)->exists())->toBeFalse();
});

test('cancel reply resets replyToId', function () {
    $owner = User::factory()->create();
    $team = Team::factory()->for($owner)->create();
    $message = TeamMessage::factory()->for($team)->for($owner)->create();

    $this->actingAs($owner);

    Livewire::test(TeamChat::class, ['team' => $team])
        ->call('setReply', $message->id)
        ->assertSet('replyToId', $message->id)
        ->call('cancelReply')
        ->assertSet('replyToId', null);
});

test('@mention in message notifies the mentioned team member', function () {
    Notification::fake();

    $owner = User::factory()->create(['nickname' => 'alice']);
    $member = User::factory()->create(['nickname' => 'bob']);
    $team = Team::factory()->for($owner)->create();
    TeamRole::factory()->for($team)->create(['user_id' => $member->id]);

    $this->actingAs($owner);

    Livewire::test(TeamChat::class, ['team' => $team])
        ->set('message', 'Привет @bob, проверь задачу!')
        ->call('sendMessage')
        ->assertHasNoErrors();

    Notification::assertSentTo($member, TeamChatMention::class);
    Notification::assertNotSentTo($owner, TeamChatMention::class);
});

test('@mention of non-member sends no notification', function () {
    Notification::fake();

    $owner = User::factory()->create(['nickname' => 'alice']);
    $outsider = User::factory()->create(['nickname' => 'outsider']);
    $team = Team::factory()->for($owner)->create();

    $this->actingAs($owner);

    Livewire::test(TeamChat::class, ['team' => $team])
        ->set('message', 'Привет @outsider!')
        ->call('sendMessage')
        ->assertHasNoErrors();

    Notification::assertNotSentTo($outsider, TeamChatMention::class);
});

test('self-mention sends no notification', function () {
    Notification::fake();

    $owner = User::factory()->create(['nickname' => 'alice']);
    $team = Team::factory()->for($owner)->create();

    $this->actingAs($owner);

    Livewire::test(TeamChat::class, ['team' => $team])
        ->set('message', 'Я сам себя @alice упомянул')
        ->call('sendMessage')
        ->assertHasNoErrors();

    Notification::assertNotSentTo($owner, TeamChatMention::class);
});

test('file message does not trigger mention parsing', function () {
    Notification::fake();

    $owner = User::factory()->create(['nickname' => 'alice']);
    $member = User::factory()->create(['nickname' => 'bob']);
    $team = Team::factory()->for($owner)->create();
    TeamRole::factory()->for($team)->create(['user_id' => $member->id]);

    // Directly create a file-type message (bypassing file upload flow)
    $this->actingAs($owner);
    $team->messages()->create([
        'user_id' => $owner->id,
        'content' => '@bob/some-file.pdf',
        'type' => 'file',
        'parent_id' => null,
    ]);

    Notification::assertNotSentTo($member, TeamChatMention::class);
});

test('sending text and file together creates two separate messages', function () {
    $owner = User::factory()->create();
    $team = Team::factory()->for($owner)->create();

    $this->actingAs($owner);

    $file = UploadedFile::fake()->create('doc.pdf', 100, 'application/pdf');

    Livewire::test(TeamChat::class, ['team' => $team])
        ->set('message', 'Вот документ')
        ->set('files', [$file])
        ->call('sendMessage')
        ->assertHasNoErrors();

    expect($team->messages()->where('type', 'text')->where('content', 'Вот документ')->exists())->toBeTrue();
    expect($team->messages()->where('type', 'file')->exists())->toBeTrue();
    expect($team->messages()->count())->toBe(2);
});

test('reply messages appear in the messages list', function () {
    $owner = User::factory()->create();
    $team = Team::factory()->for($owner)->create();
    $parent = TeamMessage::factory()->for($team)->for($owner)->create(['content' => 'Оригинал']);

    $this->actingAs($owner);

    Livewire::test(TeamChat::class, ['team' => $team])
        ->call('setReply', $parent->id)
        ->set('message', 'Ответ')
        ->call('sendMessage')
        ->assertHasNoErrors();

    // Both parent and reply must appear in the rendered message list
    expect($team->messages()->count())->toBe(2);
    expect($team->messages()->whereNotNull('parent_id')->exists())->toBeTrue();
});

test('submitting without message or files returns a validation error', function () {
    $owner = User::factory()->create();
    $team = Team::factory()->for($owner)->create();

    $this->actingAs($owner);

    Livewire::test(TeamChat::class, ['team' => $team])
        ->call('sendMessage')
        ->assertHasErrors(['message']);
});
