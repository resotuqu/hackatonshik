@extends('layouts.app')

@section('title', __('ui.auth.phone_verify.page_title'))

@section('slot')
    <div class="mx-auto w-full max-w-xl space-y-4" x-data="{ code: '', cooldown: 0, isSubmitting: false }" x-init="setInterval(() => { if (cooldown > 0) cooldown--; }, 1000)">
        <x-mary-card title="{{ __('ui.auth.phone_verify.card_title') }}" class="card border border-base-300 bg-base-100">
            @if ($needsPhone)
                <p class="text-sm text-base-content/70">
                    {{ __('ui.auth.phone_verify.enter_phone_description') }}
                </p>

                <form method="POST" action="{{ route('phone.verify.phone') }}" class="mt-4 space-y-3">
                    @csrf
                    <label class="form-control w-full">
                        <span class="label-text">{{ __('ui.auth.phone_verify.phone_label') }}</span>
                        <div class="join w-full">
                            <span class="join-item btn btn-disabled">+7</span>
                            <input
                                type="tel"
                                name="phone"
                                value="{{ old('phone', auth()->user()?->phone) }}"
                                inputmode="numeric"
                                class="input input-bordered join-item w-full"
                                placeholder="9991234567"
                                required
                            >
                        </div>
                    </label>
                    @error('phone')
                        <p class="text-sm text-error">{{ $message }}</p>
                    @enderror
                    <button type="submit" class="btn btn-primary w-full">{{ __('ui.auth.phone_verify.phone_submit') }}</button>
                </form>
            @else
                <p class="text-sm text-base-content/70">
                    {!! __('ui.auth.phone_verify.description', ['phone' => '<span class="font-medium">'.e(auth()->user()?->phone).'</span>']) !!}
                </p>

                <div class="mt-4 space-y-3">
                    <div class="steps steps-horizontal w-full">
                        <div class="step step-primary">{{ __('ui.auth.phone_verify.step_request') }}</div>
                        <div class="step" :class="code.length > 0 ? 'step-primary' : ''">{{ __('ui.auth.phone_verify.step_code') }}</div>
                        <div class="step" :class="isSubmitting ? 'step-primary' : ''">{{ __('ui.auth.phone_verify.step_confirm') }}</div>
                    </div>

                    <form method="POST" action="{{ route('phone.verify.send') }}" @submit="cooldown = 60">
                        @csrf
                        <button type="submit" class="btn btn-outline w-full" :disabled="cooldown > 0">
                            <span x-show="cooldown === 0">{{ __('ui.auth.phone_verify.call_btn') }}</span>
                            <span x-show="cooldown > 0">{{ __('ui.auth.phone_verify.call_cooldown', ['seconds' => '']) }}<span x-text="cooldown"></span></span>
                        </button>
                    </form>

                    <form method="POST" action="{{ route('phone.verify') }}" class="space-y-2" x-ref="verifyForm">
                        @csrf
                        <label class="form-control w-full">
                            <span class="label-text">{{ __('ui.auth.phone_verify.code_label') }}</span>
                            <input
                                type="text"
                                name="code"
                                maxlength="4"
                                inputmode="numeric"
                                x-model="code"
                                @input="if (code.length === 4) { isSubmitting = true; $refs.verifyForm.submit(); }"
                                class="input input-bordered w-full"
                                placeholder="1234"
                                required
                            >
                        </label>
                        @error('code')
                            <p class="text-sm text-error">{{ $message }}</p>
                        @enderror
                        <button type="submit" class="btn btn-primary w-full">{{ __('ui.auth.phone_verify.submit') }}</button>
                    </form>
                </div>
            @endif
        </x-mary-card>
    </div>
@endsection
