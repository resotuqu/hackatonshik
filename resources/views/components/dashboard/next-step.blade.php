@props([
    'title',
    'hint',
    'href' => null,
    'actionLabel' => null,
])

<div class="flex gap-4 rounded-xl border border-primary/20 bg-primary/5 p-4 sm:p-5">
    <div class="mt-0.5 shrink-0 text-primary">
        <x-app-icon icon="heroicons:arrow-right-circle" class="h-6 w-6" />
    </div>
    <div class="min-w-0 flex-1">
        <p class="text-xs font-semibold uppercase tracking-widest text-primary/70">Следующий шаг</p>
        <p class="mt-0.5 font-semibold">{{ $title }}</p>
        <p class="mt-1 text-sm text-base-content/70">{{ $hint }}</p>
        @if ($href && $actionLabel)
            <a href="{{ $href }}" class="ui-cta-primary btn-sm mt-3" wire:navigate>{{ $actionLabel }}</a>
        @endif
    </div>
</div>
