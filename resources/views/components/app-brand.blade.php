<a
    href="{{ route('home') }}"
    {{ $attributes->merge(['class' => 'inline-flex shrink-0 items-center']) }}
    aria-label="Хакатонщик — на главную"
>
    <img
        src="/logo_white.svg"
        onerror="this.onerror=null;this.src='/logo.svg';"
        alt="Хакатонщик"
        class="{{ $imgClass }} block group-data-[theme=hackatonshik-light]:hidden"
        width="240"
        height="48"
        loading="eager"
        decoding="async"
    />
    <img
        src="/logo_black.svg"
        onerror="this.onerror=null;this.src='/logo.svg';"
        alt="Хакатонщик"
        class="{{ $imgClass }} hidden group-data-[theme=hackatonshik-light]:block"
        width="240"
        height="48"
        loading="eager"
        decoding="async"
    />
</a>
