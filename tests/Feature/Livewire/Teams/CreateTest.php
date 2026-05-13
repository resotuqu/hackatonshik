<?php

use App\Livewire\Pages\Teams\Create;
use App\Models\Hackaton;
use App\Models\Role;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

beforeEach(function () {
    Storage::fake('public');
});

it('can create a team through the multi-step wizard', function () {
    $user = User::factory()->create();
    $hackaton = Hackaton::factory()->create(['is_public' => true]);
    $captainCategory = Role::factory()->create();
    $vacancyCategory = Role::factory()->create();

    $this->actingAs($user);

    Livewire::test(Create::class)
        // Step 1
        ->set('title', 'Test Team')
        ->set('description', 'A very cool team description')
        ->call('nextStep')
        ->assertSet('step', 2)

        // Step 2
        ->set('photo', UploadedFile::fake()->image('team.jpg'))
        ->set('hackaton_id', $hackaton->id)
        ->call('nextStep')
        ->assertSet('step', 3)

        // Step 3
        ->set('socialLinks.0.name', 'Telegram')
        ->set('socialLinks.0.url', 'https://t.me/test')
        ->call('nextStep')
        ->assertSet('step', 4)

        // Step 4
        ->set('captainRole.title', 'Team lead')
        ->set('captainRole.description', 'I lead the team and coordinate delivery')
        ->set('captainRole.role', $captainCategory->id)
        ->set('roles.0.title', 'Developer')
        ->set('roles.0.description', 'Coding stuff')
        ->set('roles.0.role', $vacancyCategory->id)
        ->call('save')
        ->assertRedirect('/profile/teams');

    $team = Team::query()
        ->where('title', 'Test Team')
        ->where('user_id', $user->id)
        ->firstOrFail();

    $this->assertDatabaseHas('team_roles', [
        'team_id' => $team->id,
        'user_id' => $user->id,
        'title' => 'Team lead',
        'role_id' => $captainCategory->id,
    ]);
    $this->assertDatabaseHas('team_roles', [
        'team_id' => $team->id,
        'user_id' => null,
        'title' => 'Developer',
        'role_id' => $vacancyCategory->id,
    ]);
});

it('validates required fields at each step', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Livewire::test(Create::class)
        ->set('title', '')
        ->call('nextStep')
        ->assertHasErrors(['title' => 'required'])
        ->assertSet('step', 1);
});

it('requires the captain to choose a role before creating a team', function () {
    $user = User::factory()->create();
    $hackaton = Hackaton::factory()->create(['is_public' => true]);
    $vacancyCategory = Role::factory()->create();

    $this->actingAs($user);

    Livewire::test(Create::class)
        ->set('title', 'Captainless Team')
        ->set('description', 'This team forgot to choose a captain role')
        ->call('nextStep')
        ->set('photo', UploadedFile::fake()->image('team.jpg'))
        ->set('hackaton_id', $hackaton->id)
        ->call('nextStep')
        ->set('socialLinks.0.name', 'Telegram')
        ->set('socialLinks.0.url', 'https://t.me/captainless')
        ->call('nextStep')
        ->set('roles.0.title', 'Backend dev')
        ->set('roles.0.description', 'Laravel and API work')
        ->set('roles.0.role', $vacancyCategory->id)
        ->call('save')
        ->assertHasErrors(['captainRole.title' => 'required', 'captainRole.role' => 'required']);

    $this->assertDatabaseMissing('teams', [
        'title' => 'Captainless Team',
    ]);
});
