<?php

use App\Livewire\Pages\Teams\Edit;
use App\Models\Role;
use App\Models\Team;
use App\Models\TeamRole;
use App\Models\User;
use Livewire\Livewire;

it('requires the captain role when editing a team', function () {
    $owner = User::factory()->create();
    $team = Team::factory()->withoutCaptainRole()->for($owner)->create();
    $captainCategory = Role::factory()->create();
    $captainRole = TeamRole::factory()->for($team)->create([
        'user_id' => $owner->id,
        'role_id' => $captainCategory->id,
        'title' => 'Captain',
        'description' => 'Current captain role',
    ]);

    $this->actingAs($owner);

    Livewire::test(Edit::class, ['team' => $team])
        ->set('captainRole.title', '')
        ->set('captainRole.role', '')
        ->call('save')
        ->assertHasErrors(['captainRole.title' => 'required', 'captainRole.role' => 'required']);

    $captainRole->refresh();

    expect($captainRole->user_id)->toBe($owner->id)
        ->and($captainRole->title)->toBe('Captain');
});

it('updates the existing captain role instead of creating a new one', function () {
    $owner = User::factory()->create();
    $team = Team::factory()->withoutCaptainRole()->for($owner)->create();
    $initialCategory = Role::factory()->create();
    $updatedCategory = Role::factory()->create();
    $captainRole = TeamRole::factory()->for($team)->create([
        'user_id' => $owner->id,
        'role_id' => $initialCategory->id,
        'title' => 'Captain',
        'description' => 'Current captain role',
    ]);

    $this->actingAs($owner);

    Livewire::test(Edit::class, ['team' => $team])
        ->set('captainRole.title', 'Product lead')
        ->set('captainRole.description', 'Owns product vision and team coordination')
        ->set('captainRole.role', $updatedCategory->id)
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect('/profile/teams');

    $captainRole->refresh();

    expect(TeamRole::query()->whereCaptain($team)->count())->toBe(1)
        ->and($captainRole->user_id)->toBe($owner->id)
        ->and($captainRole->title)->toBe('Product lead')
        ->and($captainRole->description)->toBe('Owns product vision and team coordination')
        ->and($captainRole->role_id)->toBe($updatedCategory->id);
});

it('keeps the captain role attached to the owner even when the client loses its db id', function () {
    $owner = User::factory()->create();
    $team = Team::factory()->withoutCaptainRole()->for($owner)->create();
    $captainCategory = Role::factory()->create();
    $captainRole = TeamRole::factory()->for($team)->create([
        'user_id' => $owner->id,
        'role_id' => $captainCategory->id,
        'title' => 'Captain',
        'description' => 'Current captain role',
    ]);

    $this->actingAs($owner);

    Livewire::test(Edit::class, ['team' => $team])
        ->set('captainRole.db_id', null)
        ->set('captainRole.title', 'Still captain')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect('/profile/teams');

    $captainRole->refresh();

    expect(TeamRole::query()->whereCaptain($team)->count())->toBe(1)
        ->and($captainRole->user_id)->toBe($owner->id)
        ->and($captainRole->title)->toBe('Still captain');
});
