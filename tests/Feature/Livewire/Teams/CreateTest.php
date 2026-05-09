<?php

use App\Livewire\Pages\Teams\Create;
use App\Models\Hackaton;
use App\Models\Role;
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
    $role = Role::factory()->create();

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
        ->set('roles.0.title', 'Developer')
        ->set('roles.0.description', 'Coding stuff')
        ->set('roles.0.role', $role->id)
        ->call('save')
        ->assertRedirect('/profile/teams');

    $this->assertDatabaseHas('teams', [
        'title' => 'Test Team',
        'user_id' => $user->id,
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
