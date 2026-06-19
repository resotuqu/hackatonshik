@props([
    'title',
    'coverUrl' => null,
    'initials' => '',
    'showBrandStrip' => false,
])

@php
    $hasPhoto = filled($coverUrl);
@endphp

<div
    class="group/cover relative h-44 w-full shrink-0 overflow-hidden rounded-t-[var(--radius-card)] bg-base-300 transition-colors duration-200"
    aria-hidden="true"
>
    @if ($hasPhoto)
        <img
            src="{{ $coverUrl }}"
            alt=""
            class="absolute inset-0 h-full w-full object-cover transition duration-300 group-hover/cover:scale-[1.02]"
            loading="lazy"
            onerror="this.style.display='none'"
        />
    @else
        <div class="ui-cover-placeholder absolute inset-0" aria-hidden="true"></div>
    @endif

    <div
        class="pointer-events-none absolute inset-0 bg-linear-to-b from-base-100/40 via-base-100/10 to-base-100/60 [html[data-theme=hackatonshik-light]_&]:from-base-content/15 [html[data-theme=hackatonshik-light]_&]:to-base-content/35"
        aria-hidden="true"
    ></div>

    <div @class([
        'relative z-10 flex h-full flex-col items-center justify-center px-4 pb-3 pt-8 text-center',
        'gap-1.5' => $showBrandStrip,
        'gap-1' => ! $showBrandStrip,
    ])>
        @if (filled($title))
            <p class="line-clamp-2 max-w-full font-display text-lg font-semibold leading-tight text-base-100 [html[data-theme=hackatonshik-light]_&]:text-base-content sm:text-xl">
                {{ $title }}
            </p>
        @endif
        @if ($showBrandStrip)
            <p class="max-w-full truncate text-[10px] font-medium text-base-100/80 [html[data-theme=hackatonshik-light]_&]:text-base-content/70 sm:text-xs">
                Хакатонщик
            </p>
        @endif
        <span
            @class([
                'font-display font-bold tabular-nums tracking-tight text-secondary [html[data-theme=hackatonshik-light]_&]:text-primary',
                'text-2xl sm:text-3xl' => $showBrandStrip,
                'text-3xl sm:text-4xl' => ! $showBrandStrip,
            ])
            aria-hidden="true"
        >{{ $initials }}</span>
    </div>
</div>
