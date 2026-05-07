<?php

namespace App\Http\Controllers;

use App\Enums\ApplicationStatus;
use App\Http\Requests\StoreHackatonCaseRequest;
use App\Models\Hackaton;
use App\Models\HackatonCase;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class HackatonCaseController extends Controller
{
    public function join(Request $request, Hackaton $hackaton, HackatonCase $case): RedirectResponse
    {
        abort_unless($case->hackaton_id === $hackaton->id, 404);

        $validated = $request->validate([
            'team_id' => ['required', 'exists:teams,id'],
        ]);

        $team = Team::findOrFail($validated['team_id']);

        // Authorization: user must be able to manage this team
        Gate::authorize('update', $team);

        if ($team->hackaton_id !== $hackaton->id) {
            return back()->with('error', 'Эта команда не зарегистрирована на данный хакатон.');
        }
        // Team must be approved for this hackathon
        $isApproved = $hackaton->applications()
            ->where('team_id', $team->id)
            ->where('status', ApplicationStatus::ACCEPTED)
            ->exists();

        if (! $isApproved) {
            return back()->with('error', 'Команда должна быть одобрена для участия в хакатоне.');
        }

        $team->update(['hackaton_case_id' => $case->id]);

        return back()->with('success', "Команда «{$team->title}» успешно присоединилась к кейсу «{$case->title}».");
    }

    public function store(StoreHackatonCaseRequest $request, Hackaton $hackaton): RedirectResponse
    {
        Gate::authorize('create', [HackatonCase::class, $hackaton]);

        $validated = $request->validated();
        $nextOrder = (int) $hackaton->cases()->max('sort_order') + 1;

        $hackaton->cases()->create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'sort_order' => $nextOrder,
            'is_published' => (bool) ($validated['is_published'] ?? false),
            'publish_at' => $validated['publish_at'] ?? null,
            'deadline_at' => $validated['deadline_at'] ?? null,
        ]);

        return back()->with('success', 'Кейс добавлен.');
    }

    public function destroy(Hackaton $hackaton, HackatonCase $case): RedirectResponse
    {
        abort_unless($case->hackaton_id === $hackaton->id, 404);
        Gate::authorize('delete', $case);

        $case->delete();

        return back()->with('success', 'Кейс удалён.');
    }
}
