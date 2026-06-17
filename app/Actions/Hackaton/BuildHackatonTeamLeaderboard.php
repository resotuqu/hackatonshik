<?php

declare(strict_types=1);

namespace App\Actions\Hackaton;

use App\Models\Hackaton;
use App\Models\HackatonCase;
use App\Models\HackatonCaseSubmission;
use App\Models\Team;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class BuildHackatonTeamLeaderboard
{
    /**
     * @return list<array{team: Team|null, total_score: int, max_score: int, completion_percent: int}>
     */
    public function handle(Hackaton $hackaton): array
    {
        $submissionTable = (new HackatonCaseSubmission)->getTable();
        $caseTable = (new HackatonCase)->getTable();
        $scoreTable = 'hackaton_case_scores';

        $submissionTotals = DB::table($scoreTable)
            ->join($submissionTable, "{$submissionTable}.id", '=', "{$scoreTable}.hackaton_case_submission_id")
            ->join($caseTable, "{$caseTable}.id", '=', "{$submissionTable}.hackaton_case_id")
            ->where("{$caseTable}.hackaton_id", $hackaton->id)
            ->whereNotNull("{$submissionTable}.team_id")
            ->where("{$scoreTable}.is_final", true)
            ->groupBy("{$submissionTable}.id", "{$submissionTable}.team_id")
            ->selectRaw(
                "{$submissionTable}.id as submission_id, {$submissionTable}.team_id as team_id, SUM({$scoreTable}.score) as submission_score, SUM({$scoreTable}.max_score) as submission_max_score"
            );

        $rows = DB::query()
            ->fromSub($submissionTotals, 'submission_totals')
            ->groupBy('team_id')
            ->orderByDesc(DB::raw('SUM(submission_score)'))
            ->selectRaw('team_id, SUM(submission_score) as total_score, SUM(submission_max_score) as max_score')
            ->get();

        if ($rows->isEmpty()) {
            return [];
        }

        $teams = Team::query()
            ->whereIn('id', $rows->pluck('team_id'))
            ->get(['id', 'title', 'image_url'])
            ->keyBy('id');

        /** @var list<array{team: Team|null, total_score: int, max_score: int, completion_percent: int}> $entries */
        $entries = [];
        foreach ($rows as $row) {
            $entries[] = $this->mapRow($row, $teams);
        }

        return $entries;
    }

    /**
     * @param  object{team_id: int|string|null, total_score: float|int|string, max_score: float|int|string}  $row
     * @param  Collection<int, Team>  $teams
     * @return array{team: Team|null, total_score: int, max_score: int, completion_percent: int}
     */
    private function mapRow(object $row, Collection $teams): array
    {
        $team = $teams->get((int) $row->team_id);
        $resolvedTeam = $team instanceof Team ? $team : null;

        $totalScore = (int) $row->total_score;
        $maxScore = (int) $row->max_score;

        return [
            'team' => $resolvedTeam,
            'total_score' => $totalScore,
            'max_score' => $maxScore,
            'completion_percent' => $maxScore > 0 ? (int) round(($totalScore / $maxScore) * 100) : 0,
        ];
    }
}
