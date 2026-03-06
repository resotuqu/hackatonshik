<?php

use App\Models\Hackaton;
use App\Models\Role;
use App\Models\Skill;
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
            'name' => 'Любой',
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
            ->with(['user', 'hackaton', 'roles'])
            ->whereHas('roles', function ($query) {
                $query->whereNull('user_id');
            })
            ->when($this->q != '', function ($query) {
                $query->where('title', 'like', '%' . $this->q . '%');
            })
            ->when($this->hackaton_id != '0', function ($query) {
                $query->where('teams.hackaton_id', '=', $this->hackaton_id);
            })
            ->when($this->role_id != '0', function ($query) {
                $query->whereHas('roles', function ($q) {
                    $q->where('role_id', '=', $this->role_id);
                });
            })
            ->when(!empty($this->skills), function ($query) {
                $query->whereHas('roles.skills', function ($q) {
                    $q->whereIn('skills.id', $this->skills);
                });
            })
            ->when($this->start_from != '', function ($query) {
                $query->whereHas('hackaton', function($q) {
                    $q->where('start_at', '>=', $this->start_from);
                });
            })
            ->orderBy('id', 'desc')
            ->paginate(6);
    }

    #[Computed]
    public function skillsData()
    {
        return Skill::query()->orderBy('name')->get();
    }

    #[Computed]
    public function rolesData()
    {
        $roles = [];
        $roles[] = [
            'id' => '0',
            'name' => 'Любая',
        ];
        foreach (Role::query()->orderBy('name')->get() as $role) {
            $roles[] = [
                'id' => $role->id,
                'name' => $role->name,
            ];
        }
        return $roles;
    }

    public function search()
    {
        $this->resetPage();
    }

    public $q = '';
    public $hackaton_id = '0';
    public $role_id = '0';
    public $skills = [];
    public $start_from;
}
?>

<div class="flex flex-col lg:flex-row h-full justify-between gap-4">

    <x-mary-card class="w-full lg:w-1/3 h-fit card card-border">
        <h5 class="text-2xl">Фильтрация</h5>
        <x-maryform wire:submit="search">

            {{--HackatonTitle--}}
            <x-mary-input label="Наименование" placeholder="Введите название..." wire:model="q"/>

            {{--    HackatonId    --}}
            <x-maryselect wire:model="hackaton_id" :options="$this->hackatons" label="Хакатон" />

            {{-- Role --}}
            <x-maryselect wire:model="role_id" :options="$this->rolesData" label="Роль" />

            {{-- Skills --}}
            <x-marychoices-offline
                label="Навыки"
                wire:model="skills"
                :options="$this->skillsData"
                placeholder="Выберите навыки..."
                clearable
                searchable />

            {{--HackatonStartFrom--}}
            <x-marydatetime wire:model="start_from" label="Начало от"  />

            <x-slot:actions>
                <x-mary-button type="submit" class="btn-primary">Искать</x-mary-button>
                <a href="/teams"><x-mary-button type="button" class="btn-secondary">Сбросить</x-mary-button></a>
            </x-slot:actions>

        </x-maryform>
    </x-mary-card>
    
    <div class="w-full lg:w-2/3">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @foreach($this->teams as $team)
                <x-mary-card class="card card-border">
                    <div class="overflow-hidden rounded-xl bg-base-200 aspect-[16/9]">
                        <img src="/uploads/{{$team->image_url}}" class="w-full h-full object-cover" alt="{{$team->title}}">
                    </div>

                    <p class="card-title mt-2">{{$team->title}}</p>  

                    <x-mary-card class="card card-border bg-base-200">
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
