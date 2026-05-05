<?php

use App\Models\Team;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
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

    public bool $deleteHackatonModal = false;
    public ?int $deleteHackatonId = null;

    public function showDeleteHackatonModal(int $hackatonId): void
    {
        $this->deleteHackatonId = $hackatonId;
        $this->deleteHackatonModal = true;
    }

    public function deleteHackaton(): void
    {
        if ($this->deleteHackatonId === null) {
            return;
        }

        $hackaton = \App\Models\Hackaton::find($this->deleteHackatonId);
        $hackaton?->delete();
        $this->deleteHackatonId = null;
        $this->deleteHackatonModal = false;
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
    <div wire:loading class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 mt-6" aria-busy="true" aria-label="Загрузка хакатонов">
        @foreach (range(1, 6) as $_)
            <div class="card card-border border-base-300 bg-base-100 shadow-sm overflow-hidden rounded-3xl">
                <div class="skeleton aspect-video w-full rounded-none"></div>
                <div class="p-4 space-y-3">
                    <div class="skeleton h-6 w-4/5 rounded-xl"></div>
                    <div class="skeleton h-16 w-full rounded-2xl"></div>
                    <div class="flex gap-2 pt-2">
                        <div class="skeleton h-9 flex-1 rounded-xl"></div>
                        <div class="skeleton h-9 flex-1 rounded-xl"></div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div wire:loading.remove>
    <div class="text-sm breadcrumbs">
        <ul>
            <li><a href="/">Главная</a></li>
            <li><a href="/profile">Профиль</a></li>
            <li class="opacity-70">Мои хакатоны</li>
        </ul>
    </div>

    <h3 class="text-3xl text-center">Ваши хакатоны</h3>

    <x-mary-modal wire:model="deleteHackatonModal" title="Подтверждение удаления" class="backdrop-blur">
        Вы действительно хотите удалить хакатон? Это действие нельзя отменить.

        <x-slot:actions>
            <x-mary-button class="btn-error" label="Удалить" wire:click="deleteHackaton" />
            <x-mary-button label="Отмена" @click="$wire.deleteHackatonModal = false" />
        </x-slot:actions>
    </x-mary-modal>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-6">
        @forelse($this->hackatons as $hackaton)
            <x-marycard class="card card-border">
               <div class="overflow-hidden rounded-xl bg-base-200 aspect-video">
                        <img src="/uploads/{{$hackaton->image_url}}" class="w-full h-full object-cover" alt="{{$hackaton->title}}">
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
                    <a href="/hackatons/{{$hackaton->id}}">
                        <x-marybutton class="btn-ghost" label="Просмотреть" />
                    </a>
                    <x-marybutton class="btn-primary" label="Изменить" wire:click="editHackaton({{$hackaton->id}})" />
                    <x-marybutton class="btn-secondary" label="Участники" wire:click="participantsHackaton({{$hackaton->id}})" />
                    <x-marybutton class="btn-error" label="Удалить" wire:click="showDeleteHackatonModal({{$hackaton->id}})" />
                </x-slot:actions>

            </x-marycard>

            @empty

            <h4>У вас нет хакатонов :(</h4>

        @endforelse
    </div>

</div>
    </div>
