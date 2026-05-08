<?php

use App\Models\Hackaton;
use App\Models\HackatonCase;
use App\Models\HackatonCaseSubmission;
use App\Models\HackatonJudge;
use App\Models\Team;
use App\Models\User;

use function Pest\Laravel\actingAs;

test('a judge can score a submission', function () {
    // Arrange
    $hackaton = Hackaton::factory()->create();
    $case = HackatonCase::factory()->create(['hackaton_id' => $hackaton->id]);
    $team = Team::factory()->create();
    $submission = HackatonCaseSubmission::factory()->create([
        'hackaton_case_id' => $case->id,
        'team_id' => $team->id,
    ]);

    $judgeUser = User::factory()->create();
    HackatonJudge::factory()->create([
        'hackaton_id' => $hackaton->id,
        'user_id' => $judgeUser->id,
    ]);

    // Act
    actingAs($judgeUser)
        ->post(route('hackatons.scores.store', $hackaton), [
            'hackaton_case_submission_id' => $submission->id,
            'score' => 85,
            'max_score' => 100,
            'comment' => 'Great work!',
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    // Assert
    $this->assertDatabaseHas('hackaton_case_scores', [
        'hackaton_case_submission_id' => $submission->id,
        'reviewed_by' => $judgeUser->id,
        'score' => 85,
        'comment' => 'Great work!',
    ]);
});

test('a regular user cannot score a submission', function () {
    $hackaton = Hackaton::factory()->create();
    $case = HackatonCase::factory()->create(['hackaton_id' => $hackaton->id]);
    $team = Team::factory()->create();
    $submission = HackatonCaseSubmission::factory()->create([
        'hackaton_case_id' => $case->id,
        'team_id' => $team->id,
    ]);
    $regularUser = User::factory()->create();

    $this->actingAs($regularUser)
        ->post(route('hackatons.scores.store', $hackaton), [
            'hackaton_case_submission_id' => $submission->id,
            'score' => 60,
        ])
        ->assertForbidden();
});

test('score submission endpoint uses csrf middleware in web group', function () {
    $webMiddleware = app('router')->getMiddlewareGroups()['web'] ?? [];

    $containsCsrfMiddleware = collect($webMiddleware)->contains(
        fn (string $middleware): bool => str_contains($middleware, 'CsrfToken')
    );

    expect($containsCsrfMiddleware)->toBeTrue();
});
