<?php

use App\Enums\HackatonLevel;
use App\Enums\HackatonStatus;
use App\Livewire\Pages\Hackatons\Index as HackatonsIndex;
use App\Livewire\Pages\Teams\Index as TeamsIndex;
use App\Models\Hackaton;
use App\Models\ListAnalyticsEvent;
use App\Models\Role;
use App\Models\SavedListFilter;
use App\Models\Team;
use App\Models\TeamApplication;
use App\Models\TeamRole;
use App\Models\User;
use Livewire\Livewire;

test('hackatons page supports search filter and quick apply', function () {
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
        ->test(HackatonsIndex::class)
        ->set('q', 'Upcoming')
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

test('teams page renders catalog hero and tabs', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(TeamsIndex::class)
        ->assertSee('Каталог команд')
        ->assertSee('Открытые')
        ->assertSee('Все')
        ->assertDontSee('Таблица');
});

test('teams catalog all tab shows full teams when open roles filter off', function () {
    $viewer = User::factory()->create();
    $owner = User::factory()->create();
    $member = User::factory()->create();
    $hackaton = Hackaton::factory()->create();
    $team = Team::factory()->for($owner)->for($hackaton)->create([
        'is_public' => true,
        'title' => 'FullyStaffedUniqueXYZ',
    ]);
    $role = Role::factory()->create();
    TeamRole::factory()->for($team)->for($role)->create(['user_id' => $member->id]);

    Livewire::actingAs($viewer)
        ->test(TeamsIndex::class)
        ->set('catalog_tab', 'all')
        ->set('only_open_roles', false)
        ->assertSee('FullyStaffedUniqueXYZ');

    Livewire::actingAs($viewer)
        ->test(TeamsIndex::class)
        ->set('catalog_tab', 'all')
        ->set('only_open_roles', true)
        ->assertDontSee('FullyStaffedUniqueXYZ');
});

test('teams page quick apply creates pending application', function () {
    $owner = User::factory()->create();
    $applicant = User::factory()->create();
    $hackaton = Hackaton::factory()->create();
    $team = Team::factory()->for($owner)->for($hackaton)->create();
    $role = Role::factory()->create();
    $teamRole = TeamRole::factory()->for($team)->for($role)->create(['user_id' => null]);

    Livewire::actingAs($applicant)
        ->test(TeamsIndex::class)
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
        ->test(TeamsIndex::class)
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

    app()->detectEnvironment(fn (): string => 'local');

    Livewire::actingAs($user)->test(HackatonsIndex::class);
    Livewire::actingAs($user)->test(TeamsIndex::class)->call('search');

    expect(ListAnalyticsEvent::query()->where('list_key', 'hackatons')->where('event_name', 'list_view')->exists())
        ->toBeTrue();
    expect(ListAnalyticsEvent::query()->where('list_key', 'teams')->where('event_name', 'filter_apply')->exists())
        ->toBeTrue();
});

test('hackatons page renders hero and simplified filters', function () {
    Livewire::test(HackatonsIndex::class)
        ->assertSee('Каталог хакатонов')
        ->assertSee('хакатонов')
        ->assertSee('Поиск')
        ->assertSee('Уровень')
        ->assertDontSee('Подборки')
        ->assertDontSee('Только с призами')
        ->assertDontSee('Только публичные');
});

test('hackatons catalog only shows public hackatons', function () {
    Hackaton::factory()->create([
        'title' => 'PublicHackVisible',
        'is_public' => true,
    ]);
    Hackaton::factory()->create([
        'title' => 'PrivateHackHidden',
        'is_public' => false,
    ]);

    Livewire::test(HackatonsIndex::class)
        ->assertSee('PublicHackVisible')
        ->assertDontSee('PrivateHackHidden');
});

test('hackatons level filter works', function () {
    Hackaton::factory()->create([
        'title' => 'BeginnerHackUnique',
        'is_public' => true,
        'level' => HackatonLevel::Beginner,
    ]);
    Hackaton::factory()->create([
        'title' => 'AdvancedHackUnique',
        'is_public' => true,
        'level' => HackatonLevel::Advanced,
    ]);

    Livewire::test(HackatonsIndex::class)
        ->set('level', HackatonLevel::Beginner->value)
        ->call('search')
        ->assertSee('BeginnerHackUnique')
        ->assertDontSee('AdvancedHackUnique');
});

test('hackaton card displays prize fund and dates for active hackaton', function () {
    Hackaton::factory()->create([
        'title' => 'CardMetricsHack',
        'is_public' => true,
        'status' => HackatonStatus::REGISTRATION_OPEN,
        'start_at' => now()->addDays(5),
        'end_at' => now()->addDays(10),
        'prize_fund' => 250000,
        'prize_places_count' => 3,
        'registration_deadline_at' => now()->addDays(2),
    ]);

    Livewire::test(HackatonsIndex::class)
        ->assertSee('CardMetricsHack')
        ->assertSee('Призовой фонд')
        ->assertSee('Команд / участников')
        ->assertSee('250 000')
        ->assertSee('До дедлайна');
});

test('finished hackatons render finished overlay marker', function () {
    Hackaton::factory()->create([
        'title' => 'OverlayFinishedHack',
        'is_public' => true,
        'status' => HackatonStatus::FINISHED,
    ]);

    Livewire::test(HackatonsIndex::class)
        ->assertSee('OverlayFinishedHack')
        ->assertSee('Завершён');
});

test('hackatons status group filter hides non-matching statuses', function () {
    Hackaton::factory()->create([
        'title' => 'ActiveStatusHack',
        'is_public' => true,
        'status' => HackatonStatus::IN_PROGRESS,
    ]);
    Hackaton::factory()->create([
        'title' => 'FinishedStatusHack',
        'is_public' => true,
        'status' => HackatonStatus::FINISHED,
    ]);

    Livewire::test(HackatonsIndex::class)
        ->set('statusGroup', 'active')
        ->call('search')
        ->assertSee('ActiveStatusHack')
        ->assertDontSee('FinishedStatusHack');

    Livewire::test(HackatonsIndex::class)
        ->set('statusGroup', 'finished')
        ->call('search')
        ->assertSee('FinishedStatusHack')
        ->assertDontSee('ActiveStatusHack');
});

test('hackatons status tab switch resets pagination', function () {
    Hackaton::factory()->count(12)->create([
        'is_public' => true,
        'status' => HackatonStatus::FINISHED,
    ]);

    Livewire::test(HackatonsIndex::class)
        ->set('page', 2)
        ->call('setStatusGroup', 'finished')
        ->assertSet('page', 1);
});

test('clearFilters resets filters', function () {
    Livewire::test(HackatonsIndex::class)
        ->set('q', 'something')
        ->set('level', HackatonLevel::Beginner->value)
        ->set('statusGroup', 'active')
        ->call('clearFilters')
        ->assertSet('q', '')
        ->assertSet('level', 'all')
        ->assertSet('statusGroup', 'all');
});

test('hackaton can be created with new metric fields', function () {
    $hackaton = Hackaton::factory()->create([
        'prize_fund' => 1000000,
        'prize_places_count' => 5,
        'level' => HackatonLevel::Pro,
        'registration_deadline_at' => now()->addDays(10),
    ]);

    expect($hackaton->fresh())
        ->prize_fund->toEqual('1000000.00')
        ->prize_places_count->toBe(5)
        ->level->toBe(HackatonLevel::Pro)
        ->registration_deadline_at->not->toBeNull();
});

test('teams page hero shows brand badge', function () {
    Livewire::test(TeamsIndex::class)
        ->assertSee('Каталог команд');
});
