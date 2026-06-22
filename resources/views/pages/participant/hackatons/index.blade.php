<div class="mx-auto w-full max-w-6xl space-y-8">
    <nav class="text-sm breadcrumbs">
        <ul>
            <li><a href="{{ route('home') }}" wire:navigate>Главная</a></li>
            <li><a href="{{ route('profile') }}" wire:navigate>Профиль</a></li>
            <li class="opacity-70">Мои заявки и хакатоны</li>
        </ul>
    </nav>

    <x-profile-nav-tabs active="hackatons" />

    <header class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="ui-heading-display text-2xl font-bold sm:text-3xl">Мои заявки и хакатоны</h1>
            <p class="mt-1 text-sm text-base-content/70">Заявки ваших команд, участие в событиях и быстрые переходы в рабочие пространства.</p>
        </div>
        <div class="flex shrink-0 flex-wrap gap-2">
            <a href="{{ route('hackatons.index') }}" class="ui-cta-outline btn-sm" wire:navigate>Каталог</a>
            <a href="{{ route('teams.create') }}" class="ui-cta-primary btn-sm" wire:navigate>Создать команду</a>
        </div>
    </header>

    @include('pages.home.dashboard._participant', ['context' => 'full'])
</div>
