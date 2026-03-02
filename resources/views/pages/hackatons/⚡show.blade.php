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

<div class="flex flex-row">
    <div class="w-1/2 justify-center items-center justify-items-center">
        <img class="w-128" src="/uploads/{{$hackaton->image_url}}" alt="">
    </div>
    <div>
        <div>
            <h5>{{$hackaton->title}}</h5>
            <p>{{$hackaton->description}}</p>
            <p>Даты проведения: с {{$hackaton->start_at}} по {{$hackaton->end_at}}</p>
            <p>Всего команд: {{$hackaton->participantsCount()}}</p>
        </div>

        {{--Documents--}}
        <div class="bg-slate-600 text-white py-4 px-2 space-y-4 rounded-sm">
            @foreach($hackaton->documents as $document)
                <livewire:document-download :hackaton-document="$document" />
            @endforeach
        </div>
    </div>
</div>
