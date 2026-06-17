<a href="{{ route('home') }}"
    {{ $attributes->merge(['class' => $wide ? 'flex w-full min-w-0 items-stretch justify-start' : 'inline-flex shrink-0 items-center']) }}
    aria-label="Хакатонщик — на главную">
    <img src="/logo_white.svg" onerror="this.onerror=null;this.src='/logo.svg';" alt="Хакатонщик"
        class="{{ $imgClass }} block group-data-[theme=hackatonshik-light]:hidden"
        @if ($wide) width="1500"
            height="750"
        @else
            width="240"
            height="48" @endif
        loading="eager" decoding="async" />
    <img src="/logo_black.svg" onerror="this.onerror=null;this.src='/logo.svg';" alt="Хакатонщик"
        class="{{ $imgClass }} hidden group-data-[theme=hackatonshik-light]:block"
        @if ($wide) width="1500"
            height="750"
        @else
            width="240"
            height="48" @endif
        loading="eager" decoding="async" />
</a>
