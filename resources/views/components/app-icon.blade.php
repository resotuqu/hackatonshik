@props([
    'icon',
    'label' => null,
])

<iconify-icon
    icon="{{ $icon }}"
    inline
    height="unset"
    @if ($label)
        role="img"
        aria-label="{{ $label }}"
    @else
        aria-hidden="true"
    @endif
    {{ $attributes->class('inline-block shrink-0 align-middle') }}
></iconify-icon>
