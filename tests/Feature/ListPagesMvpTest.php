<?php

use App\Enums\HackatonStatus;
use App\Models\Hackaton;
use App\Models\ListAnalyticsEvent;
use App\Models\Role;
use App\Models\SavedListFilter;
use App\Models\Team;
use App\Models\TeamApplication;
use App\Models\TeamRole;
use App\Models\User;
use Livewire\Livewire;

test('hackatons page supports status filter and quick apply', function () {
    $user = User::factory()->create();
    $upcoming = Hackaton::factory()->create([
        'title' => 'Upcoming Hackaton',
        'start_at' => now()->addDays(5)->toDateString(),
        'end_at' => now()->addDays(7)->toDateString(),
        'is_public' => true,
        'status' => HackatonStatus::REGISTRATION_OPEN,
    ]);
    Hackaton::factory()->create([
        'title' => 'Finished Hackaton',
        'start_at' => now()->subDays(10)->toDateString(),
        'end_at' => now()->subDays(5)->toDateString(),
        'is_public' => true,
        'status' => HackatonStatus::FINISHED,
    ]);
    $ownedTeam = Team::factory()->for($user)->for($upcoming)->create();

    Livewire::actingAs($user)
        ->test('pages::hackatons.index')
        ->set('status', HackatonStatus::REGISTRATION_OPEN->value)
        ->call('search')
        ->call('quickApplyHackaton', $upcoming->id)
        ->assertSee('Upcoming Hackaton')
        ->assertDontSee('Finished Hackaton');

    expect(Hackaton::query()
        ->findOrFail($upcoming->id)
        ->applications()
        ->where('team_id', $ownedTeam->id)
        ->exists())->toBeTrue();
});

test('teams page shows cards only without removed filter controls', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::teams.index')
        ->assertDontSee('Таблица')
        ->assertDontSee('Только открытые к вступлению');
});

test('teams page quick apply creates pending application', function () {
    $owner = User::factory()->create();
    $applicant = User::factory()->create();
    $hackaton = Hackaton::factory()->create();
    $team = Team::factory()->for($owner)->for($hackaton)->create();
    $role = Role::factory()->create();
    $teamRole = TeamRole::factory()->for($team)->for($role)->create(['user_id' => null]);

    Livewire::actingAs($applicant)
        ->test('pages::teams.index')
        ->call('quickApplyTeam', $team->id);

    expect(TeamApplication::query()
        ->where('user_id', $applicant->id)
        ->where('team_role_id', $teamRole->id)
        ->where('status', 'pending')
        ->exists())->toBeTrue();
});

test('authenticated user can save list filters', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::teams.index')
        ->set('q', 'frontend')
        ->set('saved_filter_name', 'Frontend search')
        ->call('saveCurrentFilter');

    expect(SavedListFilter::query()
        ->where('user_id', $user->id)
        ->where('list_key', 'teams')
        ->where('name', 'Frontend search')
        ->exists())->toBeTrue();
});

test('list pages record analytics events', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)->test('pages::hackatons.index');
    Livewire::actingAs($user)->test('pages::teams.index')->call('search');

    expect(ListAnalyticsEvent::query()->where('list_key', 'hackatons')->where('event_name', 'list_view')->exists())
        ->toBeTrue();
    expect(ListAnalyticsEvent::query()->where('list_key', 'teams')->where('event_name', 'filter_apply')->exists())
        ->toBeTrue();
});
