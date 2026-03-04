<?php

use App\Models\Team;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\Attributes\Computed;

new #[Layout('layouts::app', ['title' => 'Мои хакатоны'])]
class extends Component {

    #[Computed]
    public function hackatons()
    {
        return \App\Models\Hackaton::query()->where('user_id', '=', Auth::user()->id)->get();
    }

    public function deleteHackaton($id) {
        $team = \App\Models\Hackaton::find($id);
        $team->delete();
    }

    public function editHackaton($id) {
        return redirect('/hackaton/' . $id . '/edit');
    }
};
?>

<div class="">
    <h3 class="text-3xl text-center">Ваши хакатоны</h3>

    <div class="grid grid-cols-3 gap-4 mt-6">
        @forelse($this->hackatons as $hackaton)
            <x-marycard class="card card-border">
                <div class="bg-white">
                    <img src="/uploads/{{$hackaton->image_url}}" class="object-contain w-full h-56 rounded-sm" alt="">
                </div>
                <div class="mt-2 space-y-2">
                    <p class="card-title">{{$hackaton->title}}</p>
                    <x-mary-card class="card card-border bg-base-200">
                        <p>Принимает участие: {{$hackaton->participantsCount()}} команд</p>
                        <p>Даты проведения:
                            {{$hackaton->start_at }} &DownLeftVectorBar; {{$hackaton->end_at}}</p>
                    </x-mary-card>
                </div>

                <x-slot:actions>
                    <x-marybutton class="btn-primary" label="Изменить" wire:click="editHackaton({{$hackaton->id}})" />
                    <x-marybutton class="btn-error" label="Удалить" wire:click="deleteHackaton({{$hackaton->id}})" />
                </x-slot:actions>

            </x-marycard>

            @empty

            <h4>У вас нет хакатонов :(</h4>

        @endforelse
    </div>

</div>
