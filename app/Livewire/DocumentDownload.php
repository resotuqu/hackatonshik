<?php

namespace App\Livewire;

use App\Models\HackatonDocument;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class DocumentDownload extends Component
{
    public HackatonDocument $hackatonDocument;

    public function mount(HackatonDocument $hackatonDocument): void
    {
        $this->hackatonDocument = $hackatonDocument;
    }

    public function download(): mixed
    {
        return Storage::disk('public')->download($this->hackatonDocument->file_url);
    }

    public function render(): View
    {
        return view('livewire.document-download');
    }
}
