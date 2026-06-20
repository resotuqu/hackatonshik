@props([
    'active' => null,
])

@auth
    @php
        $navUser = auth()->user();
        $hackatonsLabel = \App\Support\ProfileNavigation::hackatonsTabLabel($navUser);
        $hackatonsHref = \App\Support\ProfileNavigation::hackatonsTabHref($navUser);
        $hackatonsActive = \App\Support\ProfileNavigation::isHackatonsTabActive($navUser, $active);
    @endphp
    <div {{ $attributes->merge(['class' => 'tabs tabs-bordered w-full overflow-x-auto border-b border-base-200']) }}>
        <a @class(['tab', 'tab-active' => $active === 'personal' || ($active === null && request()->routeIs('profile'))]) href="{{ route('profile') }}">Личные данные</a>
        <a @class(['tab', 'tab-active' => $active === 'teams' || ($active === null && request()->routeIs('profile.teams'))]) href="{{ route('profile.teams') }}">Мои команды</a>
        <a @class(['tab', 'tab-active' => $hackatonsActive]) href="{{ $hackatonsHref }}">{{ $hackatonsLabel }}</a>
        <a @class(['tab', 'tab-active' => $active === 'certificates' || ($active === null && request()->routeIs('profile.certificates'))]) href="{{ route('profile.certificates') }}">Сертификаты</a>
    </div>
@endauth
