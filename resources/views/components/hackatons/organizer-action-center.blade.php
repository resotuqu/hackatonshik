@props([
    'hackaton',
    'modals' => [],
])

<section class="ui-surface-card p-4 sm:p-5" aria-label="Быстрые действия организатора">
    <h2 class="text-sm font-bold uppercase tracking-widest text-base-content/50">Центр действий</h2>
    <div class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-3">
        <button
            type="button"
            class="ui-cta-secondary btn-md h-auto min-h-[4rem] flex-col justify-center gap-1 py-3 text-left sm:flex-row sm:items-center sm:gap-3"
            data-organizer-action="tab"
            data-tab-target="organization"
        >
            <x-app-icon icon="heroicons:user-plus" class="h-6 w-6 shrink-0" />
            <span class="font-semibold">Пригласить судей</span>
        </button>

        <button
            type="button"
            class="ui-cta-secondary btn-md h-auto min-h-[4rem] flex-col justify-center gap-1 py-3 text-left sm:flex-row sm:items-center sm:gap-3"
            data-organizer-action="tab"
            data-tab-target="announcements"
        >
            <x-app-icon icon="heroicons:megaphone" class="h-6 w-6 shrink-0" />
            <span class="font-semibold">Опубликовать анонс</span>
        </button>

        <button
            type="button"
            class="ui-cta-secondary btn-md h-auto min-h-[4rem] flex-col justify-center gap-1 py-3 text-left sm:flex-row sm:items-center sm:gap-3"
            data-organizer-action="tab"
            data-tab-target="cases"
        >
            <x-app-icon icon="heroicons:puzzle-piece" class="h-6 w-6 shrink-0" />
            <span class="font-semibold">Создать / опубликовать кейс</span>
        </button>

        <a
            href="{{ route('hackatons.export.participants', $hackaton) }}"
            class="ui-cta-outline btn-md h-auto min-h-[4rem] flex-col justify-center gap-1 border-base-300 py-3 sm:flex-row sm:items-center sm:gap-3"
        >
            <x-app-icon icon="heroicons:arrow-down-tray" class="h-6 w-6 shrink-0" />
            <span class="font-semibold">Экспорт участников</span>
        </a>

        <button
            type="button"
            class="ui-cta-outline btn-md h-auto min-h-[4rem] flex-col justify-center gap-1 border-base-300 py-3 text-left sm:flex-row sm:items-center sm:gap-3"
            data-organizer-action="tab"
            data-tab-target="organization"
            data-open-modal="{{ $modals['certificate_upload'] ?? '' }}"
        >
            <x-app-icon icon="heroicons:academic-cap" class="h-6 w-6 shrink-0" />
            <span class="font-semibold">Выдать сертификаты</span>
        </button>
    </div>
</section>
