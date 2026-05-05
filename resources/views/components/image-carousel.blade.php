@props([
    'carouselId',
    'items',
    'aspectClass' => 'aspect-video',
    'emptyText' => 'Изображения отсутствуют',
])

@php
    $slides = collect($items)->values();
    $lightboxSlides = $slides->map(function ($slide): array {
        $slidePath = data_get($slide, 'path');
        $slideAlt = (string) data_get($slide, 'alt', '');
        $slideUrl = filled($slidePath)
            ? (str_starts_with($slidePath, 'http') ? $slidePath : asset('storage/' . $slidePath))
            : null;

        return [
            'url' => $slideUrl,
            'alt' => $slideAlt !== '' ? $slideAlt : 'Изображение',
        ];
    })->filter(fn (array $slide) => filled($slide['url']))->values();
@endphp

<div
    data-image-carousel
    id="{{ $carouselId }}"
    class="relative overflow-hidden rounded-xl border border-base-300 bg-base-200"
    x-data="{
        open: false,
        index: 0,
        slides: @js($lightboxSlides),
        openLightbox(i) {
            this.index = i;
            this.open = true;
            document.body.classList.add('overflow-hidden');
        },
        closeLightbox() {
            this.open = false;
            document.body.classList.remove('overflow-hidden');
        },
        prevSlide() {
            if (this.slides.length === 0) return;
            this.index = (this.index - 1 + this.slides.length) % this.slides.length;
        },
        nextSlide() {
            if (this.slides.length === 0) return;
            this.index = (this.index + 1) % this.slides.length;
        },
    }"
>
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
                        <button
                            type="button"
                            class="h-full w-full cursor-zoom-in focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/70"
                            @click="openLightbox({{ $slideIndex }})"
                            aria-label="Открыть изображение в полноэкранном режиме"
                        >
                            <img src="{{ $slideUrl }}" alt="{{ $slideAlt !== '' ? $slideAlt : 'Изображение' }}" class="h-full w-full object-cover" loading="lazy">
                        </button>
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

    <template x-if="slides.length > 0">
        <div
            x-show="open"
            x-cloak
            x-transition.opacity
            class="fixed inset-0 z-[120] bg-base-content/90 backdrop-blur-sm"
            @keydown.escape.window="closeLightbox()"
        >
            <div class="absolute inset-0 flex items-center justify-center p-4 sm:p-6">
                <button
                    type="button"
                    class="btn btn-circle btn-sm absolute right-4 top-4 bg-base-100/90"
                    @click="closeLightbox()"
                    aria-label="Закрыть галерею"
                >
                    ✕
                </button>

                <button
                    type="button"
                    class="btn btn-circle btn-sm absolute left-4 top-1/2 -translate-y-1/2 bg-base-100/90"
                    @click="prevSlide()"
                    aria-label="Предыдущее изображение"
                >
                    ❮
                </button>

                <img
                    :src="slides[index]?.url"
                    :alt="slides[index]?.alt ?? 'Изображение'"
                    class="max-h-[88vh] max-w-[92vw] rounded-xl object-contain shadow-2xl"
                />

                <button
                    type="button"
                    class="btn btn-circle btn-sm absolute right-4 top-1/2 -translate-y-1/2 bg-base-100/90"
                    @click="nextSlide()"
                    aria-label="Следующее изображение"
                >
                    ❯
                </button>
            </div>
        </div>
    </template>
</div>
