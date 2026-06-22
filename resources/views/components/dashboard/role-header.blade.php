@props([
    'icon',
    'title',
    'subtitle',
    'panelHref' => null,
    'panelLabel' => 'Панель',
    'iconTone' => 'primary',
])

@php
    $iconWrapperClass = match ($iconTone) {
        'secondary' => 'bg-secondary/10 text-secondary',
        'accent' => 'bg-accent/10 text-accent',
        default => 'bg-primary/10 text-primary',
    };
@endphp

<div class="flex items-center gap-3">
    <div @class(['flex h-9 w-9 items-center justify-center rounded-xl', $iconWrapperClass])>
        <x-app-icon :icon="$icon" class="h-5 w-5" />
    </div>
    <div>
        <h2 class="ui-heading-display text-base font-semibold">{{ $title }}</h2>
        <p class="text-xs text-base-content/50">{{ $subtitle }}</p>
    </div>
    @if ($panelHref)
        <a href="{{ $panelHref }}" class="btn btn-ghost btn-xs ml-auto" wire:navigate>
            {{ $panelLabel }}
            <x-app-icon icon="heroicons:arrow-right" class="h-3.5 w-3.5" />
        </a>
    @endif
</div>
