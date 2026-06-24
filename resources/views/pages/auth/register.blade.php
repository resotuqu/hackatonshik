
<div class="mx-auto w-full max-w-5xl">
    <div class="grid grid-cols-1 gap-4 lg:grid-cols-5">
        <x-auth-brand-panel
            :heading="$accountType === 'partner'
                ? __('ui.auth.register.brand_heading_partner')
                : __('ui.auth.register.brand_heading_participant')"
            :subtitle="$accountType === 'partner'
                ? __('ui.auth.register.brand_subtitle_partner')
                : __('ui.auth.register.brand_subtitle_participant')"
        >
            @if ($accountType === 'partner')
                <x-auth-brand-panel.feature>{{ __('ui.auth.register.feature_partner_1') }}</x-auth-brand-panel.feature>
                <x-auth-brand-panel.feature>{{ __('ui.auth.register.feature_partner_2') }}</x-auth-brand-panel.feature>
                <x-auth-brand-panel.feature>{{ __('ui.auth.register.feature_partner_3') }}</x-auth-brand-panel.feature>
            @else
                <x-auth-brand-panel.feature>{{ __('ui.auth.register.feature_participant_1') }}</x-auth-brand-panel.feature>
                <x-auth-brand-panel.feature>{{ __('ui.auth.register.feature_participant_2') }}</x-auth-brand-panel.feature>
                <x-auth-brand-panel.feature>{{ __('ui.auth.register.feature_participant_3') }}</x-auth-brand-panel.feature>
            @endif
        </x-auth-brand-panel>

        <x-maryform
            wire:submit.prevent="{{ $step < 4 ? 'nextStep' : 'save' }}"
            class="card border border-base-300 bg-base-100 p-4 sm:p-6 lg:col-span-3"
        >
            @php
                $progressPercent = (int) round(($step / 4) * 100);
            @endphp
            <x-mary-header title="{{ __('ui.auth.register.form_title') }}" separator />

            <ul class="steps steps-horizontal mb-6 w-full max-w-full flex-wrap justify-start gap-y-2 text-[0.65rem] sm:text-xs">
                <li class="step {{ $step >= 1 ? 'step-primary animate-step-active' : '' }}">{{ __('ui.auth.register.step_personal') }}</li>
                <li class="step {{ $step >= 2 ? 'step-primary animate-step-active' : '' }}">{{ __('ui.auth.register.step_account') }}</li>
                <li class="step {{ $step >= 3 ? 'step-primary animate-step-active' : '' }}">{{ __('ui.auth.register.step_password') }}</li>
                <li class="step {{ $step >= 4 ? 'step-primary animate-step-active' : '' }}">{{ __('ui.auth.register.step_phone') }}</li>
            </ul>
            <div class="mb-6">
                <div class="mb-1 flex items-center justify-between text-xs text-base-content/70">
                    <span>{{ __('ui.auth.register.progress_label') }}</span>
                    <span class="tabular-nums">{{ $progressPercent }}%</span>
                </div>
                <progress class="progress progress-primary h-2 w-full transition-all duration-300" value="{{ $progressPercent }}" max="100" aria-label="{{ __('ui.auth.register.progress_label') }}"></progress>
            </div>

            @if ($step === 1)
                <div class="animate-form-slide-in">
                {{-- Account type picker --}}
                <div class="mb-4">
                    <p class="mb-2 text-sm font-medium text-base-content/80">{{ __('ui.auth.register.account_type_label') }}</p>
                    <div class="grid grid-cols-2 gap-2">
                        <label class="cursor-pointer">
                            <input type="radio" wire:model.live="accountType" value="user" class="peer sr-only" />
                            <div class="flex flex-col items-center gap-1.5 rounded-xl border-2 px-3 py-3 text-center transition
                                peer-checked:border-primary peer-checked:bg-primary/5
                                border-base-300 bg-base-200/40 hover:border-base-400">
                                <x-app-icon icon="heroicons:user" class="h-6 w-6 text-base-content/70 peer-checked:text-primary" />
                                <span class="text-sm font-semibold">{{ __('ui.auth.register.account_type_participant') }}</span>
                                <span class="text-[11px] text-base-content/50">{{ __('ui.auth.register.account_type_participant_hint') }}</span>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" wire:model.live="accountType" value="partner" class="peer sr-only" />
                            <div class="flex flex-col items-center gap-1.5 rounded-xl border-2 px-3 py-3 text-center transition
                                peer-checked:border-secondary peer-checked:bg-secondary/5
                                border-base-300 bg-base-200/40 hover:border-base-400">
                                <x-app-icon icon="heroicons:building-office-2" class="h-6 w-6 text-base-content/70" />
                                <span class="text-sm font-semibold">{{ __('ui.auth.register.account_type_partner') }}</span>
                                <span class="text-[11px] text-base-content/50">{{ __('ui.auth.register.account_type_partner_hint') }}</span>
                            </div>
                        </label>
                    </div>
                    @error('accountType')
                        <p class="mt-1 text-xs text-error">{{ $message }}</p>
                    @enderror
                </div>

                <p class="text-xs text-base-content/70 mb-2 rounded-lg border border-base-300 bg-base-200/40 px-3 py-2">
                    {{ __('ui.auth.register.unsaved_warning') }}
                </p>
                <x-mary-input label="{{ __('ui.auth.register.fio_label') }}" wire:model="fio" placeholder="Владимир" hint="{{ __('ui.auth.register.fio_hint') }}" />
                <x-marydatetime label="{{ __('ui.auth.register.dob_label') }}" hint="{{ __('ui.auth.register.dob_hint') }}" wire:model="date_of_birth" />

                @if ($accountType === 'partner')
                    <div class="mt-4 space-y-3 rounded-xl border border-secondary/20 bg-secondary/5 p-4">
                        <p class="text-sm font-semibold text-base-content">Данные для заявки организатора</p>
                        <p class="text-xs text-base-content/70">После регистрации заявка будет рассмотрена администратором.</p>

                        <div>
                            <p class="mb-2 text-sm font-medium text-base-content/80">Тип организатора</p>
                            <div class="grid grid-cols-2 gap-2">
                                <label class="cursor-pointer">
                                    <input type="radio" wire:model.live="organizerEntityType" value="individual" class="peer sr-only" />
                                    <div class="rounded-xl border-2 border-base-300 px-3 py-2 text-center text-sm transition peer-checked:border-secondary peer-checked:bg-secondary/10">
                                        Физическое лицо
                                    </div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" wire:model.live="organizerEntityType" value="company" class="peer sr-only" />
                                    <div class="rounded-xl border-2 border-base-300 px-3 py-2 text-center text-sm transition peer-checked:border-secondary peer-checked:bg-secondary/10">
                                        Юридическое лицо
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
                @endif
                </div>
            @endif

            @if ($step === 2)
                <div class="animate-form-slide-in">
                <x-mary-input label="{{ __('ui.auth.register.email_label') }}" wire:model="email" placeholder="example@mail.com"
                    hint="{{ __('ui.auth.register.email_hint') }}" />
                <x-mary-input label="{{ __('ui.auth.register.nickname_label') }}" wire:model="nickname" placeholder="vova_vlad_123" hint="{{ __('ui.auth.register.nickname_hint') }}" />
                </div>
            @endif

            @if ($step === 3)
                <div class="animate-form-slide-in">
                <div x-data="{ password: @entangle('password').live }" class="space-y-2">
                    <x-marypassword label="{{ __('ui.auth.register.password_label') }}" wire:model="password" />
                    <div class="space-y-1">
                        <div class="h-2 w-full rounded-full bg-base-300">
                            <div
                                class="h-2 rounded-full transition-all"
                                :class="password.length >= 12 ? 'bg-success' : (password.length >= 8 ? 'bg-warning' : 'bg-error')"
                                :style="`width: ${Math.min(100, Math.max(15, password.length * 8))}%`"
                            ></div>
                        </div>
                        <p class="text-xs text-base-content/70"
                            x-text="password.length >= 12 ? '{{ __('ui.auth.register.password_strong') }}' : (password.length >= 8 ? '{{ __('ui.auth.register.password_medium') }}' : '{{ __('ui.auth.register.password_weak') }}')"
                        ></p>
                    </div>
                </div>
                <x-marypassword label="{{ __('ui.auth.register.password_confirm_label') }}" wire:model="password_confirmation" />
                </div>
            @endif

            @if ($step === 4)
                <div class="animate-form-slide-in">
                <x-mary-input label="{{ __('ui.auth.register.phone_label') }}" wire:model.blur="phone" placeholder="+79991234567" />
                <div class="mt-4 space-y-1">
                    <label class="flex cursor-pointer items-start gap-3">
                        <input type="checkbox" wire:model="pd_consent" class="checkbox checkbox-primary mt-0.5 shrink-0" />
                        <span class="text-sm text-base-content/80">{!! __('ui.auth.register.pd_consent_label') !!}</span>
                    </label>
                    @error('pd_consent')
                        <p class="text-xs text-error">{{ __('ui.auth.register.pd_consent_error') }}</p>
                    @enderror
                </div>
                </div>
            @endif

            <x-slot:actions class="w-full">
                <div class="flex w-full flex-col gap-2 sm:flex-row sm:justify-end">
                    @if ($step > 1)
                        <x-marybutton class="btn-outline w-full sm:w-auto transition-all duration-200 hover:scale-105 active:scale-95" label="{{ __('ui.auth.register.btn_back') }}" type="button" wire:click="previousStep" wire:loading.attr="disabled" wire:target="previousStep,nextStep,save" />
                    @endif
                    @if ($step < 4)
                        <x-marybutton class="btn-primary w-full sm:min-w-40 transition-all duration-200 hover:scale-105 active:scale-95" label="{{ __('ui.auth.register.btn_next') }}" type="submit" wire:loading.attr="disabled" wire:target="nextStep,save" />
                    @else
                        <x-marybutton class="btn-primary w-full sm:min-w-40 transition-all duration-200 hover:scale-105 active:scale-95" label="{{ __('ui.auth.register.btn_submit') }}" type="submit" wire:loading.attr="disabled" wire:target="save" spinner="save" />
                    @endif
                </div>
            </x-slot:actions>
            <x-oauth-buttons mode="register" />
        </x-maryform>
    </div>
</div>
