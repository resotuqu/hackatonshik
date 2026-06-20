<div class="mx-auto w-full max-w-7xl space-y-6">
    <div class="text-sm breadcrumbs">
        <ul>
            <li><a href="/">Главная</a></li>
            <li><a href="{{ route('profile') }}">Профиль</a></li>
            <li class="opacity-70">Закладки</li>
        </ul>
    </div>

    <section class="ui-page-header">
        <div class="pb-5">
            <h1 class="ui-heading-display text-3xl font-bold sm:text-4xl">Мои закладки</h1>
            <p class="mt-1 text-base-content/70">Хакатоны, за которыми вы следите. Мы уведомим вас об изменении статуса и о скором старте.</p>
        </div>
    </section>

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
