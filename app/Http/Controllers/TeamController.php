<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\TeamRole;
use Illuminate\Http\RedirectResponse;

class TeamController extends Controller
{
    public function destroyParticipant(Team $team, TeamRole $teamRole): RedirectResponse
    {
        $this->authorize('update', $team);

        if ($teamRole->team_id !== $team->id) {
            abort(404);
        }

        if ($teamRole->user_id === null) {
            return back()->with('warning', 'В этой роли нет участника.');
        }

        $teamRole->update([
            'user_id' => null,
        ]);

        return back()->with('success', 'Участник удален из команды.');
    }
}
