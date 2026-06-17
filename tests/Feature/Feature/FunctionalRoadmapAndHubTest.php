<?php

use App\Models\Hackaton;
use App\Models\HackatonApplication;
use App\Models\Team;
use App\Models\TeamRole;
use App\Models\User;
use App\Notifications\ApplicationStatusUpdated;
use Illuminate\Support\Facades\Notification;

test('organizer can export teams csv', function () {
    $organizer = User::factory()->partner()->create();
    $participant = User::factory()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create();
    Team::factory()->for($participant)->for($hackaton)->create();

    $response = $this
        ->actingAs($organizer)
        ->get(route('hackatons.export.teams', $hackaton));

    $response->assertSuccessful();
    $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
});

test('non organizer cannot export teams csv', function () {
    $organizer = User::factory()->partner()->create();
    $outsider = User::factory()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create();

    $response = $this
        ->actingAs($outsider)
        ->get(route('hackatons.export.teams', $hackaton));

    $response->assertForbidden();
});

test('participant can open personal hackaton hub', function () {
    $organizer = User::factory()->partner()->create();
    $participant = User::factory()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create();
    $team = Team::factory()->for($participant)->for($hackaton)->create();
    TeamRole::factory()->for($team)->create(['user_id' => $participant->id]);
    HackatonApplication::factory()->create([
        'team_id' => $team->id,
        'hackaton_id' => $hackaton->id,
    ]);

    $response = $this
        ->actingAs($participant)
        ->get(route('participant.hackatons.hub', $hackaton));

    $response->assertSuccessful();
    $response->assertSee('Личный кабинет участника', false);

    $this->actingAs($participant)
        ->get(route('profile.hackatons.hub', $hackaton))
        ->assertRedirect(route('participant.hackatons.hub', $hackaton));
});

test('outsider cannot open personal hackaton hub', function () {
    $organizer = User::factory()->partner()->create();
    $participant = User::factory()->create();
    $outsider = User::factory()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create();
    $team = Team::factory()->for($participant)->for($hackaton)->create();
    TeamRole::factory()->for($team)->create(['user_id' => $participant->id]);

    $response = $this
        ->actingAs($outsider)
        ->get(route('participant.hackatons.hub', $hackaton));

    $response->assertForbidden();
});

test('application status update sends notifications to team participants', function () {
    Notification::fake();

    $organizer = User::factory()->partner()->create();
    $participant = User::factory()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create();
    $team = Team::factory()->for($participant)->create();
    TeamRole::factory()->for($team)->create([
        'user_id' => $participant->id,
    ]);
    $application = HackatonApplication::factory()->create([
        'team_id' => $team->id,
        'hackaton_id' => $hackaton->id,
    ]);

    $response = $this
        ->actingAs($organizer)
        ->patch(route('hackaton.applications.update', $application), ['status' => 'rejected']);

    $response->assertRedirect();
    Notification::assertSentTo([$participant], ApplicationStatusUpdated::class);
});
