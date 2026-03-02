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

<div class="px-4 py-4 bg-slate-400 rounded-sm">
    <p>Наименование: {{$hackatonDocument->name}}</p>
    <p>Описание: {{$hackatonDocument->description}}</p>
    <button wire:click="download" class="bg-blue-400 text-white">Скачать</button>
</div>
