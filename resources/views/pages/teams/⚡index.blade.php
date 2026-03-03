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
        $hackatons = [];
        $hackatons[] = [
            'id' => '0',
            'name' => 'Без разницы',
        ];
        foreach (Hackaton::query()
                     ->where('is_public', '=', '1')
                     ->get() as $hackaton) {
            $hackatons[] = [
                'id' => $hackaton->id,
                'name' => $hackaton->title,
            ];
        }
        return $hackatons;
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

    <x-mary-card class="w-1/3 min-h-full card card-border">
        <h5 class="text-2xl">Фильтрация</h5>
        <x-maryform wire:submit="search">

            {{--HackatonTitle--}}
            <x-mary-input label="Наименование" wire:model="q"/>

            {{--    HackatonId    --}}
            <x-maryselect wire:model="hackaton_id" :options="$this->hackatons" label="Хакатон" />

            {{--HackatonStartFrom--}}
            <x-marydatetime wire:model="start_from" label="Начало от"  />

            <div class="mt-4 flex flex-row space-x-2">
                <x-mary-button type="submit" class="btn-secondary">Искать</x-mary-button>
                <a href="/teams"><x-mary-button type="button" class="btn-error">Сбросить</x-mary-button></a>
            </div>

        </x-maryform>
    </x-mary-card>

    <div class="w-2/3">
        <div class="grid grid-cols-2 gap-4">
            @foreach($this->teams as $team)
                <x-mary-card title="{{$team->title}}" class="card card-border">
                    <x-slot:figure>
                        <img src="/uploads/{{$team->image_url}}" class="object-contain w-full h-32 rounded-sm" alt="">
                    </x-slot:figure>
                    <x-mary-card class="card card-border bg-base-300">
                        <p>Пользователь: {{$team->user->nickname}}</p>
                        <p>{{$team->hackaton->title}}</p>
                        <p>Даты проведения:
                            {{$team->hackaton->start_at }} &DownLeftVectorBar; {{$team->hackaton->end_at}}</p>
                    </x-mary-card>

                    <div class="mt-2">
                        <x-marybadge value="Количество ролей: {{$team->roles->count()}}" class="badge-neutral" />
                        <x-marybadge value="Свободно ролей: {{$team->emptyRoles()}}" class="badge-neutral" />
                    </div>

                    <x-slot:actions>
                        <a href="/teams/{{$team->id}}"><x-mary-button label="Подробнее" class="btn-primary" /></a>
                    </x-slot:actions>

                </x-mary-card>
            @endforeach
        </div>
        {{$this->teams->links(data: ['scrollTo' => false])}}
    </div>

</div>
