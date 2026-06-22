@props([
    'label',
    'value',
    'icon',
    'href' => null,
    'linkText' => 'Открыть →',
    'highlight' => false,
])

<div @class([
    'ui-surface-card flex flex-col gap-3 p-4 sm:p-5',
    'border-warning/30 bg-warning/5' => $highlight,
])>
    <div class="flex items-start justify-between gap-2">
        <p class="text-xs text-base-content/50">{{ $label }}</p>
        <div @class([
            'flex h-7 w-7 shrink-0 items-center justify-center rounded-lg',
            'bg-warning/15' => $highlight,
            'bg-base-200' => ! $highlight,
        ])>
            <x-app-icon
                :icon="$icon"
                @class([
                    'h-3.5 w-3.5',
                    'text-warning' => $highlight,
                    'text-base-content/40' => ! $highlight,
                ])
            />
        </div>
    </div>
    <p @class([
        'ui-heading-display text-3xl font-bold tabular-nums sm:text-4xl',
        'text-warning' => $highlight,
    ])>{{ $value }}</p>
    @if ($href)
        <a href="{{ $href }}" @class([
            'text-xs font-medium hover:underline',
            'text-warning' => $highlight,
            'text-primary' => ! $highlight,
        ]) wire:navigate>{{ $linkText }}</a>
    @else
        {{ $slot }}
    @endif
</div>
