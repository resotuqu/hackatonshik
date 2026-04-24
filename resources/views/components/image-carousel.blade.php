@props([
    'carouselId',
    'items',
    'aspectClass' => 'aspect-video',
    'emptyText' => 'Изображения отсутствуют',
])

@php
    $slides = collect($items)->values();
@endphp

<div data-image-carousel id="{{ $carouselId }}" class="relative overflow-hidden rounded-xl border border-base-300 bg-base-200">
    @if ($slides->isEmpty())
        <div class="{{ $aspectClass }} flex items-center justify-center text-base-content/60">
            {{ $emptyText }}
        </div>
    @else
        <div class="{{ $aspectClass }} relative">
            @foreach ($slides as $slideIndex => $slide)
                @php
                    $slidePath = data_get($slide, 'path');
                    $slideAlt = (string) data_get($slide, 'alt', '');
                    $slideUrl = filled($slidePath)
                        ? (str_starts_with($slidePath, 'http') ? $slidePath : asset('storage/' . $slidePath))
                        : null;
                @endphp
                <div data-carousel-slide class="{{ $slideIndex === 0 ? '' : 'hidden' }} absolute inset-0">
                    @if ($slideUrl)
                        <img src="{{ $slideUrl }}" alt="{{ $slideAlt !== '' ? $slideAlt : 'Изображение' }}" class="h-full w-full object-cover" loading="lazy">
                    @endif
                </div>
            @endforeach
        </div>

        @if ($slides->count() > 1)
            <button type="button" data-carousel-prev class="btn btn-circle btn-sm absolute left-3 top-1/2 -translate-y-1/2 bg-base-100/90">❮</button>
            <button type="button" data-carousel-next class="btn btn-circle btn-sm absolute right-3 top-1/2 -translate-y-1/2 bg-base-100/90">❯</button>

            <div class="absolute bottom-3 left-1/2 flex -translate-x-1/2 gap-2">
                @foreach ($slides as $dotIndex => $unusedSlide)
                    <button type="button"
                            data-carousel-dot="{{ $dotIndex }}"
                            class="h-2.5 w-2.5 rounded-full border border-base-100 {{ $dotIndex === 0 ? 'bg-base-100' : 'bg-base-100/40' }}"
                            aria-label="Перейти к слайду {{ $dotIndex + 1 }}">
                    </button>
                @endforeach
            </div>
        @endif
    @endif
</div>
