@props([
    'value' => null,
    'mode' => 'absolute',
])

@php
    use App\Support\FormatsDateTime;

    $formatted = match ($mode) {
        'relative' => FormatsDateTime::relative($value),
        'date' => FormatsDateTime::date($value),
        default => FormatsDateTime::absolute($value),
    };

    $iso = FormatsDateTime::iso($value);
    $absoluteTitle = FormatsDateTime::absolute($value);
@endphp

@if (filled($formatted))
    <time
        {{ $attributes->merge(['class' => 'tabular-nums']) }}
        @if ($iso) datetime="{{ $iso }}" @endif
        @if ($absoluteTitle && $mode === 'relative') title="{{ $absoluteTitle }}" @endif
    >
        {{ $formatted }}
    </time>
@endif
