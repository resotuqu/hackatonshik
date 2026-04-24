@props([
    'title' => '',
    'submitButtonTitle' => 'Сохранить',
])

<div class="mx-auto w-full max-w-3xl">
    <form wire:submit="save" class="card border border-base-200 bg-base-100 shadow-sm">
        <div class="card-body gap-5 p-4 sm:p-6">
            @if ($title !== '')
                <h3 class="text-xl font-semibold sm:text-2xl">{{ $title }}</h3>
            @endif
            @csrf
            @if ($errors->any())
                <div class="alert alert-error">
                    <span>Проверьте форму и исправьте ошибки.</span>
                </div>
            @endif
            {{ $slot }}
            <button class="btn btn-primary mt-2 w-full sm:w-auto" type="submit" wire:loading.attr="disabled" wire:target="save">
                <span wire:loading.remove wire:target="save">{{ $submitButtonTitle }}</span>
                <span wire:loading wire:target="save">Сохраняем...</span>
            </button>
        </div>
    </form>
</div>
