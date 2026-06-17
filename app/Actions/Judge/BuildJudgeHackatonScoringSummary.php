<?php

declare(strict_types=1);

namespace App\Actions\Judge;

use App\Models\Hackaton;
use App\Models\HackatonCaseScore;
use App\Models\HackatonCaseSubmission;
use App\Models\User;
use Illuminate\Support\Collection;

final class BuildJudgeHackatonScoringSummary
{
    /**
     * @return array{
     *     totalSubmissions: int,
     *     ratedSubmissions: int,
     *     unratedSubmissions: int,
     *     cases: Collection<int, array{id: int, title: string, total: int, rated: int, unrated: int}>
     * }
     */
    public function handle(Hackaton $hackaton, User $judge): array
    {
        $userId = (int) $judge->id;

        $cases = $hackaton->cases()
            ->select(['id', 'title', 'hackaton_id'])
            ->orderBy('id')
            ->get();

        $submissionIdsByCase = HackatonCaseSubmission::query()
            ->whereIn('hackaton_case_id', $cases->pluck('id'))
            ->get(['id', 'hackaton_case_id'])
            ->groupBy('hackaton_case_id');

        $ratedSubmissionIds = HackatonCaseScore::query()
            ->where('reviewed_by', $userId)
            ->where('is_final', true)
            ->whereIn(
                'hackaton_case_submission_id',
                $submissionIdsByCase->flatten()->pluck('id')
            )
            ->pluck('hackaton_case_submission_id')
            ->flip();

        $caseSummaries = $cases->map(function ($case) use ($submissionIdsByCase, $ratedSubmissionIds): array {
            $submissions = $submissionIdsByCase->get($case->id, collect());
            $total = $submissions->count();
            $rated = $submissions->filter(fn ($s) => $ratedSubmissionIds->has($s->id))->count();

            return [
                'id' => $case->id,
                'title' => $case->title,
                'total' => $total,
                'rated' => $rated,
                'unrated' => max(0, $total - $rated),
            ];
        });

        $totalSubmissions = (int) $caseSummaries->sum('total');
        $ratedSubmissions = (int) $caseSummaries->sum('rated');

        return [
            'totalSubmissions' => $totalSubmissions,
            'ratedSubmissions' => $ratedSubmissions,
            'unratedSubmissions' => max(0, $totalSubmissions - $ratedSubmissions),
            'cases' => $caseSummaries,
        ];
    }
}
