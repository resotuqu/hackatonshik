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
    class="group/cover relative h-44 w-full shrink-0 overflow-hidden rounded-t-3xl bg-base-300 transition duration-300 group-hover/card:ring-2 group-hover/card:ring-accent/35 group-hover/card:ring-inset"
    aria-hidden="true"
>
    @if ($hasPhoto)
        <img
            src="{{ $coverUrl }}"
            alt=""
            class="absolute inset-0 h-full w-full object-cover transition duration-500 group-hover/cover:scale-[1.03] group-hover/card:scale-[1.03]"
            loading="lazy"
        />
    @else
        <div
            class="absolute inset-0 bg-linear-to-br from-violet-600 via-cyan-500 to-fuchsia-600 [html[data-theme=hackatonshik-light]_&]:from-primary [html[data-theme=hackatonshik-light]_&]:via-accent [html[data-theme=hackatonshik-light]_&]:to-secondary"
            aria-hidden="true"
        ></div>
    @endif

    {{-- Readability overlay --}}
    <div
        class="pointer-events-none absolute inset-0 bg-linear-to-b from-base-100/55 via-base-100/15 to-base-100/65 [html[data-theme=hackatonshik-light]_&]:from-base-content/20 [html[data-theme=hackatonshik-light]_&]:via-transparent [html[data-theme=hackatonshik-light]_&]:to-base-content/35"
        aria-hidden="true"
    ></div>

    @if ($showRecruitingBadge)
        <div class="absolute right-3 top-3 z-20">
            <span class="badge badge-sm border-0 bg-secondary/95 px-2 py-1 text-[10px] font-bold uppercase tracking-widest text-secondary-content shadow-lg shadow-secondary/20">
                ОТКРЫТ НАБОР
            </span>
        </div>
    @endif

    @if (filled($hackatonTitle))
        <div class="absolute bottom-2 left-2 z-20 max-w-[min(100%,14rem)]">
            <span class="badge badge-sm border-0 bg-base-100/85 text-xs font-medium text-base-content backdrop-blur-sm">
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
        <p
            class="line-clamp-2 max-w-full font-display text-lg font-bold leading-tight tracking-tight text-base-100 drop-shadow-md [html[data-theme=hackatonshik-light]_&]:text-base-content [html[data-theme=hackatonshik-light]_&]:drop-shadow-sm sm:text-xl"
        >
            {{ $title }}
        </p>
        @if ($showBrandStrip)
            <p
                class="max-w-full truncate rounded-full border border-cyan-400/35 bg-base-100/10 px-3 py-0.5 font-mono text-[9px] font-bold uppercase tracking-[0.28em] text-cyan-200 shadow-[0_0_16px_rgba(34,211,238,0.25)] backdrop-blur-[2px] sm:text-[10px] [html[data-theme=hackatonshik-light]_&]:border-primary/30 [html[data-theme=hackatonshik-light]_&]:text-primary [html[data-theme=hackatonshik-light]_&]:shadow-[0_0_14px_rgba(81,112,255,0.22)]"
            >
                <span class="text-cyan-400/90 [html[data-theme=hackatonshik-light]_&]:text-primary/80">&gt;</span>
                <span class="mx-0.5 tracking-[0.32em]">ХАКАТОНЩИК</span>
                <span class="text-cyan-400/90 [html[data-theme=hackatonshik-light]_&]:text-primary/80">&lt;</span>
            </p>
        @endif
        <span
            @class([
                'font-display font-black tabular-nums tracking-tight text-secondary drop-shadow-[0_0_18px_rgba(163,230,53,0.45)] [html[data-theme=hackatonshik-light]_&]:text-primary [html[data-theme=hackatonshik-light]_&]:drop-shadow-[0_0_12px_rgba(81,112,255,0.35)]',
                'text-2xl sm:text-3xl' => $showBrandStrip,
                'text-3xl sm:text-4xl' => ! $showBrandStrip,
            ])
            aria-hidden="true"
        >{{ $initials }}</span>
    </div>
</div>
