<?php

use App\Livewire\Pages\Teams\Edit;
use App\Models\Hackaton;
use App\Models\Role;
use App\Models\Team;
use App\Models\TeamRole;
use App\Models\User;
use Livewire\Livewire;

test('team owner can view team edit page', function () {
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->get(route('teams.edit', $team))
        ->assertOk()
        ->assertSee('Редактирование команды', false);
});

test('non-owner cannot view team edit page', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $owner->id]);

    $this->actingAs($other)
        ->get(route('teams.edit', $team))
        ->assertRedirect('/profile/teams');
});

test('team owner can update basic team info', function () {
    $user = User::factory()->create();
    $hackaton = Hackaton::factory()->create(['is_public' => true]);
    $team = Team::factory()->create([
        'user_id' => $user->id,
        'hackaton_id' => $hackaton->id,
        'title' => 'Old Title',
    ]);

    $this->actingAs($user);

    Livewire::test(Edit::class, ['team' => $team])
        ->set('title', 'New Epic Team')
        ->set('description', 'New description for the team')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect('/profile/teams');

    $team->refresh();
    expect($team->title)->toBe('New Epic Team')
        ->and($team->description)->toBe('New description for the team');
});

test('team owner can manage social links via edit page', function () {
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);
    $this->actingAs($user);

    Livewire::test(Edit::class, ['team' => $team])
        ->call('addSocialPreset', 'telegram')
        ->set('socialLinks.0.url', 'https://t.me/myteam')
        ->call('save')
        ->assertHasNoErrors();

    expect($team->socialLinks)->toHaveCount(1)
        ->and($team->socialLinks->first()->url)->toBe('https://t.me/myteam');

    Livewire::test(Edit::class, ['team' => $team])
        ->call('removeSocialLink', 0)
        ->call('save');

    $team->refresh();
    expect($team->socialLinks)->toHaveCount(0);
});

test('team owner can manage vacant roles via edit page', function () {
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);
    $roleCategory = Role::factory()->create();
    $this->actingAs($user);

    Livewire::test(Edit::class, ['team' => $team])
        ->call('addRole')
        ->set('roles.0.title', 'Backend dev')
        ->set('roles.0.description', 'Coding things')
        ->set('roles.0.role', $roleCategory->id)
        ->call('save')
        ->assertHasNoErrors();

    expect($team->roles()->whereNull('user_id')->get())->toHaveCount(1)
        ->and($team->roles()->whereNull('user_id')->first()->title)->toBe('Backend dev');

    // Test removing role
    Livewire::test(Edit::class, ['team' => $team])
        ->call('removeRole', 0)
        ->call('save');

    $team->refresh();
    expect($team->roles()->whereNull('user_id')->count())->toBe(0);
});

test('cannot remove an occupied role', function () {
    $user = User::factory()->create();
    $member = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);
    $roleCategory = Role::factory()->create();

    $occupiedRole = TeamRole::factory()->for($team)->create([
        'user_id' => $member->id,
        'role_id' => $roleCategory->id,
    ]);

    $this->actingAs($user);

    Livewire::test(Edit::class, ['team' => $team])
        ->call('removeRole', 0)
        ->assertHasNoErrors();

    $occupiedRole->refresh();
    expect($occupiedRole->exists)->toBeTrue();
});
