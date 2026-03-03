<?php

use App\Models\Team;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\Attributes\Computed;

new #[Layout('layouts::app', ['title' => 'Мои команды'])]
class extends Component {

    #[Computed]
    public function teams()
    {
        return Team::query()->where('user_id', '=', Auth::user()->id)->get();
    }

    public function deleteTeam($id) {
        $team = Team::find($id);
        $team->delete();
    }

    public function editTeam($id) {
        return redirect('/teams/' . $id . '/edit');
    }
};
?>

<div class="">
    <h3 class="text-3xl text-center">Ваши команды</h3>

    <div class="grid grid-cols-3 gap-4 mt-6">
        @foreach($this->teams as $team)
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
                    <x-mary-button label="Удалить" class="btn-secondary" wire:click="deleteTeam({{$team->id}})" />
                </x-slot:actions>

            </x-mary-card>
        @endforeach
    </div>

</div>
