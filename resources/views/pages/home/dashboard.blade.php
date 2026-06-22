@php
    $user = auth()->user();
@endphp

@if ($user->isParticipant())
    @include('pages.home.dashboard._participant', ['context' => 'home'])
@endif

@if ($user->isOrganizer())
    @include('pages.home.dashboard._organizer')
@endif

@if ($user->isJudge())
    @include('pages.home.dashboard._judge')
@endif

@if ($user->isAdmin())
    <section class="space-y-6" data-test="home-admin-dashboard">
        <h2 class="ui-heading-display text-xl font-semibold">Администратор</h2>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <x-marycard title="Пользователей" class="card border border-base-300 bg-base-100">
                <p class="text-3xl font-semibold tabular-nums">{{ $usersCount }}</p>
            </x-marycard>
            <x-marycard title="Хакатонов" class="card border border-base-300 bg-base-100">
                <p class="text-3xl font-semibold tabular-nums">{{ $adminHackatonsCount }}</p>
            </x-marycard>
            <x-marycard title="Партнёров" class="card border border-base-300 bg-base-100">
                <p class="text-3xl font-semibold tabular-nums">{{ $adminPartnersCount }}</p>
            </x-marycard>
            <x-marycard title="Заявок команд на рассмотрении" class="card border border-base-300 bg-base-100">
                <p class="text-3xl font-semibold tabular-nums">{{ $adminPendingApplicationsCount }}</p>
                <p class="mt-2 text-sm text-base-content/70">По всей платформе.</p>
            </x-marycard>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('admin.dashboard') }}" class="ui-cta-primary">Админ-панель</a>
            <a href="{{ route('hackatons.index') }}" class="ui-cta-outline">Хакатоны</a>
        </div>
    </section>
@endif

@if (! $user->isParticipant() && ! $user->isOrganizer() && ! $user->isJudge() && ! $user->isAdmin())
    <p class="text-base-content/80">Выберите раздел в меню слева или перейдите к хакатонам.</p>
    <div class="flex flex-wrap gap-3">
        <a href="/hackatons" class="ui-cta-primary">Хакатоны</a>
        <a href="/profile" class="ui-cta-outline">Профиль</a>
    </div>
@endif
