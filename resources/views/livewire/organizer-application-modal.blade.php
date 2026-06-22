<div>
    @if ($applicationStatus !== null)
        @if (! $showModal)
            <div
                class="alert alert-info mb-4 shadow-sm"
                data-test="organizer-application-banner"
            >
                <x-app-icon icon="heroicons:building-office-2" class="h-5 w-5 shrink-0" />
                <div class="flex flex-1 flex-wrap items-center justify-between gap-2">
                    <span class="text-sm">
                        @if ($applicationStatus === \App\Enums\OrganizerApplicationStatus::Pending)
                            {{ __('ui.organizer_application.banner_pending') }}
                        @else
                            {{ __('ui.organizer_application.banner_rejected') }}
                        @endif
                    </span>
                    <button type="button" class="btn btn-ghost btn-xs" wire:click="openModal">
                        {{ __('ui.organizer_application.details') }}
                    </button>
                </div>
            </div>
        @endif

        <x-mary-modal
            wire:model="showModal"
            class="backdrop-blur"
            :title="$applicationStatus === \App\Enums\OrganizerApplicationStatus::Pending ? __('ui.organizer_application.modal_title_pending') : __('ui.organizer_application.modal_title_resubmit')"
        >
            @if ($applicationStatus === \App\Enums\OrganizerApplicationStatus::Pending)
                <p class="text-sm text-base-content/80">
                    {{ __('ui.organizer_application.modal_body_pending') }}
                </p>

                <div class="mt-4 flex justify-end">
                    <x-mary-button :label="__('ui.organizer_application.modal_ok')" class="btn-primary" type="button" wire:click="closeModal" />
                </div>
            @else
                @if (filled($adminNote))
                    <div class="alert alert-warning mb-4 text-sm" data-test="organizer-application-admin-note">
                        <span><strong>{{ __('ui.organizer_application.admin_note') }}</strong> {{ $adminNote }}</span>
                    </div>
                @endif

                <div class="space-y-3">
                    <div>
                        <p class="mb-2 text-sm font-medium text-base-content/80">{{ __('ui.organizer_application.entity_type_label') }}</p>
                        <div class="grid grid-cols-2 gap-2">
                            <label class="cursor-pointer">
                                <input type="radio" wire:model.live="organizerEntityType" value="individual" class="peer sr-only" />
                                <div class="rounded-xl border-2 border-base-300 px-3 py-2 text-center text-sm transition peer-checked:border-secondary peer-checked:bg-secondary/10">
                                    {{ __('ui.organizer_application.entity_individual') }}
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" wire:model.live="organizerEntityType" value="company" class="peer sr-only" />
                                <div class="rounded-xl border-2 border-base-300 px-3 py-2 text-center text-sm transition peer-checked:border-secondary peer-checked:bg-secondary/10">
                                    {{ __('ui.organizer_application.entity_company') }}
                                </div>
                            </label>
                        </div>
                        @error('organizerEntityType')
                            <p class="mt-1 text-xs text-error">{{ $message }}</p>
                        @enderror
                    </div>

                    @if ($organizerEntityType === 'company')
                        <x-mary-input
                            label="Название компании"
                            wire:model="organizerCompanyName"
                            placeholder="ООО «Пример»"
                        />
                    @endif

                    <x-marytextarea
                        label="Примечание"
                        wire:model="organizerNote"
                        placeholder="Кто вы как организатор, из какой компании или как физлицо планируете проводить хакатоны"
                        rows="4"
                        hint="Минимум 20 символов"
                    />
                </div>

                <div class="mt-4 flex flex-wrap justify-end gap-2">
                    <x-mary-button label="Отмена" class="btn-ghost" type="button" wire:click="closeModal" />
                    <x-mary-button label="Отправить повторно" class="btn-primary" type="button" wire:click="resubmit" />
                </div>
            @endif
        </x-mary-modal>
    @endif
</div>
