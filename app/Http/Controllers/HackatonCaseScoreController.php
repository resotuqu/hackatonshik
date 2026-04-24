<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHackatonCaseScoreRequest;
use App\Models\Hackaton;
use App\Models\HackatonCaseScore;
use App\Models\HackatonCaseSubmission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class HackatonCaseScoreController extends Controller
{
    public function store(StoreHackatonCaseScoreRequest $request, Hackaton $hackaton): RedirectResponse
    {
        if ((int) $hackaton->user_id !== (int) $request->user()->id) {
            abort(403);
        }

        $validated = $request->validated();
        $submission = HackatonCaseSubmission::query()
            ->with('case')
            ->findOrFail($validated['hackaton_case_submission_id']);

        abort_unless((int) $submission->case->hackaton_id === (int) $hackaton->id, 404);
        Gate::authorize('update', $submission->case);

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
