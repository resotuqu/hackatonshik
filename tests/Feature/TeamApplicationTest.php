<?php

use App\Enums\ApplicationStatus;
use App\Models\Team;
use App\Models\TeamApplication;
use App\Models\TeamRole;
use App\Models\User;

use function Pest\Laravel\actingAs;

test('a user can apply for a team role', function () {
    // Arrange
    $captain = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $captain->id]);
    $teamRole = TeamRole::factory()->create(['team_id' => $team->id, 'user_id' => null]);
    $applicant = User::factory()->create();

    // Act
    actingAs($applicant)
        ->post(route('team.applications.store'), [
            'team_role_id' => $teamRole->id,
            'message' => 'I would like to join!',
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    // Assert
    $this->assertDatabaseHas('team_applications', [
        'team_role_id' => $teamRole->id,
        'user_id' => $applicant->id,
        'status' => ApplicationStatus::PENDING,
        'message' => 'I would like to join!',
    ]);
});

test('a captain can accept a team application', function () {
    // Arrange
    $captain = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $captain->id]);
    $teamRole = TeamRole::factory()->create(['team_id' => $team->id, 'user_id' => null]);
    $applicant = User::factory()->create();
    $application = TeamApplication::factory()->create([
        'team_role_id' => $teamRole->id,
        'user_id' => $applicant->id,
        'status' => ApplicationStatus::PENDING,
    ]);

    // Act
    actingAs($captain)
        ->patch(route('team.applications.update', $application), [
            'status' => ApplicationStatus::ACCEPTED->value,
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    // Assert
    $this->assertDatabaseHas('team_applications', [
        'id' => $application->id,
        'status' => ApplicationStatus::ACCEPTED,
        'reviewed_by' => $captain->id,
    ]);

    $this->assertDatabaseHas('team_roles', [
        'id' => $teamRole->id,
        'user_id' => $applicant->id,
    ]);
});

test('a captain can reject a team application', function () {
    // Arrange
    $captain = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $captain->id]);
    $teamRole = TeamRole::factory()->create(['team_id' => $team->id, 'user_id' => null]);
    $applicant = User::factory()->create();
    $application = TeamApplication::factory()->create([
        'team_role_id' => $teamRole->id,
        'user_id' => $applicant->id,
        'status' => ApplicationStatus::PENDING,
    ]);

    // Act
    actingAs($captain)
        ->patch(route('team.applications.update', $application), [
            'status' => ApplicationStatus::REJECTED->value,
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    // Assert
    $this->assertDatabaseHas('team_applications', [
        'id' => $application->id,
        'status' => ApplicationStatus::REJECTED,
        'reviewed_by' => $captain->id,
    ]);

    $this->assertDatabaseHas('team_roles', [
        'id' => $teamRole->id,
        'user_id' => null,
    ]);
});

test('a user can delete their own application', function () {
    // Arrange
    $applicant = User::factory()->create();
    $teamRole = TeamRole::factory()->create(['user_id' => null]);
    $application = TeamApplication::factory()->create([
        'team_role_id' => $teamRole->id,
        'user_id' => $applicant->id,
        'status' => ApplicationStatus::PENDING,
    ]);

    // Act
    actingAs($applicant)
        ->delete(route('team.applications.destroy', $application))
        ->assertRedirect()
        ->assertSessionHas('success');

    // Assert
    $this->assertDatabaseMissing('team_applications', [
        'id' => $application->id,
    ]);
});
