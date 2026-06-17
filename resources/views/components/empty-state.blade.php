@props([
    'title',
    'description' => null,
    'icon' => 'heroicons:inbox',
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
        : ($compact ? 'px-5 py-10 sm:px-8' : 'px-6 py-12 sm:px-10');
    $shellClasses = $embedded
        ? 'rounded-lg border border-base-300 bg-base-200 text-center'
        : 'rounded-[var(--radius-card)] border border-base-300 bg-base-200 text-center';
@endphp

<div
    {{ $attributes->class([
        $shellClasses,
        $padding,
    ]) }}
    @if ($testId) data-testid="{{ $testId }}" @endif
>
    <div @class(['mx-auto flex flex-col items-center gap-5', $embedded ? 'max-w-lg' : 'max-w-md'])>
        @php
            $iconSizeClass = $embedded
                ? 'h-9 w-9 text-base-content/50'
                : ($compact ? 'h-10 w-10 text-base-content/50' : 'h-12 w-12 text-base-content/50');
        @endphp
        <x-app-icon :icon="$icon" class="{{ $iconSizeClass }}" />

        <div class="space-y-2">
            <h3 @class([
                'ui-heading-display font-semibold leading-tight',
                $embedded ? 'text-lg' : 'text-xl sm:text-2xl',
            ])>{{ $title }}</h3>
            @if ($description)
                <p class="mx-auto max-w-sm text-pretty text-sm leading-relaxed text-base-content/70 sm:text-base">{{ $description }}</p>
            @endif
        </div>

        @if ($actionHref && $actionLabel)
            <div class="flex w-full flex-col gap-2 sm:flex-row sm:justify-center">
                <a href="{{ $actionHref }}" @class(['btn-wide', $embedded ? 'btn btn-primary btn-sm' : 'ui-cta-primary'])>
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
