<?php

declare(strict_types=1);

namespace App\Actions\Judge;

use App\Actions\Scoring\StoreSubmissionScoreAction;
use App\Models\HackatonCaseScore;
use App\Models\HackatonCaseSubmission;
use App\Models\User;

final class SaveScoreDraftAction
{
    public function __construct(
        private readonly StoreSubmissionScoreAction $storeSubmissionScore,
    ) {}

    /**
     * @param  array{
     *   criteria_scores?: array<string, mixed>,
     *   field_comments?: array<string, mixed>,
     *   general_comment?: string|null
     * }  $payload
     */
    public function handle(User $judge, HackatonCaseSubmission $submission, array $payload): HackatonCaseScore
    {
        return $this->storeSubmissionScore->storeRubricScore(
            $judge,
            $submission,
            $payload,
            isFinal: false,
        );
    }
}
