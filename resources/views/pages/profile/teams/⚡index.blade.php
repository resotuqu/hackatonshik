<?php

use App\Models\Team;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\Attributes\Computed;

new #[Layout('layouts::app', ['title' => 'Мои команды'])]
class extends Component {

    use \Mary\Traits\Toast;

    #[Computed]
    public function teams()
    {
        return Team::query()->where('user_id', '=', Auth::user()->id)->get();
    }



    public $deleteTeamModal = false;
    public $deleteTeamId = null;
    public function showDeleteTeamModal($team_id)
    {
        $this->deleteTeamId = $team_id;
        $this->deleteTeamModal = true;
    }

    public function deleteTeam() {
        $team = Team::find($this->deleteTeamId);
        $team->delete();
        $this->deleteTeamId = null;
        $this->deleteTeamModal = false;
    }



    public function editTeam($id) {
        return redirect('/teams/' . $id . '/edit');
    }

};
?>

<div class="">
    <h3 class="text-3xl text-center">Ваши команды</h3>


    <x-mary-modal wire:model="deleteTeamModal" title="Подтверждение" class="backdrop-blur">
        Вы действительно хотите удалить команду ?

        <x-slot:actions>
            <x-mary-button class="btn-error" label="Да" wire:click="deleteTeam" />
            <x-mary-button label="Нет" @click="$wire.deleteTeamModal = false" />
        </x-slot:actions>
    </x-mary-modal>

    <div class="grid grid-cols-3 gap-4 mt-6">
        @forelse($this->teams as $team)
            <x-mary-card title="{{$team->title}}" class="card card-border">
                <x-slot:figure>
                    <img src="/uploads/{{$team->image_url}}" class="object-contain w-full h-32 rounded-sm" alt="">
                </x-slot:figure>
                <x-mary-card class="card card-border bg-base-300">
                    <p>{{$team->hackaton->title}}</p>
                    <p>Даты проведения:
                        {{$team->hackaton->start_at }} &DownLeftVectorBar; {{$team->hackaton->end_at}}</p>
                </x-mary-card>

                <div class="mt-2">
                    <x-marybadge value="Количество ролей: {{$team->roles->count()}}" class="badge-neutral" />
                    <x-marybadge value="Свободно ролей: {{$team->emptyRoles()}}" class="badge-neutral" />
                </div>

                <x-slot:actions>
                    <x-mary-button label="Изменить" class="btn-primary" wire:click="editTeam({{$team->id}})" />
                    <x-mary-button label="Удалить" class="btn-secondary" wire:click="showDeleteTeamModal({{$team->id}})" />
                </x-slot:actions>

            </x-mary-card>

        @empty

            <h4>У вас нет команд :(</h4>

        @endforelse
    </div>

</div>
