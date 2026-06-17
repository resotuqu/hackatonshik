<?php

declare(strict_types=1);

use App\Actions\Scoring\AggregateSubmissionScoresAction;
use App\Actions\Scoring\StoreSubmissionScoreAction;
use App\Models\HackatonCase;
use App\Models\HackatonCaseScore;
use App\Models\HackatonCaseSubmission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('aggregate submission scores sums final judge scores', function () {
    $submission = HackatonCaseSubmission::factory()->create();
    $reviewerA = User::factory()->create();
    $reviewerB = User::factory()->create();

    HackatonCaseScore::factory()->create([
        'hackaton_case_submission_id' => $submission->id,
        'reviewed_by' => $reviewerA->id,
        'score' => 30,
        'max_score' => 50,
        'is_final' => true,
    ]);
    HackatonCaseScore::factory()->create([
        'hackaton_case_submission_id' => $submission->id,
        'reviewed_by' => $reviewerB->id,
        'score' => 20,
        'max_score' => 50,
        'is_final' => true,
    ]);
    HackatonCaseScore::factory()->create([
        'hackaton_case_submission_id' => $submission->id,
        'reviewed_by' => User::factory()->create()->id,
        'score' => 99,
        'max_score' => 100,
        'is_final' => false,
    ]);

    $totals = app(AggregateSubmissionScoresAction::class)->forSubmission($submission);

    expect($totals)->toBe([
        'score' => 50,
        'max_score' => 100,
    ]);
});

test('store submission score upserts simple score for reviewer', function () {
    $case = HackatonCase::factory()->create(['rubric_json' => []]);
    $submission = HackatonCaseSubmission::factory()->create([
        'hackaton_case_id' => $case->id,
    ]);
    $reviewer = User::factory()->create();

    $score = app(StoreSubmissionScoreAction::class)->storeSimpleScore($reviewer, $submission, [
        'score' => 42,
        'max_score' => 100,
        'comment' => 'Solid work',
    ]);

    expect($score->score)->toBe(42)
        ->and($score->max_score)->toBe(100)
        ->and($score->is_final)->toBeTrue();

    $updated = app(StoreSubmissionScoreAction::class)->storeSimpleScore($reviewer, $submission, [
        'score' => 45,
        'max_score' => 100,
        'comment' => 'Updated',
    ]);

    expect($updated->id)->toBe($score->id)
        ->and($updated->score)->toBe(45)
        ->and(HackatonCaseScore::query()->where('hackaton_case_submission_id', $submission->id)->count())->toBe(1);
});
