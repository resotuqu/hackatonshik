<?php

use App\Livewire\TeamChat;
use App\Models\Team;
use App\Models\TeamRole;
use App\Models\User;
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
