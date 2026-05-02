<a
    href="{{ route('home') }}"
    {{ $attributes->merge(['class' => 'inline-flex shrink-0 items-center']) }}
    aria-label="Хакатонщик — на главную"
>
    <img
        src="/brand/4.png"
        onerror="this.onerror=null;this.src='/logo.svg';"
        alt="Хакатонщик"
        class="{{ $imgClass }}"
        width="240"
        height="48"
        loading="eager"
        decoding="async"
    />
</a>
