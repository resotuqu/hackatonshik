<?php

use App\Enums\ApplicationStatus;
use App\Models\Hackaton;
use App\Models\HackatonApplication;
use App\Models\HackatonCase;
use App\Models\HackatonCaseField;
use App\Models\Team;
use App\Models\User;

use function Pest\Laravel\actingAs;

test('a team can submit a case solution', function () {
    // Arrange
    $user = User::factory()->create([
        'email_verified_at' => now(),
        'phone_verified_at' => now(),
    ]);
    $hackaton = Hackaton::factory()->create();
    $team = Team::factory()->create([
        'user_id' => $user->id,
        'hackaton_id' => $hackaton->id,
    ]);

    // Create an accepted application for the team to the hackaton
    HackatonApplication::factory()->create([
        'hackaton_id' => $hackaton->id,
        'team_id' => $team->id,
        'status' => ApplicationStatus::ACCEPTED,
    ]);

    $case = HackatonCase::factory()->create(['hackaton_id' => $hackaton->id, 'is_published' => true]);
    $field = HackatonCaseField::factory()->create([
        'hackaton_case_id' => $case->id,
        'type' => HackatonCaseField::TYPE_TEXT,
    ]);

    // Act
    actingAs($user)
        ->post(route('hackatons.cases.submissions.store', [$hackaton, $case]), [
            'scope' => 'team',
            'answers' => [
                $field->id => 'My answer to the field',
            ],
            'team_id' => $team->id,
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    // Assert
    $this->assertDatabaseHas('hackaton_case_submissions', [
        'hackaton_case_id' => $case->id,
        'team_id' => $team->id,
    ]);

    $this->assertDatabaseHas('hackaton_case_answers', [
        'hackaton_case_field_id' => $field->id,
        'value_text' => 'My answer to the field',
    ]);
});

test('unapproved teams cannot submit case solutions', function () {
    // Arrange
    $user = User::factory()->create([
        'email_verified_at' => now(),
        'phone_verified_at' => now(),
    ]);
    $hackaton = Hackaton::factory()->create();
    $team = Team::factory()->create([
        'user_id' => $user->id,
        'hackaton_id' => $hackaton->id,
    ]);

    // Application is PENDING, not ACCEPTED
    HackatonApplication::factory()->create([
        'hackaton_id' => $hackaton->id,
        'team_id' => $team->id,
        'status' => ApplicationStatus::PENDING,
    ]);

    $case = HackatonCase::factory()->create(['hackaton_id' => $hackaton->id, 'is_published' => true]);
    $field = HackatonCaseField::factory()->create([
        'hackaton_case_id' => $case->id,
        'type' => HackatonCaseField::TYPE_TEXT,
    ]);

    // Act
    actingAs($user)
        ->post(route('hackatons.cases.submissions.store', [$hackaton, $case]), [
            'scope' => 'team',
            'answers' => [
                $field->id => 'My answer',
            ],
            'team_id' => $team->id,
        ])
        ->assertStatus(403); // Handled by Gate/Policy

    // Assert
    $this->assertDatabaseMissing('hackaton_case_submissions', [
        'hackaton_case_id' => $case->id,
        'team_id' => $team->id,
    ]);
});
