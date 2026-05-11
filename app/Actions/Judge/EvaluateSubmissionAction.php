<?php

declare(strict_types=1);

namespace App\Actions\Judge;

use App\Enums\JudgeDomain;
use App\Models\HackatonCaseScore;
use App\Models\HackatonCaseSubmission;
use App\Models\HackatonJudge;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;

final class EvaluateSubmissionAction
{
    public function __construct(
        private readonly ComputeScoreTotalsFromRubricAction $computeTotals,
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
        $judgeDomain = $this->resolveJudgeDomain($judge, $submission);

        $criteriaScores = Arr::wrap($payload['criteria_scores'] ?? []);
        $this->ensureOnlyAllowedCriteriaUpdated($submission, $judgeDomain, $criteriaScores);

        $totals = $this->computeTotals->handle($submission->case, $judgeDomain, $criteriaScores);

        /** @var HackatonCaseScore $score */
        $score = HackatonCaseScore::query()->updateOrCreate(
            [
                'hackaton_case_submission_id' => $submission->id,
                'reviewed_by' => $judge->id,
            ],
            [
                'criteria_scores' => $criteriaScores,
                'field_comments' => Arr::wrap($payload['field_comments'] ?? []),
                'general_comment' => $payload['general_comment'] ?? null,
                'score' => $totals['score'],
                'max_score' => $totals['max_score'],
                'is_final' => true,
                'reviewed_at' => now(),
            ],
        );

        return $score;
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
        if (! $domain instanceof JudgeDomain) {
            return JudgeDomain::DEV;
        }

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
        /** @var array<int, array{id?: string, domain?: string}> $rubric */
        $rubric = is_array($submission->case?->rubric_json) ? $submission->case->rubric_json : [];

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
