<x-mail::html.layout>
    <x-slot:header>
        <x-mail::html.header :url="config('app.url')">
            <img src="{{ asset('hackatonshik.svg') }}" alt="Хакатонщик" width="168" height="40" style="max-width: 168px; height: auto;">
        </x-mail::html.header>
    </x-slot:header>

    <h1 style="margin-top: 0;">Приглашение судьёй</h1>

    <p>
        {{ $invitation->inviter?->fio ?? $invitation->inviter?->nickname ?? 'Организатор' }}
        приглашает вас стать судьёй хакатона «{{ $invitation->hackaton?->title ?? 'Хакатон' }}» на платформе Хакатонщик.
    </p>

    <p>Чтобы принять приглашение, войдите в аккаунт с этим email (или зарегистрируйтесь) и перейдите по кнопке ниже.</p>

    <x-mail::html.button :url="$acceptUrl" color="primary">
        Принять приглашение
    </x-mail::html.button>

    <x-slot:subcopy>
        <x-mail::html.subcopy>
            Если вы не ожидали это письмо — проигнорируйте его. Ссылка действительна, пока организатор не отменит приглашение.
        </x-mail::html.subcopy>
    </x-slot:subcopy>

    <x-slot:footer>
        <x-mail::html.footer>
            © {{ date('Y') }} Хакатонщик. Все права защищены.
        </x-mail::html.footer>
    </x-slot:footer>
</x-mail::html.layout>
