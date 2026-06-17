<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Hackaton;
use Illuminate\Http\RedirectResponse;

class HackatonWatchController extends Controller
{
    public function store(Hackaton $hackaton): RedirectResponse
    {
        $this->authorize('view', $hackaton);

        $user = auth()->user();

        if ($user === null) {
            abort(403);
        }

        $user->watchedHackatons()->syncWithoutDetaching([$hackaton->id]);

        return back()->with('success', 'Хакатон добавлен в закладки.');
    }

    public function destroy(Hackaton $hackaton): RedirectResponse
    {
        $this->authorize('view', $hackaton);

        auth()->user()?->watchedHackatons()->detach($hackaton->id);

        return back()->with('success', 'Хакатон удалён из закладок.');
    }
}
