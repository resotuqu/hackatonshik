<?php

use App\Enums\ApplicationStatus;
use App\Models\Team;
use App\Models\TeamApplication;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::app', ['title' => 'Мои команды'])]
    class extends Component {

    use \Mary\Traits\Toast;

    #[Computed]
    public function teams()
    {
        return Team::query()->where('user_id', '=', Auth::user()->id)->get();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, TeamApplication>
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
    public function showDeleteTeamModal($team_id)
    {
        $this->deleteTeamId = $team_id;
        $this->deleteTeamModal = true;
    }

    public function deleteTeam()
    {
        $team = Team::find($this->deleteTeamId);
        $team->delete();
        $this->deleteTeamId = null;
        $this->deleteTeamModal = false;
    }



    public function editTeam($id)
    {
        return redirect('/teams/' . $id . '/edit');
    }

};
?>

<div class="">
    <div class="text-sm breadcrumbs">
        <ul>
            <li><a href="/">Главная</a></li>
            <li><a href="/profile">Профиль</a></li>
            <li class="opacity-70">Мои команды</li>
        </ul>
    </div>

    <h3 class="text-3xl text-center">Ваши команды</h3>

    <section id="pending-team-role-applications" class="mx-auto mt-8 max-w-3xl rounded-xl border border-base-200 bg-base-100 p-4 shadow-sm" aria-labelledby="pending-apps-heading">
        <h4 id="pending-apps-heading" class="text-lg font-semibold">Заявки на роли в командах</h4>
        @if ($this->pendingTeamRoleApplications->isEmpty())
            <p class="mt-2 text-sm text-base-content/70">Нет заявок на рассмотрении.</p>
        @else
            <ul class="mt-3 space-y-3">
                @foreach ($this->pendingTeamRoleApplications as $application)
                    @php
                        $team = $application->teamRole?->team;
                        $role = $application->teamRole?->role;
                        $hackatonTitle = $team?->hackaton?->title;
                    @endphp
                    <li class="rounded-lg border border-base-200 p-3 text-sm">
                        <p>
                            <span class="text-base-content/70">Команда:</span>
                            @if ($team)
                                <a href="{{ url('/teams/'.$team->id) }}" class="link link-primary font-medium">{{ $team->title }}</a>
                            @else
                                <span class="font-medium">—</span>
                            @endif
                        </p>
                        @if ($role)
                            <p class="mt-1"><span class="text-base-content/70">Роль:</span> {{ $role->name }}</p>
                        @elseif ($application->teamRole?->title)
                            <p class="mt-1"><span class="text-base-content/70">Роль:</span> {{ $application->teamRole->title }}</p>
                        @endif
                        @if ($hackatonTitle)
                            <p class="mt-1"><span class="text-base-content/70">Хакатон:</span> {{ $hackatonTitle }}</p>
                        @endif
                        <p class="mt-2"><span class="badge badge-warning badge-sm">{{ $application->status->label() }}</span></p>
                    </li>
                @endforeach
            </ul>
        @endif
    </section>

    <x-mary-modal wire:model="deleteTeamModal" title="Подтверждение" class="backdrop-blur">
        Вы действительно хотите удалить команду ?

        <x-slot:actions>
            <x-mary-button class="btn-error" label="Да" wire:click="deleteTeam" />
            <x-mary-button label="Нет" @click="$wire.deleteTeamModal = false" />
        </x-slot:actions>
    </x-mary-modal>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-6">
        @forelse($this->teams as $team)
            <x-mary-card title="{{ $team->title }}" class="card card-border h-full">
                <div class="flex grow flex-col space-y-2">
                    <div class="overflow-hidden rounded-xl bg-base-200 aspect-video">
                        <img src="/uploads/{{ $team->image_url }}" class="w-full h-full object-cover" alt="{{ $team->title }}">
                    </div>
                    <x-mary-card class="card card-border bg-base-300">
                        <p>{{ $team->hackaton->title }}</p>
                        <p>
                            Даты проведения:
                            {{ Carbon::parse($team->hackaton->start_at)->format('d.m.Y H:i') }} &DownLeftVectorBar;
                            {{ Carbon::parse($team->hackaton->end_at)->format('d.m.Y H:i') }}
                        </p>
                    </x-mary-card>

                    <div class="mt-1 flex flex-wrap gap-2">
                        <x-marybadge value="Количество ролей: {{ $team->roles->count() }}" class="badge-neutral" />
                        <x-marybadge value="Свободно ролей: {{ $team->emptyRoles() }}" class="badge-neutral" />
                    </div>
                </div>

                <x-slot:actions class="mt-auto pt-2">
                    <a href="/teams/{{ $team->id }}">
                        <x-mary-button label="Просмотреть" class="btn-ghost" />
                    </a>
                    <x-mary-button label="Изменить" class="btn-primary" wire:click="editTeam({{ $team->id }})" />
                    <x-mary-button label="Удалить" class="btn-secondary" wire:click="showDeleteTeamModal({{ $team->id }})" />
                </x-slot:actions>

            </x-mary-card>

        @empty

            <h4>У вас нет команд :(</h4>

        @endforelse
    </div>

</div>