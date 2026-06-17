<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Hackaton;
use App\Models\HackatonCaseScore;
use App\Models\HackatonCaseSubmission;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class JudgeExportController extends Controller
{
    public function scores(Hackaton $hackaton): StreamedResponse
    {
        $user = Auth::user();
        abort_unless($user !== null, 403);
        abort_unless($hackaton->isJudge($user), 403);

        $userId = (int) $user->id;
        $submissionIds = HackatonCaseSubmission::query()
            ->whereHas('case', fn ($q) => $q->where('hackaton_id', $hackaton->id))
            ->pluck('id');

        $scores = HackatonCaseScore::query()
            ->where('reviewed_by', $userId)
            ->whereIn('hackaton_case_submission_id', $submissionIds)
            ->with([
                'submission.case:id,title',
                'submission.team:id,title',
            ])
            ->orderBy('hackaton_case_submission_id')
            ->get();

        $filename = "judge_hackaton_{$hackaton->id}_scores.csv";

        return response()->streamDownload(function () use ($scores): void {
            $stream = fopen('php://output', 'wb');
            fputcsv($stream, ['submission_id', 'case', 'team', 'score', 'max_score', 'is_final', 'general_comment']);

            foreach ($scores as $score) {
                fputcsv($stream, [
                    $score->hackaton_case_submission_id,
                    $score->submission?->case?->title,
                    $score->submission?->team?->title,
                    $score->score,
                    $score->max_score,
                    $score->is_final ? '1' : '0',
                    $score->general_comment,
                ]);
            }

            fclose($stream);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
