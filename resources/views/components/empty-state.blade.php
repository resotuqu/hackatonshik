@props([
    'title',
    'description' => null,
    'icon' => 'heroicons:sparkles',
    'actionHref' => null,
    'actionLabel' => null,
    'secondaryActionHref' => null,
    'secondaryActionLabel' => null,
    'testId' => null,
    'compact' => false,
    'embedded' => false,
])

@php
    $padding = $embedded
        ? 'px-4 py-6 sm:px-5'
        : ($compact ? 'px-5 py-10 sm:px-8' : 'px-6 py-14 sm:px-10');
    $shellClasses = $embedded
        ? 'rounded-2xl border border-base-300/60 bg-base-200/35 text-center'
        : 'relative overflow-hidden rounded-[var(--radius-card)] border border-dashed border-primary/25 bg-linear-to-br from-base-200/40 via-base-100/80 to-primary/10 text-center';
@endphp

<div
    {{ $attributes->class([
        $shellClasses,
        $padding,
    ]) }}
    @if ($testId) data-testid="{{ $testId }}" @endif
>
    @unless ($embedded)
        <div class="pointer-events-none absolute -left-8 top-1/2 h-40 w-40 -translate-y-1/2 rounded-full bg-primary/15 blur-3xl"></div>
        <div class="pointer-events-none absolute -right-10 bottom-0 h-36 w-36 rounded-full bg-secondary/15 blur-3xl"></div>
    @endunless

    <div @class(['relative mx-auto flex flex-col items-center gap-5', $embedded ? 'max-w-lg' : 'max-w-md'])>
        <div @class(['relative flex items-center justify-center', $compact || $embedded ? 'h-20 w-20' : 'h-28 w-28'])>
            <span @class([
                'absolute inset-0 rounded-full border-2',
                $embedded ? 'border-base-300/70' : 'border-dashed border-primary/30',
            ])></span>
            <span @class([
                'absolute rounded-full ring-1',
                $embedded ? 'inset-2 bg-base-100/80 ring-base-300/50' : 'inset-3 bg-base-100/90 ring-primary/20',
            ])></span>
            @unless ($embedded)
                <span class="absolute inset-0 rounded-full bg-primary/5 motion-safe:animate-pulse [animation-duration:3s]"></span>
            @endunless
        @php
            $iconSizeClass = $embedded
                ? 'h-9 w-9 text-base-content/60'
                : ($compact ? 'h-10 w-10 text-primary' : 'h-14 w-14 text-primary');
        @endphp
            <div class="relative motion-safe:animate-[bounce_3s_infinite] [animation-delay:150ms]">
                <x-app-icon :icon="$icon" class="relative {{ $iconSizeClass }}" />
            </div>
        </div>

        <div class="space-y-3">
            <h3 @class([
                'ui-heading-display font-black leading-tight',
                $embedded ? 'text-lg' : 'text-2xl sm:text-3xl',
            ])>{{ $title }}</h3>
            @if ($description)
                <p class="mx-auto max-w-sm text-pretty text-sm leading-relaxed text-base-content/70 sm:text-base">{{ $description }}</p>
            @endif
        </div>

        @if ($actionHref && $actionLabel)
            <div class="flex w-full flex-col gap-2 sm:flex-row sm:justify-center">
                <a href="{{ $actionHref }}" @class(['btn-wide', $embedded ? 'btn btn-primary btn-sm' : 'ui-cta-primary'])>
                    @unless($embedded)
                        <x-app-icon icon="heroicons:arrow-right-circle" class="h-5 w-5" />
                    @endunless
                    {{ $actionLabel }}
                </a>
                @if ($secondaryActionHref && $secondaryActionLabel)
                    <a href="{{ $secondaryActionHref }}" @class(['btn-wide', $embedded ? 'btn btn-outline btn-sm border-base-300' : 'ui-cta-outline'])>
                        {{ $secondaryActionLabel }}
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>
