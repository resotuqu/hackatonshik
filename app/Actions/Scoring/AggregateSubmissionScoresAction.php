<?php

declare(strict_types=1);

namespace App\Actions\Scoring;

use App\Models\HackatonCaseScore;
use App\Models\HackatonCaseSubmission;
use Illuminate\Support\Collection;

final class AggregateSubmissionScoresAction
{
    /**
     * @return array{score: int, max_score: int}
     */
    public function forSubmission(HackatonCaseSubmission $submission): array
    {
        $totals = HackatonCaseScore::query()
            ->where('hackaton_case_submission_id', $submission->id)
            ->where('is_final', true)
            ->selectRaw('COALESCE(SUM(score), 0) as total_score, COALESCE(SUM(max_score), 0) as total_max_score')
            ->first();

        return [
            'score' => (int) ($totals->total_score ?? 0),
            'max_score' => (int) ($totals->total_max_score ?? 0),
        ];
    }

    /**
     * @param  Collection<int, HackatonCaseSubmission>  $submissions
     * @return array<int, array{score: int, max_score: int}>
     */
    public function forManySubmissions(Collection $submissions): array
    {
        if ($submissions->isEmpty()) {
            return [];
        }

        $rows = HackatonCaseScore::query()
            ->whereIn('hackaton_case_submission_id', $submissions->pluck('id'))
            ->where('is_final', true)
            ->groupBy('hackaton_case_submission_id')
            ->selectRaw('hackaton_case_submission_id, COALESCE(SUM(score), 0) as total_score, COALESCE(SUM(max_score), 0) as total_max_score')
            ->get()
            ->keyBy('hackaton_case_submission_id');

        $aggregated = [];

        foreach ($submissions as $submission) {
            $row = $rows->get($submission->id);

            $aggregated[$submission->id] = [
                'score' => (int) ($row->total_score ?? 0),
                'max_score' => (int) ($row->total_max_score ?? 0),
            ];
        }

        return $aggregated;
    }
}
