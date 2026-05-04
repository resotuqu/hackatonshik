<x-mail::html.layout>
    <x-slot:header>
        <x-mail::html.header :url="config('app.url')">
            <img src="{{ asset('hackatonshik.svg') }}" alt="Хакатонщик" width="168" height="40" style="max-width: 168px; height: auto;">
        </x-mail::html.header>
    </x-slot:header>

    <h1 style="margin-top: 0;">Привет, {{ $user->fio ?? 'участник' }}!</h1>

    <p>Добро пожаловать на платформу Хакатонщик!</p>
    <p>Для завершения регистрации и получения полного доступа к командам, хакатонам и возможностям — подтверди email.</p>

    <x-mail::html.button :url="$url" color="primary">
        Подтвердить email
    </x-mail::html.button>

    <x-slot:subcopy>
        <x-mail::html.subcopy>
            Если вы не создавали аккаунт — просто проигнорируйте это письмо.
        </x-mail::html.subcopy>
    </x-slot:subcopy>

    <x-slot:footer>
        <x-mail::html.footer>
            © {{ date('Y') }} Хакатонщик. Все права защищены.
        </x-mail::html.footer>
    </x-slot:footer>
</x-mail::html.layout>
