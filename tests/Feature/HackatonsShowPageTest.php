<?php

declare(strict_types=1);

use App\Enums\HackatonStatus;
use App\Models\Hackaton;
use App\Models\HackatonJudge;
use App\Models\Team;
use App\Models\TeamRole;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

test('guest can open public hackaton show page', function () {
    $organizer = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create([
        'is_public' => true,
        'title' => 'PublicShowHackUnique',
    ]);

    get(route('hackatons.show', $hackaton))
        ->assertOk()
        ->assertSee('PublicShowHackUnique', false);
});

test('guest cannot open private hackaton show page', function () {
    $organizer = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create([
        'is_public' => false,
        'status' => HackatonStatus::DRAFT,
    ]);

    get(route('hackatons.show', $hackaton))
        ->assertForbidden();
});

test('organizer can open own private hackaton show page', function () {
    $organizer = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create([
        'is_public' => false,
        'status' => HackatonStatus::DRAFT,
        'title' => 'PrivateOwnerHackUnique',
    ]);

    actingAs($organizer)
        ->get(route('hackatons.show', $hackaton))
        ->assertOk()
        ->assertSee('PrivateOwnerHackUnique', false);
});

test('team participant can open private hackaton show page', function () {
    $organizer = User::factory()->partner()->create();
    $captain = User::factory()->create();
    $member = User::factory()->create();

    $hackaton = Hackaton::factory()->for($organizer)->create([
        'is_public' => false,
        'status' => HackatonStatus::DRAFT,
    ]);

    $team = Team::factory()->for($captain)->for($hackaton)->create();
    TeamRole::factory()->for($team)->create(['user_id' => $member->id]);

    actingAs($captain)
        ->get(route('hackatons.show', $hackaton))
        ->assertOk();

    actingAs($member)
        ->get(route('hackatons.show', $hackaton))
        ->assertOk();
});

test('assigned judge can open private hackaton show page', function () {
    $organizer = User::factory()->partner()->create();
    $judge = User::factory()->judge()->create();

    $hackaton = Hackaton::factory()->for($organizer)->create([
        'is_public' => false,
        'status' => HackatonStatus::DRAFT,
    ]);

    HackatonJudge::factory()->create([
        'hackaton_id' => $hackaton->id,
        'user_id' => $judge->id,
    ]);

    actingAs($judge)
        ->get(route('hackatons.show', $hackaton))
        ->assertOk();
});

test('outsider cannot open private hackaton show page', function () {
    $organizer = User::factory()->partner()->create();
    $outsider = User::factory()->create();

    $hackaton = Hackaton::factory()->for($organizer)->create([
        'is_public' => false,
        'status' => HackatonStatus::DRAFT,
    ]);

    actingAs($outsider)
        ->get(route('hackatons.show', $hackaton))
        ->assertForbidden();
});

test('mount syncs hackaton status to in progress when timeline is active', function () {
    $organizer = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create([
        'is_public' => true,
        'status' => HackatonStatus::REGISTRATION_OPEN,
        'start_at' => now()->subHour(),
        'end_at' => now()->addHour(),
    ]);

    get(route('hackatons.show', $hackaton))->assertOk();

    expect($hackaton->fresh()->status)->toBe(HackatonStatus::IN_PROGRESS);
});

test('archived hackatons are not auto-synced on show', function () {
    $organizer = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create([
        'is_public' => true,
        'status' => HackatonStatus::ARCHIVED,
        'start_at' => now()->subHour(),
        'end_at' => now()->addHour(),
    ]);

    get(route('hackatons.show', $hackaton))->assertOk();

    expect($hackaton->fresh()->status)->toBe(HackatonStatus::ARCHIVED);
});

test('show page mounts livewire tab panels for organizer', function () {
    $organizer = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create([
        'is_public' => true,
    ]);

    actingAs($organizer)
        ->get(route('hackatons.show', $hackaton))
        ->assertOk()
        ->assertSeeLivewire('hackatons.show-cases-panel')
        ->assertSeeLivewire('hackatons.show-applications-panel')
        ->assertSeeLivewire('hackatons.show-organization-panel')
        ->assertSee('Жизненный цикл', false)
        ->assertSee('Центр действий', false);
});
