<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTeamRequest;
use App\Http\Requests\UpdateTeamRequest;
use App\Models\Team;
use App\Models\TeamRole;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class TeamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTeamRequest $request)
    {
        //
    }

    public function show(Team $team)
    {
        $team->loadShowRelations();

        return view('pages.teams.show', compact('team'));
    }

    public function destroyParticipant(Team $team, TeamRole $teamRole): RedirectResponse
    {
        if (Auth::id() !== $team->user_id) {
            abort(403);
        }

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

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Team $team)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTeamRequest $request, Team $team)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Team $team)
    {
        //
    }
}
