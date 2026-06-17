<?php

namespace App\Livewire\Pages\Profile\Teams;

use App\Enums\ApplicationStatus;
use App\Models\Team;
use App\Models\TeamApplication;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Index extends Component
{
    #[Computed]
    public function teams()
    {
        return Team::query()
            ->where('user_id', Auth::id())
            ->with(['hackaton', 'roles'])
            ->withCount([
                'roles as roles_count',
                'roles as empty_roles_count' => fn ($q) => $q->whereNull('user_id'),
            ])
            ->get();
    }

    /**
     * @return Collection<int, TeamApplication>
     */
    #[Computed]
    public function pendingTeamRoleApplications()
    {
        return TeamApplication::query()
            ->where('user_id', Auth::id())
            ->where('status', ApplicationStatus::PENDING)
            ->with(['teamRole.team.hackaton', 'teamRole.role'])
            ->latest()
            ->get();
    }

    public $deleteTeamModal = false;

    public $deleteTeamId = null;

    public $deleteTeamTitle = null;

    public function showDeleteTeamModal($team_id): void
    {
        $team = Team::query()->where('id', $team_id)->where('user_id', Auth::id())->first();
        if (! $team) {
            return;
        }
        $this->deleteTeamId = $team->id;
        $this->deleteTeamTitle = $team->title;
        $this->deleteTeamModal = true;
    }

    public function deleteTeam(): void
    {
        if (! $this->deleteTeamId) {
            return;
        }
        $team = Team::query()->where('id', $this->deleteTeamId)->where('user_id', Auth::id())->first();
        if ($team) {
            $team->delete();
        }
        $this->deleteTeamId = null;
        $this->deleteTeamTitle = null;
        $this->deleteTeamModal = false;
    }

    public function editTeam($id)
    {
        return redirect('/teams/'.$id.'/edit');
    }

    public function updatedDeleteTeamModal(mixed $value): void
    {
        if (! $value) {
            $this->deleteTeamId = null;
            $this->deleteTeamTitle = null;
        }
    }

    #[Layout('layouts::app', ['title' => 'Мои команды'])]
    public function render()
    {
        return view('pages.profile.teams.index');
    }
}
