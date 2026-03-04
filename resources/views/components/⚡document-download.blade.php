<?php

use Livewire\Component;

new class extends Component
{
    public \App\Models\HackatonDocument $hackatonDocument;

    public function mount(\App\Models\HackatonDocument $hackatonDocument): void
    {
        $this->hackatonDocument = $hackatonDocument;
    }

    public function download() {
        return Storage::disk('public')->download($this->hackatonDocument->file_url);
    }
};
?>

<x-marycard title="{{$hackatonDocument->name}}" class="card card-border">
    <x-markdown>{{$hackatonDocument->description}}</x-markdown>
    <x-marybutton wire:click="download" class="btn-primary mt-2">Скачать</x-marybutton>
</x-marycard>
