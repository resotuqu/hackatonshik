<div class="flex items-center justify-between gap-3 rounded-xl px-3 py-3 text-sm font-medium leading-snug text-base-content">
    <span class="text-[0.9375rem]">{{ __('ui.nav.language') }}</span>
    <div class="join" role="group" aria-label="{{ __('ui.locale.switcher_aria') }}">
        <button
            type="button"
            wire:click="switch('ru')"
            @class([
                'join-item btn btn-xs',
                'btn-primary' => $current === 'ru',
                'btn-ghost border border-base-300' => $current !== 'ru',
            ])
            aria-pressed="{{ $current === 'ru' ? 'true' : 'false' }}"
            aria-label="{{ __('ui.locale.switch_to_ru') }}"
        >RU</button>
        <button
            type="button"
            wire:click="switch('en')"
            @class([
                'join-item btn btn-xs',
                'btn-primary' => $current === 'en',
                'btn-ghost border border-base-300' => $current !== 'en',
            ])
            aria-pressed="{{ $current === 'en' ? 'true' : 'false' }}"
            aria-label="{{ __('ui.locale.switch_to_en') }}"
        >EN</button>
    </div>
</div>
