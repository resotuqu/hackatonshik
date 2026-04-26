<?php

namespace App\Http\Controllers;

use App\Models\Hackaton;
use App\Models\HackatonCaseSubmission;
use App\Models\Team;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

class ParticipantHackatonHubController extends Controller
{
    public function show(Hackaton $hackaton): View
    {
        $user = auth()->user();

        $teams = Team::query()
            ->where('hackaton_id', $hackaton->id)
            ->where(function (Builder $query) use ($user): void {
                $query
                    ->where('user_id', $user->id)
                    ->orWhereHas('roles', fn (Builder $rolesQuery) => $rolesQuery->where('user_id', $user->id));
            })
            ->with(['roles.user:id,fio', 'hackatonApplications'])
            ->get();

        if ($teams->isEmpty()) {
            abort(403);
        }

        $teamIds = $teams->pluck('id');
        $applications = $hackaton->applications()
            ->whereIn('team_id', $teamIds)
            ->with('team:id,title')
            ->latest()
            ->get();

        $submissions = HackatonCaseSubmission::query()
            ->whereIn('team_id', $teamIds)
            ->whereHas('case', fn (Builder $query) => $query->where('hackaton_id', $hackaton->id))
            ->with(['case:id,hackaton_id,title,deadline_at', 'score'])
            ->latest('submitted_at')
            ->get();

        $requiredDocuments = $hackaton->documents()
            ->select(['id', 'name'])
            ->withCount(['usersFiles as uploaded_count' => fn (Builder $query) => $query->where('user_id', $user->id)])
            ->get();

        $upcomingCases = $hackaton->cases()
            ->whereNotNull('deadline_at')
            ->where('deadline_at', '>', now())
            ->orderBy('deadline_at')
            ->limit(5)
            ->get(['id', 'title', 'deadline_at']);

        return view('pages.profile.hackatons.hub', compact(
            'hackaton',
            'teams',
            'applications',
            'submissions',
            'requiredDocuments',
            'upcomingCases'
        ));
    }
}
