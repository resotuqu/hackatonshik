<?php

declare(strict_types=1);

namespace App\Actions\Judge;

use App\Enums\JudgeDomain;
use App\Models\HackatonCase;

final class ComputeScoreTotalsFromRubricAction
{
    /**
     * @param  array<string, mixed>  $criteriaScores
     * @return array{score: int, max_score: int}
     */
    public function handle(HackatonCase $case, JudgeDomain $judgeDomain, array $criteriaScores): array
    {
        /** @var array<int, array{id?: string, max?: int, domain?: string}> $rubric */
        $rubric = $case->rubric_json ?? [];

        $score = 0;
        $maxScore = 0;

        foreach ($rubric as $criterion) {
            $criterionId = isset($criterion['id']) ? (string) $criterion['id'] : '';
            if ($criterionId === '') {
                continue;
            }

            $criterionDomain = isset($criterion['domain']) ? (string) $criterion['domain'] : null;
            if ($criterionDomain !== $judgeDomain->value) {
                continue;
            }

            $criterionMax = isset($criterion['max']) ? (int) $criterion['max'] : 0;
            if ($criterionMax <= 0) {
                continue;
            }

            $maxScore += $criterionMax;

            $raw = $criteriaScores[$criterionId] ?? null;
            $criterionScore = is_array($raw) ? (int) ($raw['score'] ?? 0) : (int) $raw;
            if ($criterionScore < 0) {
                $criterionScore = 0;
            }
            if ($criterionScore > $criterionMax) {
                $criterionScore = $criterionMax;
            }

            $score += $criterionScore;
        }

        return [
            'score' => $score,
            'max_score' => $maxScore,
        ];
    }
}
