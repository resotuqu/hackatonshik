@props([
    'items' => [],
])

@if (!empty($items))
    @php
        $schemaItems = collect($items)->map(fn ($item, $i) => [
            '@type' => 'ListItem',
            'position' => $i + 1,
            'name' => $item['label'],
            'item' => isset($item['href']) ? url($item['href']) : null,
        ])->values()->all();
    @endphp

    @php
        $jsonLdScript = json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $schemaItems,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    @endphp
    <script type="application/ld+json">{!! $jsonLdScript !!}</script>

    <nav {{ $attributes->class(['text-sm breadcrumbs']) }} aria-label="Хлебные крошки">
        <ul>
            @foreach ($items as $i => $item)
                @php $isLast = $i === count($items) - 1; @endphp
                <li @class(['opacity-70' => $isLast])>
                    @if (!$isLast && isset($item['href']))
                        <a href="{{ $item['href'] }}" wire:navigate>{{ $item['label'] }}</a>
                    @else
                        {{ $item['label'] }}
                    @endif
                </li>
            @endforeach
        </ul>
    </nav>
@endif
