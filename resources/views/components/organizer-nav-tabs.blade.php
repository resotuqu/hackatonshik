@props(['active' => null])

@auth
    <div {{ $attributes->merge(['class' => 'tabs tabs-bordered w-full overflow-x-auto border-b border-base-200']) }}>
        <a @class(['tab', 'tab-active' => $active === 'dashboard' || ($active === null && request()->routeIs('organizer.dashboard', 'profile.organizer', 'profile.hackatons'))]) href="{{ route('organizer.dashboard') }}">Мои хакатоны</a>
        <a @class(['tab', 'tab-active' => $active === 'applications' || ($active === null && request()->routeIs('organizer.applications', 'profile.hackatons.applications'))]) href="{{ route('organizer.applications') }}">Заявки</a>
        <a @class(['tab', 'tab-active' => $active === 'scoring' || ($active === null && request()->routeIs('organizer.scoring', 'profile.hackatons.scoring'))]) href="{{ route('organizer.scoring') }}">Оценивание</a>
        <a @class(['tab', 'tab-active' => $active === 'finished' || ($active === null && request()->routeIs('organizer.finished', 'profile.hackatons.finished'))]) href="{{ route('organizer.finished') }}">Завершённые</a>
    </div>
@endauth
