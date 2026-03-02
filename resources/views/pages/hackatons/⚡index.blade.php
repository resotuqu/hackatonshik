<?php

use App\Models\Hackaton;
use App\Models\Team;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;

new #[Layout('layouts::app', ['title' => "Хакатоны"])]
class extends Component {
    use \Livewire\WithPagination;
    use \Livewire\WithoutUrlPagination;

    #[Computed]
    public function hackatons()
    {
        return Hackaton::query()
            ->when($this->q != '', function ($query) {
                $query->where('title', 'like', '%' . $this->q . '%');
            })
            ->when($this->start_at != '', function ($query) {
                $query->where('start_at', '>=', $this->start_at);
            })
            ->orderBy('id', 'desc')
            ->paginate(5);
    }

    public function search()
    {
        $this->resetPage();
    }

    public $q = '';
    public $start_at;
}
?>

<div class="flex flex-row h-full justify-between gap-2">

    <div class="w-1/3 min-h-full bg-slate-600 px-4 py-6 text-white rounded-sm">
        <h5 class="text-2xl">Фильтрация</h5>
        <form wire:submit="search" class="text-black">
            @csrf
            <x-livewire-form-input type="text" label="Наименование" name="q" model="q"/>

            <x-livewire-form-input type="date" model="start_at" name="start_at" label="Начало от"  />

            <div class="mt-4 flex flex-row space-x-2">
                <button type="submit"
                        class="px-4 py-2 text-white bg-blue-500 hover:bg-blue-400 rounded-sm cursor-pointer">Искать
                </button>


                <button type="button"
                        class="px-4 py-2 text-white bg-red-500 hover:bg-red-400 rounded-sm cursor-pointer">
                    <a href="/hackatons">Сбросить</a></button>
            </div>
        </form>
    </div>

    <div class="w-2/3">
        <div class="grid grid-cols-2 gap-4">
            @foreach($this->hackatons as $hackaton)
                <div class="bg-slate-600 flex flex-col px-4 py-2 rounded-sm text-white">
                    <div class="bg-white">
                        <img src="/uploads/{{$hackaton->image_url}}" class="object-contain w-full h-56 rounded-sm" alt="">
                    </div>
                    <div class="mt-2 space-y-2">
                        <p>{{$hackaton->title}}</p>
                        <div class="bg-slate-500 px-2 py-2 rounded-sm">
                            <p>Принимает участие: {{$hackaton->participantsCount()}} команд</p>
                            <p>Даты проведения:
                                {{$hackaton->start_at }} &DownLeftVectorBar; {{$hackaton->end_at}}</p>
                        </div>
                    </div>
                    <a href="/hackatons/{{$hackaton->id}}">Подробнее</a>
                </div>
            @endforeach
        </div>
        {{$this->hackatons->links(data: ['scrollTo' => false])}}
    </div>

</div>
