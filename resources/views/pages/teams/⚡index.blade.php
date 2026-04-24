<?php

use App\Models\Hackaton;
use App\Models\Role;
use App\Models\Skill;
use App\Models\Team;
use Illuminate\Support\Carbon;
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

    public function clearFilters(): void
    {
        $this->reset(['q', 'hackaton_id', 'role_id', 'skills', 'start_from']);
        $this->hackaton_id = '0';
        $this->role_id = '0';
        $this->resetPage();
    }

    public $q = '';
    public $hackaton_id = '0';
    public $role_id = '0';
    public $skills = [];
    public $start_from;
}
?>

<div class="grid grid-cols-1 gap-6 lg:grid-cols-3 lg:items-start">

    <x-mary-card class="h-fit card card-border lg:col-span-1">
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
                <x-mary-button type="submit" class="btn-primary" wire:loading.attr="disabled" wire:target="search">Искать</x-mary-button>
                <x-mary-button type="button" class="btn-secondary" wire:click="clearFilters" wire:loading.attr="disabled" wire:target="clearFilters">Сбросить</x-mary-button>
            </x-slot:actions>

        </x-maryform>
    </x-mary-card>
    
    <div class="lg:col-span-2 space-y-4">
        @php
            $hasFilters = filled($q) || $hackaton_id !== '0' || $role_id !== '0' || !empty($skills) || filled($start_from);
        @endphp

        @if ($hasFilters)
            <div class="card card-border bg-base-100">
                <div class="card-body p-4">
                    <p class="text-sm font-medium">Активные фильтры</p>
                    <div class="mt-2 flex flex-wrap gap-2">
                        @if (filled($q))
                            <x-marybadge class="badge-primary" value="Наименование: {{ $q }}" />
                        @endif
                        @if ($hackaton_id !== '0')
                            @php
                                $selectedHackaton = collect($this->hackatons)->firstWhere('id', $hackaton_id);
                            @endphp
                            <x-marybadge class="badge-primary" value="Хакатон: {{ $selectedHackaton['name'] ?? 'Выбран' }}" />
                        @endif
                        @if ($role_id !== '0')
                            @php
                                $selectedRole = collect($this->rolesData)->firstWhere('id', $role_id);
                            @endphp
                            <x-marybadge class="badge-primary" value="Роль: {{ $selectedRole['name'] ?? 'Выбрана' }}" />
                        @endif
                        @if (!empty($skills))
                            @foreach ($this->skillsData->whereIn('id', $skills) as $skill)
                                <x-marybadge class="badge-primary" value="Навык: {{ $skill->name }}" />
                            @endforeach
                        @endif
                        @if (filled($start_from))
                            <x-marybadge class="badge-primary" value="Начало от: {{ Carbon::parse($start_from)->format('d.m.Y H:i') }}" />
                        @endif
                    </div>
                    <div class="mt-3">
                        <x-mary-button class="btn-sm btn-ghost" wire:click="clearFilters">Очистить все</x-mary-button>
                    </div>
                </div>
            </div>
        @endif

        <div wire:loading.flex wire:target="search,clearFilters,q,hackaton_id,role_id,skills,start_from,nextPage,previousPage,gotoPage,setPage"
            class="items-center justify-center rounded-xl border border-dashed border-base-300 bg-base-100 px-6 py-10 text-base-content/70">
            Загружаем команды...
        </div>

        <div wire:loading.remove wire:target="search,clearFilters,q,hackaton_id,role_id,skills,start_from,nextPage,previousPage,gotoPage,setPage">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                @forelse($this->teams as $team)
                    <x-mary-card class="card card-border" wire:key="team-card-{{ $team->id }}">
                        @php
                            $teamImage = filled($team->image_url)
                                ? (str_starts_with($team->image_url, 'http') ? $team->image_url : asset('storage/' . $team->image_url))
                                : null;
                        @endphp
                        <div class="overflow-hidden rounded-xl bg-base-200 aspect-video">
                            @if ($teamImage)
                                <img src="{{ $teamImage }}" class="w-full h-full object-cover" alt="{{ $team->title }}">
                            @else
                                <div class="flex h-full w-full items-center justify-center text-base-content/60">Изображение команды отсутствует</div>
                            @endif
                        </div>

                        <p class="card-title mt-2">{{$team->title}}</p>

                        <x-mary-card class="card card-border bg-base-200">
                            <p>Пользователь: {{$team->user->nickname}}</p>
                            <p>{{$team->hackaton->title}}</p>
                            <p>Даты проведения:
                                {{ Carbon::parse($team->hackaton->start_at)->format('d.m.Y H:i') }} &DownLeftVectorBar;
                                {{ Carbon::parse($team->hackaton->end_at)->format('d.m.Y H:i') }}</p>
                        </x-mary-card>

                        <div class="mt-2">
                            <x-marybadge value="Количество ролей: {{$team->roles->count()}}" class="badge-neutral" />
                            <x-marybadge value="Свободно ролей: {{$team->emptyRoles()}}" class="badge-neutral" />
                        </div>

                        <x-slot:actions>
                            <a href="{{ route('teams.show', $team) }}"><x-mary-button label="Подробнее" class="btn-primary" /></a>
                        </x-slot:actions>

                    </x-mary-card>
                @empty
                    <div class="sm:col-span-2 card card-border bg-base-100">
                        <div class="card-body items-center text-center">
                            <h3 class="card-title">Команды не найдены</h3>
                            <p class="text-base-content/70">
                                Попробуйте изменить параметры поиска или сбросить фильтры.
                            </p>
                            <x-mary-button class="btn-primary btn-sm mt-2" wire:click="clearFilters">
                                Сбросить фильтры
                            </x-mary-button>
                        </div>
                    </div>
                @endforelse
            </div>
            {{$this->teams->links(data: ['scrollTo' => false])}}
        </div>
    </div>

</div>
