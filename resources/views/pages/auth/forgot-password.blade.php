@extends('layouts.app')

@section('title', __('ui.auth.forgot_password.page_title'))

@section('slot')
    <div class="mx-auto w-full max-w-xl space-y-4">
        <x-mary-card title="{{ __('ui.auth.forgot_password.card_title') }}" class="card border border-base-300 bg-base-100">
            <p class="text-sm text-base-content/70">
                {{ __('ui.auth.forgot_password.description') }}
            </p>

            @if (session('status'))
                <div class="alert alert-success mt-4 text-sm">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="mt-4 space-y-4">
                @csrf
                <div class="form-control w-full">
                    <label class="label" for="email">
                        <span class="label-text">{{ __('ui.auth.forgot_password.email_label') }}</span>
                    </label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autocomplete="email"
                        autofocus
                        class="input input-bordered w-full @error('email') input-error @enderror"
                    />
                    @error('email')
                        <p class="label-text-alt text-error mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary w-full">{{ __('ui.auth.forgot_password.submit') }}</button>
            </form>

            <p class="mt-4 text-center text-sm text-base-content/70">
                <a href="{{ route('login') }}" class="link link-primary">{{ __('ui.auth.forgot_password.back_to_login') }}</a>
            </p>
        </x-mary-card>
    </div>
@endsection
