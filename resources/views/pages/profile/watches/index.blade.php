<div class="mx-auto w-full max-w-7xl space-y-6">
    <nav class="text-sm breadcrumbs" aria-label="{{ __('ui.breadcrumbs.aria_label') }}">
        <ul>
            <li><a href="/">{{ __('ui.nav.home') }}</a></li>
            <li><a href="{{ route('profile') }}">{{ __('ui.nav.profile') }}</a></li>
            <li class="opacity-70">{{ __('ui.nav.watches') }}</li>
        </ul>
    </nav>

    <x-page-header
        title="{{ __('ui.nav.watches') }}"
        description="Хакатоны, за которыми вы следите. Мы уведомим вас об изменении статуса и о скором старте."
    />

    @if ($watchedHackatons->isEmpty())
        <x-empty-state
            title="Закладок пока нет"
            description="Нажмите «Следить» на странице хакатона, чтобы получать уведомления."
            icon="heroicons:bookmark"
            action-href="/hackatons"
            action-label="Каталог хакатонов"
        />
    @else
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($watchedHackatons as $hackaton)
                <x-hackaton-card
                    :hackaton="$hackaton"
                    href="{{ route('hackatons.show', $hackaton) }}"
                />
            @endforeach
        </div>

        <div class="mt-6">
            {{ $watchedHackatons->links() }}
        </div>
    @endif
</div>
