<x-mary-card title="{{ $hackatonDocument->name }}" class="card card-border">
    <x-safe-markdown :content="$hackatonDocument->description" />
    <button type="button" class="ui-cta-primary mt-2" wire:click="download">
        Скачать
    </button>
</x-mary-card>
