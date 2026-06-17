<?php

declare(strict_types=1);

use App\Models\Hackaton;
use App\Models\HackatonAnnouncement;
use App\Models\HackatonCase;
use App\Models\HackatonCaseField;
use App\Models\Role;
use App\Models\Team;
use App\Models\TeamRole;
use App\Models\User;

test('organizer can destroy own hackaton case', function () {
    $organizer = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create();
    $case = HackatonCase::factory()->for($hackaton)->create();

    $this->actingAs($organizer)
        ->from(route('hackatons.show', $hackaton))
        ->delete(route('hackatons.cases.destroy', [$hackaton, $case]))
        ->assertRedirect();

    expect(HackatonCase::query()->find($case->id))->toBeNull();
});

test('intruder cannot destroy hackaton case', function () {
    $organizer = User::factory()->partner()->create();
    $intruder = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create();
    $case = HackatonCase::factory()->for($hackaton)->create();

    $this->actingAs($intruder)
        ->from(route('hackatons.show', $hackaton))
        ->delete(route('hackatons.cases.destroy', [$hackaton, $case]))
        ->assertForbidden();

    expect(HackatonCase::query()->find($case->id))->not->toBeNull();
});

test('organizer can destroy hackaton case field', function () {
    $organizer = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create();
    $case = HackatonCase::factory()->for($hackaton)->create();
    $field = HackatonCaseField::factory()->for($case, 'case')->create();

    $this->actingAs($organizer)
        ->from(route('hackatons.show', $hackaton))
        ->delete(route('hackatons.cases.fields.destroy', [$hackaton, $case, $field]))
        ->assertRedirect();

    expect(HackatonCaseField::query()->find($field->id))->toBeNull();
});

test('intruder cannot destroy hackaton case field', function () {
    $organizer = User::factory()->partner()->create();
    $intruder = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create();
    $case = HackatonCase::factory()->for($hackaton)->create();
    $field = HackatonCaseField::factory()->for($case, 'case')->create();

    $this->actingAs($intruder)
        ->from(route('hackatons.show', $hackaton))
        ->delete(route('hackatons.cases.fields.destroy', [$hackaton, $case, $field]))
        ->assertForbidden();

    expect(HackatonCaseField::query()->find($field->id))->not->toBeNull();
});

test('organizer can destroy hackaton announcement', function () {
    $organizer = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create();
    $announcement = HackatonAnnouncement::factory()->for($hackaton)->create();

    $this->actingAs($organizer)
        ->from(route('hackatons.show', $hackaton))
        ->delete(route('hackatons.announcements.destroy', [$hackaton, $announcement]))
        ->assertRedirect();

    expect(HackatonAnnouncement::query()->find($announcement->id))->toBeNull();
});

test('intruder cannot destroy hackaton announcement', function () {
    $organizer = User::factory()->partner()->create();
    $intruder = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create();
    $announcement = HackatonAnnouncement::factory()->for($hackaton)->create();

    $this->actingAs($intruder)
        ->from(route('hackatons.show', $hackaton))
        ->delete(route('hackatons.announcements.destroy', [$hackaton, $announcement]))
        ->assertForbidden();

    expect(HackatonAnnouncement::query()->find($announcement->id))->not->toBeNull();
});

test('team owner can remove participant from team', function () {
    $owner = User::factory()->create();
    $member = User::factory()->create();
    $team = Team::factory()->for($owner)->create();
    $roleCategory = Role::factory()->create();
    $teamRole = TeamRole::factory()->create([
        'team_id' => $team->id,
        'user_id' => $member->id,
        'role_id' => $roleCategory->id,
    ]);

    $this->actingAs($owner)
        ->from(route('teams.show', $team))
        ->delete(route('teams.participants.destroy', [$team, $teamRole]))
        ->assertRedirect();

    expect($teamRole->fresh()->user_id)->toBeNull();
});

test('non owner cannot remove participant from team', function () {
    $owner = User::factory()->create();
    $intruder = User::factory()->create();
    $member = User::factory()->create();
    $team = Team::factory()->for($owner)->create();
    $roleCategory = Role::factory()->create();
    $teamRole = TeamRole::factory()->create([
        'team_id' => $team->id,
        'user_id' => $member->id,
        'role_id' => $roleCategory->id,
    ]);

    $this->actingAs($intruder)
        ->from(route('teams.show', $team))
        ->delete(route('teams.participants.destroy', [$team, $teamRole]))
        ->assertForbidden();

    expect($teamRole->fresh()->user_id)->toBe($member->id);
});
