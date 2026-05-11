<?php

declare(strict_types=1);

namespace App\Actions\Hackaton;

use App\Enums\ApplicationStatus;
use App\Models\Hackaton;
use App\Models\HackatonCase;
use App\Models\HackatonCaseScore;
use App\Models\HackatonCaseSubmission;
use Illuminate\Support\Facades\DB;

final class BuildHackatonOrganizationMetrics
{
    /**
     * @return array<string, int>
     */
    public function handle(Hackaton $hackaton): array
    {
        $statusCounts = $hackaton->applications()
            ->selectRaw('status, COUNT(*) as c')
            ->groupBy('status')
            ->pluck('c', 'status');

        $submissionTable = (new HackatonCaseSubmission)->getTable();
        $caseTable = (new HackatonCase)->getTable();
        $scoreTable = (new HackatonCaseScore)->getTable();

        $submissionStats = DB::table($submissionTable)
            ->join($caseTable, "{$caseTable}.id", '=', "{$submissionTable}.hackaton_case_id")
            ->leftJoin($scoreTable, "{$scoreTable}.hackaton_case_submission_id", '=', "{$submissionTable}.id")
            ->where("{$caseTable}.hackaton_id", $hackaton->id)
            ->selectRaw('COUNT(*) as submissions_total, SUM(CASE WHEN '.$scoreTable.'.id IS NOT NULL THEN 1 ELSE 0 END) as submissions_scored')
            ->first();

        $submissionsTotal = (int) ($submissionStats->submissions_total ?? 0);
        $submissionsScored = (int) ($submissionStats->submissions_scored ?? 0);

        $metrics = [
            'applications_total' => (int) $statusCounts->sum(),
            'applications_pending' => (int) ($statusCounts[ApplicationStatus::PENDING->value] ?? 0),
            'applications_accepted' => (int) ($statusCounts[ApplicationStatus::ACCEPTED->value] ?? 0),
            'applications_rejected' => (int) ($statusCounts[ApplicationStatus::REJECTED->value] ?? 0),
            'submissions_total' => $submissionsTotal,
            'submissions_scored' => $submissionsScored,
        ];

        $metrics['submissions_scored_percent'] = $metrics['submissions_total'] > 0
            ? (int) round(($metrics['submissions_scored'] / $metrics['submissions_total']) * 100)
            : 0;

        return $metrics;
    }

    /**
     * @return array<string, int>
     */
    public function empty(): array
    {
        return [
            'applications_total' => 0,
            'applications_pending' => 0,
            'applications_accepted' => 0,
            'applications_rejected' => 0,
            'submissions_total' => 0,
            'submissions_scored' => 0,
            'submissions_scored_percent' => 0,
        ];
    }
}
