<?php

use App\Models\Hackaton;
use App\Models\Team;
use Illuminate\Support\Carbon;
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

    public function clearFilters(): void
    {
        $this->reset(['q', 'start_at']);
        $this->resetPage();
    }

    public $q = '';
    public $start_at;
}
?>

<div class="grid grid-cols-1 gap-6 lg:grid-cols-3 lg:items-start">

    <x-mary-card class="card card-border h-fit lg:col-span-1">
        <h5 class="text-2xl">Фильтрация</h5>
        <x-maryform wire:submit="search">
            @csrf
            <x-mary-input label="Наименование" placeholder="Введите название..." wire:model="q"/>
            <x-marydatetime wire:model="start_at" label="Начало от"  />
            <x-slot:actions>
                <x-mary-button type="submit" class="btn-primary" wire:loading.attr="disabled" wire:target="search">Искать</x-mary-button>
                <x-mary-button class="btn-secondary" type="button" wire:click="clearFilters" wire:loading.attr="disabled" wire:target="clearFilters">Сбросить</x-mary-button>
            </x-slot:actions>
        </x-maryform>
    </x-mary-card>

    <div class="lg:col-span-2 space-y-4">
        @php
            $hasFilters = filled($q) || filled($start_at);
        @endphp

        @if ($hasFilters)
            <div class="card card-border bg-base-100">
                <div class="card-body p-4">
                    <p class="text-sm font-medium">Активные фильтры</p>
                    <div class="mt-2 flex flex-wrap gap-2">
                        @if (filled($q))
                            <x-marybadge class="badge-primary" value="Наименование: {{ $q }}" />
                        @endif
                        @if (filled($start_at))
                            <x-marybadge class="badge-primary" value="Начало от: {{ Carbon::parse($start_at)->format('d.m.Y H:i') }}" />
                        @endif
                    </div>
                    <div class="mt-3">
                        <x-mary-button class="btn-sm btn-ghost" wire:click="clearFilters">Очистить все</x-mary-button>
                    </div>
                </div>
            </div>
        @endif

        <div wire:loading.flex wire:target="search,clearFilters,q,start_at,nextPage,previousPage,gotoPage,setPage"
             class="items-center justify-center rounded-xl border border-dashed border-base-300 bg-base-100 px-6 py-10 text-base-content/70">
            Загружаем хакатоны...
        </div>

        <div wire:loading.remove wire:target="search,clearFilters,q,start_at,nextPage,previousPage,gotoPage,setPage">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                @forelse($this->hackatons as $hackaton)
                    <x-mary-card class="card card-border" wire:key="hackaton-card-{{ $hackaton->id }}">
                        @php
                            $hackatonImage = filled($hackaton->image_url)
                                ? (str_starts_with($hackaton->image_url, 'http') ? $hackaton->image_url : asset('storage/' . $hackaton->image_url))
                                : null;
                        @endphp
                        <div class="overflow-hidden rounded-xl bg-base-200 aspect-video">
                            @if ($hackatonImage)
                                <img src="{{ $hackatonImage }}" class="w-full h-full object-cover" alt="{{ $hackaton->title }}">
                            @else
                                <div class="flex h-full w-full items-center justify-center text-base-content/60">Изображение хакатона отсутствует</div>
                            @endif
                        </div>
                        <div class="mt-2 space-y-2">
                            <p class="card-title">{{$hackaton->title}}</p>
                            <x-mary-card class="card card-border bg-base-200">
                                <p>Принимает участие: {{$hackaton->participantsCount()}} команд</p>
                                <p>Даты проведения:
                                    {{ Carbon::parse($hackaton->start_at)->format('d.m.Y H:i') }} &DownLeftVectorBar;
                                    {{ Carbon::parse($hackaton->end_at)->format('d.m.Y H:i') }}</p>
                            </x-mary-card>
                        </div>

                        <x-slot:actions>
                            <a href="{{ route('hackatons.show', $hackaton) }}"><x-mary-button class="btn-primary" label="Подробнее"/></a>
                        </x-slot:actions>

                    </x-mary-card>
                @empty
                    <div class="sm:col-span-2 card card-border bg-base-100">
                        <div class="card-body items-center text-center">
                            <h3 class="card-title">Хакатоны не найдены</h3>
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
            {{$this->hackatons->links(data: ['scrollTo' => false])}}
        </div>
    </div>

</div>
