@props([
    'imageUrl' => null,
    'status' => null,
    'level' => null,
    'isFinished' => false,
])

@php
    $hasPhoto = filled($imageUrl);
@endphp

<div
    class="group/cover relative h-44 w-full shrink-0 overflow-hidden rounded-t-[var(--radius-card)] bg-base-300 sm:h-48"
    aria-hidden="true"
>
    @if ($hasPhoto)
        <img
            src="{{ $imageUrl }}"
            alt=""
            class="absolute inset-0 h-full w-full object-cover transition duration-300 group-hover/cover:scale-[1.02]"
            loading="lazy"
        />
    @else
        <div class="ui-cover-placeholder absolute inset-0" aria-hidden="true"></div>
    @endif

    {{-- Readability overlay --}}
    <div
        class="pointer-events-none absolute inset-0 bg-linear-to-b from-base-100/10 via-transparent to-base-100/60 [html[data-theme=hackatonshik-light]_&]:from-base-content/5 [html[data-theme=hackatonshik-light]_&]:to-base-content/30"
        aria-hidden="true"
    ></div>

    @if ($status)
        <div class="absolute right-3 top-3 z-20 flex flex-col items-end gap-2">
            <span class="badge badge-sm {{ $status->badgeClass() }} border-0 px-2.5 py-1 text-xs font-medium">
                {{ $status->label() }}
            </span>
            @if ($level)
                <span class="badge badge-sm {{ $level->badgeClass() }} border-0 px-2.5 py-1 text-xs font-medium">
                    {{ $level->label() }}
                </span>
            @endif
        </div>
    @endif

    @if ($isFinished)
        <div class="absolute inset-0 z-30 flex items-center justify-center bg-base-300/55" aria-hidden="true">
            <span class="rounded-lg border border-base-300 bg-base-100 px-4 py-1.5 font-display text-sm font-semibold text-base-content">
                Завершён
            </span>
        </div>
    @endif
</div>
