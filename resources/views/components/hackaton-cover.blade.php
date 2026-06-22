@props([
    'imageUrl' => null,
    'isFinished' => false,
    'label' => null,
])

@php
    $hasPhoto = filled($imageUrl);
@endphp

<div
    class="group/cover relative h-44 w-full shrink-0 overflow-hidden rounded-t-card bg-base-300 sm:h-48"
    aria-hidden="true"
>
    @if ($hasPhoto)
        <img
            src="{{ $imageUrl }}"
            alt=""
            class="absolute inset-0 h-full w-full object-cover transition duration-300 group-hover/cover:scale-[1.02]"
            loading="lazy"
            onerror="this.style.display='none';this.nextElementSibling.style.display='block'"
        />
        <div class="ui-cover-placeholder absolute inset-0" aria-hidden="true" style="display:none"></div>
    @else
        <div class="ui-cover-placeholder absolute inset-0" aria-hidden="true"></div>
    @endif

    {{-- Readability overlay --}}
    <div
        class="pointer-events-none absolute inset-0 bg-linear-to-b from-base-100/10 via-transparent to-base-100/60 [html[data-theme=cmyk]_&]:from-base-content/5 [html[data-theme=cmyk]_&]:to-base-content/30"
        aria-hidden="true"
    ></div>

    @if ($isFinished)
        <div class="absolute inset-0 z-30 flex items-center justify-center bg-base-300/50" aria-hidden="true">
            <span class="rounded-lg border border-base-300 bg-base-100 px-4 py-1.5 font-display text-sm font-semibold text-base-content">
                Завершён
            </span>
        </div>
    @elseif ($label)
        <div class="absolute left-2.5 top-2.5 z-20" aria-hidden="true">
            <span class="inline-flex items-center gap-1.5 rounded-control border border-base-300 bg-base-100/80 px-2 py-0.5 text-[11px] font-semibold leading-5 text-base-content backdrop-blur-sm">
                {{ $label }}
            </span>
        </div>
    @endif
</div>
