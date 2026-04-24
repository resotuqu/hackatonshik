<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHackatonCaseRequest;
use App\Models\Hackaton;
use App\Models\HackatonCase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class HackatonCaseController extends Controller
{
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
