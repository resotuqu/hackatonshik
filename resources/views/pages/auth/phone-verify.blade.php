@extends('layouts.app')

@section('title', __('ui.auth.phone_verify.page_title'))

@section('slot')
    <div class="mx-auto w-full max-w-5xl">
        <div class="grid grid-cols-1 items-start gap-4 lg:grid-cols-5">
            <x-auth-brand-panel
                :heading="__('ui.auth.phone_verify.brand_heading')"
                :subtitle="__('ui.auth.phone_verify.brand_subtitle')"
            >
                <x-auth-brand-panel.feature>{{ __('ui.auth.phone_verify.feature_call') }}</x-auth-brand-panel.feature>
                <x-auth-brand-panel.feature>{{ __('ui.auth.phone_verify.feature_code') }}</x-auth-brand-panel.feature>
                <x-auth-brand-panel.feature>{{ __('ui.auth.phone_verify.feature_auto') }}</x-auth-brand-panel.feature>
            </x-auth-brand-panel>

            <div class="card border border-base-300 bg-base-100 p-4 sm:p-6 lg:col-span-3"
                x-data="{ code: '', cooldown: 0, isSubmitting: false }"
                x-init="setInterval(() => { if (cooldown > 0) cooldown--; }, 1000)">

                @if ($needsPhone)
                    {{-- Шаг 0: ввод номера телефона --}}
                    <div class="space-y-5">
                        <div class="flex items-center gap-3">
                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-primary/10">
                                <x-app-icon icon="heroicons:phone" class="h-5 w-5 text-primary" />
                            </div>
                            <div>
                                <h2 class="font-semibold text-base-content">{{ __('ui.auth.phone_verify.card_title') }}</h2>
                                <p class="text-sm text-base-content/60">{{ __('ui.auth.phone_verify.enter_phone_description') }}</p>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('phone.verify.phone') }}" class="space-y-4">
                            @csrf
                            <div class="form-control">
                                <label class="label" for="phone">
                                    <span class="label-text font-medium">{{ __('ui.auth.phone_verify.phone_label') }}</span>
                                </label>
                                <div class="join w-full">
                                    <span class="join-item flex items-center border border-base-300 bg-base-200 px-4 font-medium text-base-content/70">+7</span>
                                    <input
                                        id="phone"
                                        type="tel"
                                        name="phone"
                                        value="{{ old('phone', auth()->user()?->phone) }}"
                                        class="input input-bordered join-item w-full @error('phone') input-error @enderror"
                                        placeholder="9991234567"
                                        autofocus
                                        required
                                    >
                                </div>
                                @error('phone')
                                    <p class="label-text-alt mt-1 text-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary w-full gap-2">
                                <x-app-icon icon="heroicons:arrow-right" class="h-4 w-4" />
                                {{ __('ui.auth.phone_verify.phone_submit') }}
                            </button>
                        </form>
                    </div>
                @else
                    {{-- Шаг 1-3: запрос звонка и ввод кода --}}
                    <div class="space-y-5">

                        {{-- Информация о номере --}}
                        <div class="flex items-start gap-3 rounded-xl border border-primary/20 bg-primary/5 px-4 py-3">
                            <x-app-icon icon="heroicons:phone-arrow-down-left" class="mt-0.5 h-5 w-5 shrink-0 text-primary" />
                            <p class="text-sm text-base-content/80">
                                {!! __('ui.auth.phone_verify.description', ['phone' => '<span class="font-semibold text-base-content">'.e(auth()->user()?->phone).'</span>']) !!}
                            </p>
                        </div>

                        {{-- Шаги --}}
                        <ul class="steps steps-horizontal w-full text-xs">
                            <li class="step step-primary">{{ __('ui.auth.phone_verify.step_request') }}</li>
                            <li class="step" :class="code.length > 0 ? 'step-primary' : ''">{{ __('ui.auth.phone_verify.step_code') }}</li>
                            <li class="step" :class="isSubmitting ? 'step-primary' : ''">{{ __('ui.auth.phone_verify.step_confirm') }}</li>
                        </ul>

                        {{-- Кнопка звонка --}}
                        <form method="POST" action="{{ route('phone.verify.send') }}" @submit="cooldown = 60">
                            @csrf
                            <button type="submit" class="btn btn-outline w-full gap-2" :disabled="cooldown > 0">
                                <template x-if="cooldown === 0">
                                    <x-app-icon icon="heroicons:phone-arrow-down-left" class="h-4 w-4" />
                                </template>
                                <template x-if="cooldown > 0">
                                    <span class="loading loading-spinner loading-xs"></span>
                                </template>
                                <span x-show="cooldown === 0">{{ __('ui.auth.phone_verify.call_btn') }}</span>
                                <span x-show="cooldown > 0" x-cloak>
                                    {{ __('ui.auth.phone_verify.call_cooldown', ['seconds' => '']) }}<span x-text="cooldown"></span>
                                </span>
                            </button>
                        </form>

                        <div class="divider my-0 text-xs text-base-content/40">{{ __('ui.auth.phone_verify.step_code') }}</div>

                        {{-- Форма ввода кода --}}
                        <form method="POST" action="{{ route('phone.verify') }}" class="space-y-3" x-ref="verifyForm">
                            @csrf
                            <div class="form-control">
                                <label class="label" for="code">
                                    <span class="label-text font-medium">{{ __('ui.auth.phone_verify.code_label') }}</span>
                                </label>
                                <input
                                    id="code"
                                    type="text"
                                    name="code"
                                    maxlength="4"
                                    inputmode="numeric"
                                    autocomplete="one-time-code"
                                    x-model="code"
                                    @input="if (code.length === 4) { isSubmitting = true; $refs.verifyForm.submit(); }"
                                    class="input input-bordered w-full text-center font-mono text-3xl tracking-[0.6em] @error('code') input-error @enderror"
                                    placeholder="····"
                                    required
                                >
                                @error('code')
                                    <p class="label-text-alt mt-1 text-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary w-full gap-2"
                                :disabled="isSubmitting || code.length < 4">
                                <span x-show="!isSubmitting">
                                    <x-app-icon icon="heroicons:check-circle" class="inline h-4 w-4 align-[-2px]" />
                                    {{ __('ui.auth.phone_verify.submit') }}
                                </span>
                                <span x-show="isSubmitting" x-cloak class="flex items-center gap-2">
                                    <span class="loading loading-spinner loading-sm"></span>
                                    Проверяем...
                                </span>
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
