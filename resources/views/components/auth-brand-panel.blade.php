@props([
    'heading',
    'subtitle' => null,
])

<section {{ $attributes->class(['card flex flex-col justify-start gap-6 border border-base-300 bg-base-100 p-4 sm:p-6 lg:col-span-2']) }}>
    <div class="space-y-3">
        <p class="text-xs font-semibold uppercase tracking-widest text-base-content/50">{{ __('ui.auth.brand_name') }}</p>
        <h2 class="text-2xl font-semibold leading-tight text-base-content">{{ $heading }}</h2>
        @if ($subtitle)
            <p class="text-sm text-base-content/70">{{ $subtitle }}</p>
        @endif
    </div>
    <div class="grid gap-2 text-sm">
        {{ $slot }}
    </div>
</section>
