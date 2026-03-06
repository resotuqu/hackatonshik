<?php

use App\Models\Hackaton;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;

new #[Layout('layouts::app', ['title' => 'Хакатон'])]
class extends Component {
    public Hackaton $hackaton;

    public function mount(Hackaton $hackaton): void
    {
        $this->hackaton = $hackaton;
    }

};
?>

<div class="flex flex-col lg:flex-row gap-4">
    <x-mary-card class="w-full lg:w-1/2 card card-border h-fit">
        <img class="w-full justify-self-center" src="/uploads/{{$hackaton->image_url}}" alt="">
    </x-mary-card>
    <x-mary-card class="w-full lg:w-1/2 card card-border">
        <div class="space-y-4">
            <h5 class="card-title">{{$hackaton->title}}</h5>

            <div>
                <x-marybadge class="badge-soft" value="Всего команд: {{$hackaton->teamsCount()}}" />
                <x-marybadge class="badge-soft" value="Всего участников: {{$hackaton->participantsCount()}}" />
            </div>

            {{--Description--}}
            <x-mary-card class="card card-border" title="Описание">
                <x-markdown>{{$hackaton->description}}</x-markdown>
            </x-mary-card>

            <x-mary-card title="Даты" class="card card-border">
                <p>Даты проведения: с {{$hackaton->start_at}} по {{$hackaton->end_at}}</p>
            </x-mary-card>




            {{--Documents--}}
            <x-marycard title="Файлы" class="card card-border">
                @foreach($hackaton->documents as $document)
                    <livewire:document-download :hackaton-document="$document" />
                @endforeach
            </x-marycard>
        </div>
    </x-mary-card>
</div>
