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

    <x-marycard class="card card-border w-1/3 h-fit">
        <h5 class="text-2xl">Фильтрация</h5>
        <x-maryform wire:submit="search">
            @csrf
            <x-mary-input label="Наименование" wire:model="q"/>

            <x-marydatetime wire:model="start_at" label="Начало от"  />

            <x-slot:actions>
                <x-mary-button type="submit" class="btn-primary">Искать</x-mary-button>
                <a href="/hackatons"><x-mary-button label="Сбросить" class="btn-secondary"></x-mary-button></a>
            </x-slot:actions>
        </x-maryform>
    </x-marycard>

    <div class="w-2/3">
        <div class="grid grid-cols-2 gap-4">
            @foreach($this->hackatons as $hackaton)
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
                        <a href="/hackatons/{{$hackaton->id}}"><x-mary-button class="btn-primary" label="Подробнее"/></a>
                    </x-slot:actions>

                </x-marycard>
            @endforeach
        </div>
        {{$this->hackatons->links(data: ['scrollTo' => false])}}
    </div>

</div>
