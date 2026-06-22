<?php

declare(strict_types=1);

use App\Http\Controllers\JudgeExportController;
use App\Livewire\Pages\Judge\Dashboard as JudgeDashboard;
use App\Livewire\Pages\Judge\EvaluateSubmission as JudgeEvaluateSubmission;
use App\Livewire\Pages\Judge\HackatonShow as JudgeHackatonShow;
use App\Livewire\Pages\Judge\SubmissionList as JudgeSubmissionList;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'judge'])->group(function (): void {
    Route::get('/judge', JudgeDashboard::class)->name('judge.dashboard');
    Route::get('/judge/hackatons/{hackaton}', JudgeHackatonShow::class)->name('judge.hackatons.show');
    Route::get('/judge/hackatons/{hackaton}/scores/export', [JudgeExportController::class, 'scores'])->name('judge.hackatons.scores.export');
    Route::get('/judge/hackatons/{hackaton}/cases/{case}', JudgeSubmissionList::class)->name('judge.cases.submissions');
    Route::get('/judge/submissions/{submission}', JudgeEvaluateSubmission::class)->name('judge.submissions.evaluate');
});
