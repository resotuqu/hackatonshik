@props([
    'title',
    'description' => null,
    'icon' => 'heroicons:sparkles',
    'actionHref' => null,
    'actionLabel' => null,
    'testId' => null,
])

<div
    {{ $attributes->class('relative overflow-hidden rounded-3xl border border-dashed border-primary/25 bg-linear-to-br from-base-200/40 via-base-100/80 to-primary/10 px-6 py-14 text-center sm:px-10') }}
    @if ($testId) data-testid="{{ $testId }}" @endif
>
    <div class="pointer-events-none absolute -left-8 top-1/2 h-40 w-40 -translate-y-1/2 rounded-full bg-primary/15 blur-3xl"></div>
    <div class="pointer-events-none absolute -right-10 bottom-0 h-36 w-36 rounded-full bg-secondary/15 blur-3xl"></div>

    <div class="relative mx-auto flex max-w-md flex-col items-center gap-5">
        <div class="relative flex h-28 w-28 items-center justify-center">
            <span class="absolute inset-0 rounded-full border-2 border-dashed border-primary/30"></span>
            <span class="absolute inset-3 rounded-full bg-base-100/90 ring-1 ring-primary/20"></span>
            <span class="absolute inset-0 rounded-full bg-primary/5 motion-safe:animate-pulse [animation-duration:3s]"></span>
            <x-app-icon :icon="$icon" class="relative h-14 w-14 text-primary" />
        </div>

        <div class="space-y-2">
            <h3 class="font-display text-xl font-bold tracking-tight text-base-content sm:text-2xl">{{ $title }}</h3>
            @if ($description)
                <p class="text-pretty text-sm leading-relaxed text-base-content/70 sm:text-base">{{ $description }}</p>
            @endif
        </div>

        @if ($actionHref && $actionLabel)
            <a href="{{ $actionHref }}" class="btn btn-primary btn-wide gap-2 shadow-lg shadow-primary/20">
                <x-app-icon icon="heroicons:arrow-right-circle" class="h-5 w-5" />
                {{ $actionLabel }}
            </a>
        @endif
    </div>
</div>
