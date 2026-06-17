<?php

declare(strict_types=1);

namespace App\Actions\Scoring;

use App\Actions\Judge\ComputeScoreTotalsFromRubricAction;
use App\Enums\JudgeDomain;
use App\Models\HackatonCase;
use App\Models\HackatonCaseScore;
use App\Models\HackatonCaseSubmission;
use App\Models\HackatonJudge;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;

final class StoreSubmissionScoreAction
{
    public function __construct(
        private readonly ComputeScoreTotalsFromRubricAction $computeTotals,
    ) {}

    /**
     * @param  array{score: int, max_score?: int|null, comment?: string|null}  $payload
     */
    public function storeSimpleScore(User $reviewer, HackatonCaseSubmission $submission, array $payload): HackatonCaseScore
    {
        $case = $submission->case;
        if ($case instanceof HackatonCase && $this->caseHasRubric($case)) {
            throw ValidationException::withMessages([
                'score' => ['Для этого кейса используйте интерфейс оценивания по рубрике.'],
            ]);
        }

        /** @var HackatonCaseScore $score */
        $score = HackatonCaseScore::query()->updateOrCreate(
            [
                'hackaton_case_submission_id' => $submission->id,
                'reviewed_by' => $reviewer->id,
            ],
            [
                'score' => (int) $payload['score'],
                'max_score' => (int) ($payload['max_score'] ?? 100),
                'general_comment' => $payload['comment'] ?? null,
                'is_final' => true,
                'reviewed_at' => now(),
            ],
        );

        return $score;
    }

    /**
     * @param  array{
     *   criteria_scores?: array<string, mixed>,
     *   field_comments?: array<string, mixed>,
     *   general_comment?: string|null
     * }  $payload
     */
    public function storeRubricScore(
        User $reviewer,
        HackatonCaseSubmission $submission,
        array $payload,
        bool $isFinal = true,
    ): HackatonCaseScore {
        $judgeDomain = $this->resolveJudgeDomain($reviewer, $submission);

        $case = $submission->case;
        if (! $case instanceof HackatonCase) {
            throw new AuthorizationException;
        }

        $criteriaScores = Arr::wrap($payload['criteria_scores'] ?? []);
        $this->ensureOnlyAllowedCriteriaUpdated($submission, $judgeDomain, $criteriaScores);

        $totals = $this->computeTotals->handle($case, $judgeDomain, $criteriaScores);

        /** @var HackatonCaseScore $score */
        $score = HackatonCaseScore::query()->updateOrCreate(
            [
                'hackaton_case_submission_id' => $submission->id,
                'reviewed_by' => $reviewer->id,
            ],
            [
                'criteria_scores' => $criteriaScores,
                'field_comments' => Arr::wrap($payload['field_comments'] ?? []),
                'general_comment' => $payload['general_comment'] ?? null,
                'score' => $totals['score'],
                'max_score' => $totals['max_score'],
                'is_final' => $isFinal,
                'draft_saved_at' => $isFinal ? null : now(),
                'reviewed_at' => $isFinal ? now() : null,
            ],
        );

        return $score;
    }

    private function caseHasRubric(HackatonCase $case): bool
    {
        $rubric = $case->rubric_json ?? [];

        return $rubric !== [];
    }

    private function resolveJudgeDomain(User $judge, HackatonCaseSubmission $submission): JudgeDomain
    {
        $case = $submission->case;
        if (! $case) {
            throw new AuthorizationException;
        }

        $assignment = HackatonJudge::query()
            ->where('hackaton_id', $case->hackaton_id)
            ->where('user_id', $judge->id)
            ->first();

        if (! $assignment instanceof HackatonJudge) {
            throw new AuthorizationException;
        }

        $domain = $assignment->domain;

        return $domain;
    }

    /**
     * @param  array<string, mixed>  $criteriaScores
     */
    private function ensureOnlyAllowedCriteriaUpdated(
        HackatonCaseSubmission $submission,
        JudgeDomain $judgeDomain,
        array $criteriaScores,
    ): void {
        $case = $submission->case;
        if (! $case instanceof HackatonCase) {
            throw new AuthorizationException;
        }

        /** @var array<int, array{id?: string, domain?: string}> $rubric */
        $rubric = $case->rubric_json ?? [];

        $allowed = [];
        foreach ($rubric as $criterion) {
            $criterionId = isset($criterion['id']) ? (string) $criterion['id'] : '';
            $criterionDomain = isset($criterion['domain']) ? (string) $criterion['domain'] : null;
            if ($criterionId !== '' && $criterionDomain === $judgeDomain->value) {
                $allowed[$criterionId] = true;
            }
        }

        foreach (array_keys($criteriaScores) as $criterionId) {
            if (! isset($allowed[(string) $criterionId])) {
                throw ValidationException::withMessages([
                    'criteria_scores' => ['Нельзя изменять критерии другого домена.'],
                ]);
            }
        }
    }
}
