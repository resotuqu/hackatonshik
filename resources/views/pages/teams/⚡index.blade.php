<?php

use App\Models\Hackaton;
use App\Models\Team;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;

new #[Layout('layouts::app', ['title' => "Команды"])]
class extends Component {
    use \Livewire\WithPagination;
    use \Livewire\WithoutUrlPagination;

    #[Computed]
    public function hackatons()
    {
        return Hackaton::query()
            ->where('is_public', '=', '1')
            ->get();
    }

    #[Computed]
    public function teams()
    {
        return Team::query()
            ->when($this->q != '', function ($query) {
                $query->where('title', 'like', '%' . $this->q . '%');
            })
            ->when($this->hackaton_id != '0', function ($query) {
                $query->where('teams.hackaton_id', '=', $this->hackaton_id);
            })
            ->when($this->start_from != '', function ($query) {
                $query->whereHas('hackaton', function($q) {
                    $q->where('start_at', '>=', $this->start_from);
                });
            })
            ->orderBy('id', 'desc')
            ->paginate(5);
    }

    public function search()
    {
        $this->resetPage();
    }

    public $q = '';
    public $hackaton_id = '0';
    public $start_from;
}
?>

<div class="flex flex-row h-full justify-between gap-2">

    <div class="w-1/3 min-h-full bg-slate-600 px-4 py-6 text-white rounded-sm">
        <h5 class="text-2xl">Фильтрация</h5>
        <form wire:submit="search" class="text-black">
            @csrf
            <x-livewire-form-input type="text" label="Наименование" name="q" model="q"/>

            {{--    HackatonId    --}}
            <div class="flex flex-col mt-4 w-full">
                <label for="hackaton_id" class="text-white">Хакатон</label>
                <select id="hackaton_id" wire:model="hackaton_id" class="bg-white rounded-sm py-2 mt-2">
                    <option value="0">Без разницы</option>

                    @foreach($this->hackatons as $hackaton)
                        <option value="{{$hackaton->id}}">{{$hackaton->title}}</option>
                    @endforeach
                </select>
                @error('hackaton_id')
                <p class="mt-2 text-red-500">{{$message}}</p>
                @enderror
            </div>

            <x-livewire-form-input type="date" model="start_from" name="start_from" label="Начало от"  />

            <div class="mt-4 flex flex-row space-x-2">
                <button type="submit"
                        class="px-4 py-2 text-white bg-blue-500 hover:bg-blue-400 rounded-sm cursor-pointer">Искать
                </button>


                <button type="button"
                        class="px-4 py-2 text-white bg-red-500 hover:bg-red-400 rounded-sm cursor-pointer">
                    <a href="/teams">Сбросить</a></button>
            </div>
        </form>
    </div>

    <div class="w-2/3">
        <div class="grid grid-cols-2 gap-4">
            @foreach($this->teams as $team)
                <div class="bg-slate-600 flex flex-col px-4 py-2 rounded-sm text-white">
                    <div class="bg-white">
                        <img src="/uploads/{{$team->image_url}}" class="object-contain w-full h-32 rounded-sm" alt="">
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
                </div>
            @endforeach
        </div>
        {{$this->teams->links(data: ['scrollTo' => false])}}
    </div>

</div>
