<a
    href="{{ route('home') }}"
    {{ $attributes->merge(['class' => 'inline-flex shrink-0 items-center']) }}
    aria-label="Хакатонщик — на главную"
>
    <img src="/logo.svg" alt="Хакатонщик" class="{{ $imgClass }}" width="200" height="40" loading="eager" decoding="async" />
</a>
