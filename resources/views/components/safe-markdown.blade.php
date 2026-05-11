@props([
    'content' => '',
])

<div {{ $attributes->class(['markdown-body']) }}>
    {!! \App\Support\SafeMarkdown::toHtml($content) !!}
</div>
