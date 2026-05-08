<?php

declare(strict_types=1);

use App\Enums\ApplicationStatus;
use App\Models\Hackaton;
use App\Models\HackatonApplication;
use App\Models\Team;
use App\Models\TeamRole;
use App\Models\User;
use App\Notifications\ApplicationStatusUpdated;
use Illuminate\Support\Facades\Notification;

use function Pest\Laravel\actingAs;

test('organizer can bulk accept multiple pending applications', function () {
    Notification::fake();

    $organizer = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create();

    $applications = collect();
    for ($i = 0; $i < 3; $i++) {
        $captain = User::factory()->create();
        $team = Team::factory()->for($captain)->create();
        TeamRole::factory()->for($team)->create(['user_id' => $captain->id]);
        $applications->push(HackatonApplication::factory()->create([
            'hackaton_id' => $hackaton->id,
            'team_id' => $team->id,
            'status' => ApplicationStatus::PENDING,
        ]));
    }

    actingAs($organizer)
        ->from(route('hackatons.show', $hackaton))
        ->patch(route('hackaton.applications.bulk-update', $hackaton), [
            'application_ids' => $applications->pluck('id')->all(),
            'status' => ApplicationStatus::ACCEPTED->value,
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    foreach ($applications as $application) {
        $fresh = $application->fresh();
        expect($fresh->status)->toBe(ApplicationStatus::ACCEPTED);
        expect((int) $fresh->team->fresh()->hackaton_id)->toBe((int) $hackaton->id);
    }

    Notification::assertSentTimes(ApplicationStatusUpdated::class, 3);
});

test('organizer can bulk reject multiple pending applications', function () {
    Notification::fake();

    $organizer = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create();

    $applications = collect();
    for ($i = 0; $i < 2; $i++) {
        $captain = User::factory()->create();
        $team = Team::factory()->for($captain)->create();
        TeamRole::factory()->for($team)->create(['user_id' => $captain->id]);
        $applications->push(HackatonApplication::factory()->create([
            'hackaton_id' => $hackaton->id,
            'team_id' => $team->id,
            'status' => ApplicationStatus::PENDING,
        ]));
    }

    actingAs($organizer)
        ->from(route('hackatons.show', $hackaton))
        ->patch(route('hackaton.applications.bulk-update', $hackaton), [
            'application_ids' => $applications->pluck('id')->all(),
            'status' => ApplicationStatus::REJECTED->value,
        ])
        ->assertRedirect();

    foreach ($applications as $application) {
        expect($application->fresh()->status)->toBe(ApplicationStatus::REJECTED);
    }

    Notification::assertSentTimes(ApplicationStatusUpdated::class, 2);
});

test('bulk update does not affect applications from another hackaton', function () {
    Notification::fake();

    $organizerA = User::factory()->partner()->create();
    $organizerB = User::factory()->partner()->create();
    $hackatonA = Hackaton::factory()->for($organizerA)->create();
    $hackatonB = Hackaton::factory()->for($organizerB)->create();

    $captainA = User::factory()->create();
    $teamA = Team::factory()->for($captainA)->create();
    TeamRole::factory()->for($teamA)->create(['user_id' => $captainA->id]);
    $appA = HackatonApplication::factory()->create([
        'hackaton_id' => $hackatonA->id,
        'team_id' => $teamA->id,
        'status' => ApplicationStatus::PENDING,
    ]);

    $captainB = User::factory()->create();
    $teamB = Team::factory()->for($captainB)->create();
    TeamRole::factory()->for($teamB)->create(['user_id' => $captainB->id]);
    $appB = HackatonApplication::factory()->create([
        'hackaton_id' => $hackatonB->id,
        'team_id' => $teamB->id,
        'status' => ApplicationStatus::PENDING,
    ]);

    actingAs($organizerA)
        ->from(route('hackatons.show', $hackatonA))
        ->patch(route('hackaton.applications.bulk-update', $hackatonA), [
            'application_ids' => [$appA->id, $appB->id],
            'status' => ApplicationStatus::ACCEPTED->value,
        ])
        ->assertRedirect();

    expect($appA->fresh()->status)->toBe(ApplicationStatus::ACCEPTED);
    expect($appB->fresh()->status)->toBe(ApplicationStatus::PENDING);
});

test('non organizer cannot perform bulk update', function () {
    $organizer = User::factory()->partner()->create();
    $outsider = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create();
    $captain = User::factory()->create();
    $team = Team::factory()->for($captain)->create();
    $application = HackatonApplication::factory()->create([
        'hackaton_id' => $hackaton->id,
        'team_id' => $team->id,
        'status' => ApplicationStatus::PENDING,
    ]);

    actingAs($outsider)
        ->from(route('hackatons.show', $hackaton))
        ->patch(route('hackaton.applications.bulk-update', $hackaton), [
            'application_ids' => [$application->id],
            'status' => ApplicationStatus::ACCEPTED->value,
        ])
        ->assertForbidden();

    expect($application->fresh()->status)->toBe(ApplicationStatus::PENDING);
});
