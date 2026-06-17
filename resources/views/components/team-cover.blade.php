@props([
    'title',
    'coverUrl' => null,
    'initials' => '',
    'showRecruitingBadge' => false,
    'hackatonTitle' => null,
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
        />
    @else
        <div class="ui-cover-placeholder absolute inset-0" aria-hidden="true"></div>
    @endif

    <div
        class="pointer-events-none absolute inset-0 bg-linear-to-b from-base-100/40 via-base-100/10 to-base-100/60 [html[data-theme=hackatonshik-light]_&]:from-base-content/15 [html[data-theme=hackatonshik-light]_&]:to-base-content/35"
        aria-hidden="true"
    ></div>

    @if ($showRecruitingBadge)
        <div class="absolute right-3 top-3 z-20">
            <span class="badge badge-sm border-0 bg-secondary px-2 py-1 text-xs font-medium text-secondary-content">
                Открыт набор
            </span>
        </div>
    @endif

    @if (filled($hackatonTitle))
        <div class="absolute bottom-2 left-2 z-20 max-w-[min(100%,14rem)]">
            <span class="badge badge-sm border border-base-300 bg-base-100 text-xs font-medium text-base-content">
                {{ $hackatonTitle }}
            </span>
        </div>
    @endif

    <div @class([
        'relative z-10 flex h-full flex-col items-center justify-center px-4 text-center',
        'gap-1.5' => $showBrandStrip,
        'gap-1' => ! $showBrandStrip,
        'pb-10 pt-7' => filled($hackatonTitle),
        'pb-3 pt-8' => ! filled($hackatonTitle),
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
