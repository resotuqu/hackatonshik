<?php

declare(strict_types=1);

use App\Actions\Judge\EvaluateSubmissionAction;
use App\Actions\Judge\SaveScoreDraftAction;
use App\Enums\JudgeDomain;
use App\Models\Hackaton;
use App\Models\HackatonCase;
use App\Models\HackatonCaseSubmission;
use App\Models\HackatonJudge;
use App\Models\Team;
use App\Models\User;
use Illuminate\Validation\ValidationException;

test('judge can only edit criteria from their domain', function () {
    $hackaton = Hackaton::factory()->create();
    $case = HackatonCase::factory()->create([
        'hackaton_id' => $hackaton->id,
        'rubric_json' => [
            ['id' => 'dev', 'label' => 'Dev', 'max' => 10, 'domain' => JudgeDomain::DEV->value],
            ['id' => 'ux', 'label' => 'UX', 'max' => 10, 'domain' => JudgeDomain::DESIGN->value],
        ],
    ]);

    $team = Team::factory()->for($hackaton)->create();
    $submission = HackatonCaseSubmission::factory()->create([
        'hackaton_case_id' => $case->id,
        'team_id' => $team->id,
    ]);

    $judge = User::factory()->judge()->create();
    HackatonJudge::factory()->create([
        'hackaton_id' => $hackaton->id,
        'user_id' => $judge->id,
        'domain' => JudgeDomain::DEV->value,
    ]);

    $action = app(SaveScoreDraftAction::class);

    expect(fn () => $action->handle($judge, $submission, [
        'criteria_scores' => [
            'ux' => ['score' => 7],
        ],
    ]))->toThrow(ValidationException::class);
});

test('finalizing stores is_final and reviewed_at and computes totals per judge domain', function () {
    $hackaton = Hackaton::factory()->create();
    $case = HackatonCase::factory()->create([
        'hackaton_id' => $hackaton->id,
        'rubric_json' => [
            ['id' => 'dev', 'label' => 'Dev', 'max' => 10, 'domain' => JudgeDomain::DEV->value],
            ['id' => 'ux', 'label' => 'UX', 'max' => 10, 'domain' => JudgeDomain::DESIGN->value],
        ],
    ]);

    $team = Team::factory()->for($hackaton)->create();
    $submission = HackatonCaseSubmission::factory()->create([
        'hackaton_case_id' => $case->id,
        'team_id' => $team->id,
    ]);

    $judge = User::factory()->judge()->create();
    HackatonJudge::factory()->create([
        'hackaton_id' => $hackaton->id,
        'user_id' => $judge->id,
        'domain' => JudgeDomain::DEV->value,
    ]);

    $action = app(EvaluateSubmissionAction::class);

    $score = $action->handle($judge, $submission, [
        'criteria_scores' => [
            'dev' => ['score' => 8],
        ],
        'general_comment' => 'ok',
    ]);

    expect($score->is_final)->toBeTrue();
    expect($score->reviewed_at)->not->toBeNull();
    expect($score->score)->toBe(8);
    expect($score->max_score)->toBe(10);
});
