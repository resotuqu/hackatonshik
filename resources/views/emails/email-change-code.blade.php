<x-mail::html.layout>
    <x-slot:header>
        <x-mail::html.header :url="config('app.url')">
            <img src="{{ asset('hackatonshik.svg') }}" alt="Хакатонщик" width="168" height="40" style="max-width: 168px; height: auto;">
        </x-mail::html.header>
    </x-slot:header>

    <h1 style="margin-top: 0;">Привет, {{ $user->fio ?? 'участник' }}!</h1>

    <p>{{ $intro }}</p>

    <p style="margin-bottom: 8px; font-size: 14px; color: #52525b;">Код подтверждения:</p>

    <x-mail::html.panel>
**{{ $code }}**
    </x-mail::html.panel>

    <x-slot:subcopy>
        <x-mail::html.subcopy>
            {{ $disclaimer }}
        </x-mail::html.subcopy>
    </x-slot:subcopy>

    <x-slot:footer>
        <x-mail::html.footer>
            © {{ date('Y') }} Хакатонщик. Все права защищены.
        </x-mail::html.footer>
    </x-slot:footer>
</x-mail::html.layout>
