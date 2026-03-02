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
        @foreach($this->hackatons as $hackaton)
            <div class="bg-slate-600 flex flex-col px-4 py-2 rounded-sm text-white">
                <div class="bg-white">
                    <img src="/uploads/{{$hackaton->image_url}}" class="object-contain w-full h-32 rounded-sm" alt="">
                </div>
                <div class="mt-2 space-y-2">
                    <p>{{$hackaton->title}}</p>
                    <div class="bg-slate-500 px-2 py-2 rounded-sm">
                        <p>Даты проведения:
                            {{$hackaton->start_at }} &DownLeftVectorBar; {{$hackaton->end_at}}</p>
                    </div>
                    <div class="bg-slate-500 px-2 py-2 rounded-sm">
                        <p>Перечень документов:</p>
                        @foreach($hackaton->documents as $document)
                            <p><a class="text-blue-800 hover:underline" href="{{$document->file_url}}">{{$document->name}}</a></p>
                        @endforeach
                    </div>
                </div>
                <div class="flex flex-row space-x-2 mt-4">
                    <button wire:click="deleteHackaton({{$hackaton->id}})" class="cursor-pointer px-4 py-2 bg-red-500 rounded-sm">Удалить</button>
                    <button wire:click="editHackaton({{$hackaton->id}})" class="cursor-pointer px-4 py-2 bg-green-500 rounded-sm">Изменить</button>
                </div>
            </div>
        @endforeach
    </div>

</div>
