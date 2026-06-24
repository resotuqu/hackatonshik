@extends('layouts.app')

@section('title', __('ui.auth.two_factor.page_title'))

@section('slot')
    <div class="mx-auto w-full max-w-md space-y-4" x-data="{ recovery: false }">
        <x-mary-card title="{{ __('ui.auth.two_factor.card_title') }}" class="card border border-base-300 bg-base-100">
            <p class="text-sm text-base-content/70" x-show="!recovery" role="status" aria-live="polite">
                {{ __('ui.auth.two_factor.description_code') }}
            </p>
            <p class="text-sm text-base-content/70" x-cloak x-show="recovery" role="status" aria-live="polite">
                {{ __('ui.auth.two_factor.description_recovery') }}
            </p>

            @if ($errors->any())
                <div class="alert alert-error mt-4 text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('two-factor.login.store') }}" class="mt-4 space-y-4">
                @csrf

                <div x-show="!recovery">
                    <input
                        type="text"
                        name="code"
                        inputmode="numeric"
                        autocomplete="one-time-code"
                        autofocus
                        placeholder="{{ __('ui.auth.two_factor.code_placeholder') }}"
                        class="input input-bordered w-full tracking-widest"
                    />
                </div>

                <div x-cloak x-show="recovery">
                    <input
                        type="text"
                        name="recovery_code"
                        autocomplete="one-time-code"
                        placeholder="{{ __('ui.auth.two_factor.recovery_placeholder') }}"
                        class="input input-bordered w-full"
                    />
                </div>

                <button type="submit" class="btn btn-primary w-full">{{ __('ui.auth.two_factor.submit') }}</button>
            </form>

            <button type="button" class="link link-primary mt-4 text-xs"
                x-on:click="recovery = !recovery"
                aria-pressed="false">
                <span x-show="!recovery">{{ __('ui.auth.two_factor.use_recovery') }}</span>
                <span x-show="recovery">{{ __('ui.auth.two_factor.use_app') }}</span>
            </button>
        </x-mary-card>
    </div>
@endsection
