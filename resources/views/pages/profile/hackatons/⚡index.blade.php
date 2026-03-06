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
        return redirect('/hackatons/' . $id . '/edit');
    }

    public function participantsHackaton($id) {
        return redirect('/profile/hackatons/' . $id . '/participants');
    }
};
?>

<div class="">
    <h3 class="text-3xl text-center">Ваши хакатоны</h3>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-6">
        @forelse($this->hackatons as $hackaton)
            <x-marycard class="card card-border">
               <div class="overflow-hidden rounded-xl bg-base-200 aspect-[16/9]">
                        <img src="/uploads/{{$hackaton->image_url}}" class="w-full h-full object-cover" alt="{{$hackaton->title}}">
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
                    <x-marybutton class="btn-secondary" label="Участники" wire:click="participantsHackaton({{$hackaton->id}})" />
                    <x-marybutton class="btn-error" label="Удалить" wire:click="deleteHackaton({{$hackaton->id}})" />
                </x-slot:actions>

            </x-marycard>

            @empty

            <h4>У вас нет хакатонов :(</h4>

        @endforelse
    </div>

</div>
