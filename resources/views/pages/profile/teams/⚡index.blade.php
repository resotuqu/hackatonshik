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
            <div class="bg-slate-600 flex flex-col px-4 py-2 rounded-sm text-white">
                <div class="bg-white">
                    <img src="{{$team->image_url}}" class="object-contain w-full h-32 rounded-sm" alt="">
                </div>
                <div class="mt-2 space-y-2">
                    <p>{{$team->title}}</p>
                    <p>Пользователь: {{$team->user->nickname}}</p>
                    <div class="bg-slate-500 px-2 py-2 rounded-sm">
                        <p>{{$team->hackaton->title}}</p>
                        <p>Даты проведения:
                            {{$team->hackaton->start_at }} &DownLeftVectorBar; {{$team->hackaton->end_at}}</p>
                    </div>
                </div>
                <div class="flex flex-row space-x-2 mt-4">
                    <button wire:click="deleteTeam({{$team->id}})" class="cursor-pointer px-4 py-2 bg-red-500 rounded-sm">Удалить</button>
                    <button wire:click="editTeam({{$team->id}})" class="cursor-pointer px-4 py-2 bg-green-500 rounded-sm">Изменить</button>
                </div>
            </div>
        @endforeach
    </div>

</div>
