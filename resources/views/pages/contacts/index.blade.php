
<div class="mx-auto mt-8 w-full max-w-6xl space-y-8 sm:mt-12 sm:space-y-12">

    <section class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <article class="space-y-4">
            <h1 class="font-display text-3xl font-bold">Контакты</h1>
            <p class="text-base-content/70">Свяжитесь с нами удобным способом</p>
            <div class="space-y-3 text-base">
                <p>
                    Email:
                    <a class="link link-hover font-medium" href="mailto:sekhmych@yandex.ru">sekhmych@yandex.ru</a>
                </p>
                <p>
                    Телефон:
                    <a class="link link-hover font-medium" href="tel:+79248605316">+7 (924) 860-53-16</a>
                </p>
                <p>
                    Telegram:
                    <a class="link link-hover font-medium" href="https://t.me/hackatonshik" target="_blank" rel="noopener noreferrer">@hackatonshik</a>
                </p>
            </div>
        </article>

        <x-maryform wire:submit="send" class="card border border-base-300 bg-base-100 p-4 sm:p-6">
            <x-mary-header title="Форма обратной связи" separator />
            <x-mary-input label="Ваше имя" wire:model="name" />
            <x-mary-input label="Email" wire:model="email" />
            <x-mary-input label="Тема" wire:model="subject" />
            <x-mary-input label="Telegram (опционально)" wire:model="telegram" placeholder="@username" />
            <x-marytextarea label="Сообщение" wire:model="message" rows="5" />
            <x-slot:actions>
                <x-mary-button type="submit" label="Отправить сообщение" class="btn-neutral" />
            </x-slot:actions>
        </x-maryform>
    </section>

    @if (filled(env('YANDEX_MAPS_EMBED_URL')) || filled(env('GOOGLE_MAPS_EMBED_URL')))
        <section class="ui-surface-card p-4 sm:p-6">
            <h2 class="font-display text-2xl font-bold">Мы на карте</h2>
            <div class="mt-4 overflow-hidden rounded-card border border-base-300">
                <iframe
                    src="{{ env('YANDEX_MAPS_EMBED_URL', env('GOOGLE_MAPS_EMBED_URL')) }}"
                    title="Карта офиса Хакатонщика"
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                    class="h-[360px] w-full"
                    allowfullscreen
                ></iframe>
            </div>
        </section>
    @endif
</div>
