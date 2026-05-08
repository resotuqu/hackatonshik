<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHackatonCaseScoreRequest;
use App\Models\Hackaton;
use App\Models\HackatonCase;
use App\Models\HackatonCaseScore;
use App\Models\HackatonCaseSubmission;
use Illuminate\Http\RedirectResponse;

class HackatonCaseScoreController extends Controller
{
    public function store(StoreHackatonCaseScoreRequest $request, Hackaton $hackaton): RedirectResponse
    {
        $isOrganizer = (int) $hackaton->user_id === (int) $request->user()->id;
        $isJudge = $hackaton->judges()->where('users.id', $request->user()->id)->exists();

        if (! $isOrganizer && ! $isJudge) {
            abort(403);
        }

        $validated = $request->validated();
        $submission = HackatonCaseSubmission::query()
            ->with('case')
            ->findOrFail($validated['hackaton_case_submission_id']);

        $case = HackatonCase::query()->find($submission->hackaton_case_id);
        abort_unless($case instanceof HackatonCase && (int) $case->hackaton_id === (int) $hackaton->id, 404);

        HackatonCaseScore::query()->updateOrCreate(
            ['hackaton_case_submission_id' => $submission->id],
            [
                'reviewed_by' => $request->user()->id,
                'score' => (int) $validated['score'],
                'max_score' => (int) ($validated['max_score'] ?? 100),
                'comment' => $validated['comment'] ?? null,
                'reviewed_at' => now(),
            ],
        );

        return back()->with('success', 'Оценка сохранена.');
    }
}
