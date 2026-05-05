@props([
    'hackatonDocument',
])

<livewire:document-download
    :hackaton-document="$hackatonDocument"
    wire:key="document-download-{{ $hackatonDocument->id }}"
/>
