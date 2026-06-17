<?php

use App\Actions\Hackaton\BuildHackatonTeamLeaderboard;
use App\Enums\JudgeDomain;
use App\Models\Hackaton;
use App\Models\HackatonCase;
use App\Models\HackatonCaseScore;
use App\Models\HackatonCaseSubmission;
use App\Models\HackatonJudge;
use App\Models\Team;
use App\Models\User;

test('organizer can store simple score when case has no rubric', function () {
    $organizer = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create();
    $case = HackatonCase::factory()->withoutRubric()->for($hackaton)->create();
    $submission = HackatonCaseSubmission::factory()->for($case, 'case')->create();

    $this->actingAs($organizer)
        ->post(route('hackatons.scores.store', $hackaton), [
            'hackaton_case_submission_id' => $submission->id,
            'score' => 72,
            'max_score' => 100,
            'comment' => 'Solid',
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    $this->assertDatabaseHas('hackaton_case_scores', [
        'hackaton_case_submission_id' => $submission->id,
        'reviewed_by' => $organizer->id,
        'score' => 72,
        'is_final' => true,
    ]);
});

test('legacy simple scoring is blocked when case has rubric', function () {
    $organizer = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create();
    $case = HackatonCase::factory()->for($hackaton)->create([
        'rubric_json' => [
            ['id' => 'dev', 'label' => 'Dev', 'max' => 10, 'domain' => JudgeDomain::DEV->value],
        ],
    ]);
    $submission = HackatonCaseSubmission::factory()->for($case, 'case')->create();

    $this->actingAs($organizer)
        ->post(route('hackatons.scores.store', $hackaton), [
            'hackaton_case_submission_id' => $submission->id,
            'score' => 72,
            'max_score' => 100,
        ])
        ->assertSessionHasErrors('score');
});

test('leaderboard aggregates multiple judge domain scores per submission', function () {
    $hackaton = Hackaton::factory()->create();
    $case = HackatonCase::factory()->for($hackaton)->create([
        'rubric_json' => [
            ['id' => 'dev', 'label' => 'Dev', 'max' => 10, 'domain' => JudgeDomain::DEV->value],
            ['id' => 'ux', 'label' => 'UX', 'max' => 10, 'domain' => JudgeDomain::DESIGN->value],
        ],
    ]);
    $team = Team::factory()->for($hackaton)->create();
    $submission = HackatonCaseSubmission::factory()->for($case, 'case')->create([
        'team_id' => $team->id,
    ]);

    $devJudge = User::factory()->judge()->create();
    $designJudge = User::factory()->judge()->create();

    HackatonJudge::factory()->create([
        'hackaton_id' => $hackaton->id,
        'user_id' => $devJudge->id,
        'domain' => JudgeDomain::DEV->value,
    ]);
    HackatonJudge::factory()->create([
        'hackaton_id' => $hackaton->id,
        'user_id' => $designJudge->id,
        'domain' => JudgeDomain::DESIGN->value,
    ]);

    HackatonCaseScore::factory()->create([
        'hackaton_case_submission_id' => $submission->id,
        'reviewed_by' => $devJudge->id,
        'score' => 8,
        'max_score' => 10,
        'is_final' => true,
        'reviewed_at' => now(),
    ]);

    HackatonCaseScore::factory()->create([
        'hackaton_case_submission_id' => $submission->id,
        'reviewed_by' => $designJudge->id,
        'score' => 7,
        'max_score' => 10,
        'is_final' => true,
        'reviewed_at' => now(),
    ]);

    $leaderboard = app(BuildHackatonTeamLeaderboard::class)->handle($hackaton);

    expect($leaderboard)->toHaveCount(1)
        ->and($leaderboard[0]['team']?->id)->toBe($team->id)
        ->and($leaderboard[0]['total_score'])->toBe(15)
        ->and($leaderboard[0]['max_score'])->toBe(20);
});
