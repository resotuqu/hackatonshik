@props([
    'title' => '',
    'index' => 0,
    'filled' => true,
    'canRemove' => true,
    'removeAction' => '',
    'draggable' => true,
])

<x-mary-card
    class="border border-base-300 bg-base-200/50 transition-all duration-200 hover:border-base-200 hover:shadow-md"
    wire:key="list-item-{{ $index }}"
>
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
        <div class="flex items-start gap-3 flex-1 min-w-0">
            @if ($draggable)
                <div class="cursor-grab active:cursor-grabbing pt-1 text-base-content/40 hover:text-base-content/60">
                    <x-mary-icon name="o-bars-3" class="h-5 w-5" />
                </div>
            @endif
            <div class="min-w-0 flex-1">
                <div class="flex flex-wrap items-baseline gap-2">
                    <x-mary-badge
                        class="badge-neutral flex-shrink-0"
                        value="{{ isset($title) && $title ? $title : '№ ' . ($index + 1) }}"
                    />
                    @if (!$filled)
                        <span class="text-xs text-warning/70 font-medium">Заполнено частично</span>
                    @endif
                </div>
            </div>
        </div>

        @if ($canRemove)
            <x-mary-button
                type="button"
                class="btn-ghost btn-sm text-error/70 hover:bg-error/10 hover:text-error flex-shrink-0"
                wire:click="{{ $removeAction }}"
                icon="o-trash"
                label="Удалить"
            />
        @else
            <span class="text-xs text-base-content/40">Не может быть удалено</span>
        @endif
    </div>

    {{ $slot }}
</x-mary-card>
