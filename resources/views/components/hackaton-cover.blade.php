@props([
    'title',
    'imageUrl' => null,
    'status' => null,
    'level' => null,
    'isFinished' => false,
])

@php
    $hasPhoto = filled($imageUrl);
@endphp

<div
    class="group/cover relative h-44 w-full shrink-0 overflow-hidden rounded-t-3xl bg-base-300 sm:h-48"
    aria-hidden="true"
>
    @if ($hasPhoto)
        <img
            src="{{ $imageUrl }}"
            alt=""
            class="absolute inset-0 h-full w-full object-cover transition duration-500 group-hover/cover:scale-[1.04]"
            loading="lazy"
        />
    @else
        <div
            class="absolute inset-0 bg-linear-to-br from-violet-600 via-cyan-500 to-fuchsia-600 [html[data-theme=hackatonshik-light]_&]:from-primary [html[data-theme=hackatonshik-light]_&]:via-accent [html[data-theme=hackatonshik-light]_&]:to-secondary"
            aria-hidden="true"
        ></div>
        <div class="absolute inset-0 opacity-30" aria-hidden="true">
            <div class="absolute -top-10 -left-10 h-40 w-40 rounded-full bg-white/30 blur-3xl"></div>
            <div class="absolute -bottom-12 -right-8 h-44 w-44 rounded-full bg-secondary/40 blur-3xl"></div>
        </div>
    @endif

    {{-- Readability overlay --}}
    <div
        class="pointer-events-none absolute inset-0 bg-linear-to-b from-base-100/10 via-transparent to-base-100/70 [html[data-theme=hackatonshik-light]_&]:from-base-content/10 [html[data-theme=hackatonshik-light]_&]:via-transparent [html[data-theme=hackatonshik-light]_&]:to-base-content/40"
        aria-hidden="true"
    ></div>

    {{-- Status badge in the top-right corner --}}
    @if ($status)
        <div class="absolute right-3 top-3 z-20 flex flex-col items-end gap-2">
            <span class="badge badge-sm {{ $status->badgeClass() }} border-0 px-2.5 py-1 text-[10px] font-bold uppercase tracking-widest shadow-lg backdrop-blur-sm">
                {{ $status->label() }}
            </span>
            @if ($level)
                <span class="badge badge-sm {{ $level->badgeClass() }} border-0 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-wide shadow-lg backdrop-blur-sm">
                    {{ $level->label() }}
                </span>
            @endif
        </div>
    @endif

    {{-- Title overlay at bottom --}}
    <div class="absolute inset-x-0 bottom-0 z-10 px-4 pb-3 pt-10">
        <p class="line-clamp-2 font-display text-xl font-black leading-tight tracking-tight text-base-100 drop-shadow-lg [html[data-theme=hackatonshik-light]_&]:text-base-content sm:text-2xl">
            {{ $title }}
        </p>
    </div>

    {{-- Finished overlay --}}
    @if ($isFinished)
        <div class="absolute inset-0 z-30 flex items-center justify-center bg-base-300/55 backdrop-blur-[2px]" aria-hidden="true">
            <span class="rounded-full border border-base-100/20 bg-base-100/90 px-4 py-1.5 font-display text-sm font-black uppercase tracking-widest text-base-content shadow-xl">
                Завершён
            </span>
        </div>
    @endif
</div>
