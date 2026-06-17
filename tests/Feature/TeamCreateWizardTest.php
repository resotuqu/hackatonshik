<?php

use App\Livewire\Pages\Teams\Create as TeamsCreate;
use App\Models\Hackaton;
use App\Models\Role;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;

test('team create wizard completes all steps and redirects to profile teams', function () {
    $user = User::factory()->create();
    $hackaton = Hackaton::factory()->create(['is_public' => true]);
    $captainCategory = Role::factory()->create();
    $vacancyCategory = Role::factory()->create();
    $file = UploadedFile::fake()->image('team-cover.jpg', 800, 600);

    $teamTitle = 'Wizard Team '.uniqid();

    Livewire::actingAs($user)
        ->test(TeamsCreate::class)
        ->set('title', $teamTitle)
        ->set('description', 'Описание команды для теста.')
        ->call('nextStep')
        ->assertSet('step', 2)
        ->set('photo', $file)
        ->set('hackaton_id', $hackaton->id)
        ->call('nextStep')
        ->assertSet('step', 3)
        ->set('socialLinks.0.name', 'Telegram')
        ->set('socialLinks.0.url', 'https://t.me/test')
        ->call('nextStep')
        ->assertSet('step', 4)
        ->set('captainRole.title', 'Team lead')
        ->set('captainRole.description', 'I lead the team and coordinate delivery')
        ->set('captainRole.role', (string) $captainCategory->id)
        ->set('roles.0.title', 'Dev')
        ->set('roles.0.description', 'Разработка продукта.')
        ->set('roles.0.role', (string) $vacancyCategory->id)
        ->call('save')
        ->assertRedirect('/profile/teams');

    expect(Team::query()->where('title', $teamTitle)->exists())->toBeTrue();
});

test('team create wizard stays on step one when title and description are empty', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(TeamsCreate::class)
        ->set('title', '')
        ->set('description', '')
        ->call('nextStep')
        ->assertHasErrors(['title', 'description'])
        ->assertSet('step', 1);
});
