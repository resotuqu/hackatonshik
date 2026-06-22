<div class="mx-auto w-full max-w-lg">
    <x-mary-card :title="__('ui.auth.oauth_consent.title')" class="border border-base-300 bg-base-100 shadow-sm">
        <p class="mb-4 text-sm text-base-content/80">{{ __('ui.auth.oauth_consent.description') }}</p>

        @if (auth()->user()?->date_of_birth === null)
            <x-mary-input
                type="date"
                :label="__('ui.auth.register.dob_label')"
                wire:model="date_of_birth"
                class="mb-4"
            />
        @endif

        <label class="flex cursor-pointer items-start gap-3">
            <input type="checkbox" wire:model="pd_consent" class="checkbox checkbox-primary mt-1" />
            <span class="text-sm leading-relaxed">{!! __('ui.auth.register.pd_consent_label') !!}</span>
        </label>
        @error('pd_consent')
            <p class="mt-2 text-xs text-error">{{ $message }}</p>
        @enderror

        <div class="mt-6 flex justify-end">
            <x-mary-button
                :label="__('ui.auth.oauth_consent.submit')"
                class="btn-primary"
                type="button"
                wire:click="save"
                spinner="save"
            />
        </div>
    </x-mary-card>
</div>
