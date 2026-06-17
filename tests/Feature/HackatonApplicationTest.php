<?php

use App\Enums\ApplicationStatus;
use App\Events\HackatonApplicationChanged;
use App\Models\Hackaton;
use App\Models\HackatonApplication;
use App\Models\HackatonCase;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Event;

test('a user can apply to a hackaton', function () {
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);
    $hackaton = Hackaton::factory()->create();

    Event::fake([HackatonApplicationChanged::class]);

    $this->actingAs($user)
        ->from(route('hackatons.show', $hackaton))
        ->post(route('hackaton.applications.store'), [
            'hackaton_id' => $hackaton->id,
            'team_id' => $team->id,
            'message' => 'We want to win!',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('hackaton_applications', [
        'hackaton_id' => $hackaton->id,
        'team_id' => $team->id,
        'message' => 'We want to win!',
        'status' => ApplicationStatus::PENDING->value,
        'hackaton_cases_count_when_applied' => 0,
    ]);

    Event::assertDispatched(HackatonApplicationChanged::class);
});

test('only team owner can apply for that team', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $owner->id]);
    $hackaton = Hackaton::factory()->create();

    $this->actingAs($other)
        ->post(route('hackaton.applications.store'), [
            'hackaton_id' => $hackaton->id,
            'team_id' => $team->id,
        ])
        ->assertSessionHasErrors(['team_id']);
});

test('partner cannot apply to hackaton via http store', function () {
    $partner = User::factory()->partner()->create();
    $team = Team::factory()->create(['user_id' => $partner->id]);
    $hackaton = Hackaton::factory()->create();

    $this->actingAs($partner)
        ->post(route('hackaton.applications.store'), [
            'hackaton_id' => $hackaton->id,
            'team_id' => $team->id,
        ])
        ->assertForbidden();
});

test('judge cannot apply to hackaton via http store', function () {
    $judge = User::factory()->judge()->create();
    $team = Team::factory()->create(['user_id' => $judge->id]);
    $hackaton = Hackaton::factory()->create();

    $this->actingAs($judge)
        ->post(route('hackaton.applications.store'), [
            'hackaton_id' => $hackaton->id,
            'team_id' => $team->id,
        ])
        ->assertForbidden();
});

test('team owner can withdraw pending application', function () {
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);
    $hackaton = Hackaton::factory()->create();
    $application = HackatonApplication::factory()->create([
        'team_id' => $team->id,
        'hackaton_id' => $hackaton->id,
        'status' => ApplicationStatus::PENDING,
    ]);

    $this->actingAs($user)
        ->delete(route('hackaton.applications.destroy', $application))
        ->assertRedirect();

    $this->assertDatabaseMissing('hackaton_applications', ['id' => $application->id]);
});

test('organizer can accept application', function () {
    $organizer = User::factory()->partner()->create();
    $member = User::factory()->create();
    $hackaton = Hackaton::factory()->create(['user_id' => $organizer->id]);
    $team = Team::factory()->create(['user_id' => $member->id]);
    $application = HackatonApplication::factory()->create([
        'team_id' => $team->id,
        'hackaton_id' => $hackaton->id,
        'status' => ApplicationStatus::PENDING,
    ]);

    $this->actingAs($organizer)
        ->patch(route('hackaton.applications.update', $application), [
            'status' => ApplicationStatus::ACCEPTED->value,
        ])
        ->assertRedirect();

    $application->refresh();
    expect($application->status)->toBe(ApplicationStatus::ACCEPTED);

    $team->refresh();
    expect($team->hackaton_id)->toBe($hackaton->id);
});

test('organizer can bulk update applications', function () {
    $organizer = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->create(['user_id' => $organizer->id]);

    $apps = HackatonApplication::factory()->count(3)->create([
        'hackaton_id' => $hackaton->id,
        'status' => ApplicationStatus::PENDING,
    ]);

    $this->actingAs($organizer)
        ->patch(route('hackaton.applications.bulk-update', $hackaton), [
            'application_ids' => $apps->pluck('id')->toArray(),
            'status' => ApplicationStatus::ACCEPTED->value,
        ])
        ->assertRedirect();

    foreach ($apps as $app) {
        expect($app->refresh()->status)->toBe(ApplicationStatus::ACCEPTED);
    }
});

test('accepting application assigns team to the only case when cases existed at apply time', function () {
    $organizer = User::factory()->partner()->create();
    $member = User::factory()->create();
    $hackaton = Hackaton::factory()->create(['user_id' => $organizer->id]);
    $case = HackatonCase::factory()->create(['hackaton_id' => $hackaton->id]);
    $team = Team::factory()->create(['user_id' => $member->id]);

    $this->actingAs($member)
        ->from(route('hackatons.show', $hackaton))
        ->post(route('hackaton.applications.store'), [
            'hackaton_id' => $hackaton->id,
            'team_id' => $team->id,
        ])
        ->assertRedirect();

    $application = HackatonApplication::query()
        ->where('hackaton_id', $hackaton->id)
        ->where('team_id', $team->id)
        ->firstOrFail();

    $this->actingAs($organizer)
        ->patch(route('hackaton.applications.update', $application), [
            'status' => ApplicationStatus::ACCEPTED->value,
        ])
        ->assertRedirect();

    expect($team->fresh()->hackaton_case_id)->toBe($case->id);
});

test('accepting application does not assign case when team applied before any cases existed', function () {
    $organizer = User::factory()->partner()->create();
    $member = User::factory()->create();
    $hackaton = Hackaton::factory()->create(['user_id' => $organizer->id]);
    $team = Team::factory()->create(['user_id' => $member->id]);

    $this->actingAs($member)
        ->from(route('hackatons.show', $hackaton))
        ->post(route('hackaton.applications.store'), [
            'hackaton_id' => $hackaton->id,
            'team_id' => $team->id,
        ])
        ->assertRedirect();

    HackatonCase::factory()->create(['hackaton_id' => $hackaton->id]);

    $application = HackatonApplication::query()
        ->where('hackaton_id', $hackaton->id)
        ->where('team_id', $team->id)
        ->firstOrFail();

    $this->actingAs($organizer)
        ->patch(route('hackaton.applications.update', $application), [
            'status' => ApplicationStatus::ACCEPTED->value,
        ])
        ->assertRedirect();

    expect($team->fresh()->hackaton_case_id)->toBeNull();
});

test('accepting application does not assign case when hackathon has multiple cases', function () {
    $organizer = User::factory()->partner()->create();
    $member = User::factory()->create();
    $hackaton = Hackaton::factory()->create(['user_id' => $organizer->id]);
    HackatonCase::factory()->count(2)->create(['hackaton_id' => $hackaton->id]);
    $team = Team::factory()->create(['user_id' => $member->id]);

    $this->actingAs($member)
        ->from(route('hackatons.show', $hackaton))
        ->post(route('hackaton.applications.store'), [
            'hackaton_id' => $hackaton->id,
            'team_id' => $team->id,
        ])
        ->assertRedirect();

    $application = HackatonApplication::query()
        ->where('hackaton_id', $hackaton->id)
        ->where('team_id', $team->id)
        ->firstOrFail();

    $this->actingAs($organizer)
        ->patch(route('hackaton.applications.update', $application), [
            'status' => ApplicationStatus::ACCEPTED->value,
        ])
        ->assertRedirect();

    expect($team->fresh()->hackaton_case_id)->toBeNull();
});
