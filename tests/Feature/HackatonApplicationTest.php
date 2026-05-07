<?php

use App\Models\Hackaton;
use App\Models\Team;
use App\Models\User;

use function Pest\Laravel\actingAs;

test('a user can apply to a hackaton', function () {
    // Arrange
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);
    $hackaton = Hackaton::factory()->create();

    // Act
    actingAs($user)
        ->post(route('hackaton.applications.store'), [
            'hackaton_id' => $hackaton->id,
            'team_id' => $team->id,
        ]);

    // Assert
    $this->assertDatabaseHas('hackaton_applications', [
        'hackaton_id' => $hackaton->id,
        'team_id' => $team->id,
    ]);
});
