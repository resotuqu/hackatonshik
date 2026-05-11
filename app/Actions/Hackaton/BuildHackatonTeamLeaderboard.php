<?php

declare(strict_types=1);

namespace App\Actions\Hackaton;

use App\Models\Hackaton;
use App\Models\HackatonCase;
use App\Models\HackatonCaseScore;
use App\Models\HackatonCaseSubmission;
use App\Models\Team;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class BuildHackatonTeamLeaderboard
{
    /**
     * @return Collection<int, array{team: ?Team, total_score: int, max_score: int, completion_percent: int}>
     */
    public function handle(Hackaton $hackaton): Collection
    {
        $submissionTable = (new HackatonCaseSubmission)->getTable();
        $caseTable = (new HackatonCase)->getTable();
        $scoreTable = (new HackatonCaseScore)->getTable();

        $rows = DB::table($submissionTable)
            ->join($caseTable, "{$caseTable}.id", '=', "{$submissionTable}.hackaton_case_id")
            ->join($scoreTable, "{$scoreTable}.hackaton_case_submission_id", '=', "{$submissionTable}.id")
            ->where("{$caseTable}.hackaton_id", $hackaton->id)
            ->whereNotNull("{$submissionTable}.team_id")
            ->where("{$scoreTable}.is_final", true)
            ->groupBy("{$submissionTable}.team_id")
            ->orderByDesc(DB::raw('SUM('.$scoreTable.'.score)'))
            ->selectRaw(
                "{$submissionTable}.team_id as team_id, SUM({$scoreTable}.score) as total_score, SUM({$scoreTable}.max_score) as max_score"
            )
            ->get();

        if ($rows->isEmpty()) {
            return collect();
        }

        $teams = Team::query()
            ->whereIn('id', $rows->pluck('team_id'))
            ->get(['id', 'title', 'image_url'])
            ->keyBy('id');

        return $rows->map(
            /** @param object{team_id: int|string|null, total_score: float|int|string, max_score: float|int|string} $row */
            fn (object $row): array => $this->mapRow($row, $teams)
        )->values();
    }

    /**
     * @param  object{team_id: int|string|null, total_score: float|int|string, max_score: float|int|string}  $row
     * @param  Collection<int, Team>  $teams
     * @return array{team: ?Team, total_score: int, max_score: int, completion_percent: int}
     */
    private function mapRow(object $row, Collection $teams): array
    {
        $team = $teams->get((int) $row->team_id);
        if (! $team instanceof Team) {
            $team = null;
        }

        $totalScore = (int) $row->total_score;
        $maxScore = (int) $row->max_score;

        return [
            'team' => $team,
            'total_score' => $totalScore,
            'max_score' => $maxScore,
            'completion_percent' => $maxScore > 0 ? (int) round(($totalScore / $maxScore) * 100) : 0,
        ];
    }
}
