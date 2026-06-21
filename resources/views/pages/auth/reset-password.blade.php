@extends('layouts.app')

@section('title', __('ui.auth.reset_password.page_title'))

@section('slot')
    <div class="mx-auto w-full max-w-xl space-y-4">
        <x-mary-card title="{{ __('ui.auth.reset_password.card_title') }}" class="card border border-base-300 bg-base-100">
            <p class="text-sm text-base-content/70">
                {{ __('ui.auth.reset_password.description') }}
            </p>

            <form method="POST" action="{{ route('password.update') }}" class="mt-4 space-y-4">
                @csrf
                <input type="hidden" name="token" value="{{ $request->route('token') }}" />

                <div class="form-control w-full">
                    <label class="label" for="email">
                        <span class="label-text">{{ __('ui.auth.reset_password.email_label') }}</span>
                    </label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email', $request->email) }}"
                        required
                        autocomplete="username"
                        class="input input-bordered w-full @error('email') input-error @enderror"
                    />
                    @error('email')
                        <p class="label-text-alt text-error mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-control w-full">
                    <label class="label" for="password">
                        <span class="label-text">{{ __('ui.auth.reset_password.password_label') }}</span>
                    </label>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        required
                        autocomplete="new-password"
                        class="input input-bordered w-full @error('password') input-error @enderror"
                    />
                    @error('password')
                        <p class="label-text-alt text-error mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-control w-full">
                    <label class="label" for="password_confirmation">
                        <span class="label-text">{{ __('ui.auth.reset_password.password_confirm_label') }}</span>
                    </label>
                    <input
                        id="password_confirmation"
                        type="password"
                        name="password_confirmation"
                        required
                        autocomplete="new-password"
                        class="input input-bordered w-full"
                    />
                </div>

                <button type="submit" class="btn btn-primary w-full">{{ __('ui.auth.reset_password.submit') }}</button>
            </form>

            <p class="mt-4 text-center text-sm text-base-content/70">
                <a href="{{ route('login') }}" class="link link-primary">{{ __('ui.auth.reset_password.back_to_login') }}</a>
            </p>
        </x-mary-card>
    </div>
@endsection
