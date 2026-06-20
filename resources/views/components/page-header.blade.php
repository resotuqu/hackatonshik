@props([
    'eyebrow' => null,
    'title',
    'description' => null,
])

{{-- Единая шапка страницы: надзаголовок + h1 + описание, плюс слоты lead (под описанием) и actions (справа). --}}
<section {{ $attributes->class(['ui-page-header']) }}>
    <div class="flex flex-col gap-4 pb-4 md:flex-row md:items-end md:justify-between">
        <div class="min-w-0 space-y-2">
            @if ($eyebrow)
                <p class="t-eyebrow">{{ $eyebrow }}</p>
            @endif
            <h1 class="t-h1">{{ $title }}</h1>
            @if ($description)
                <p class="t-body max-w-2xl">{{ $description }}</p>
            @endif
            {{ $lead ?? '' }}
        </div>
        @isset($actions)
            <div class="flex flex-col items-start gap-3 md:items-end">
                {{ $actions }}
            </div>
        @endisset
    </div>
</section>
