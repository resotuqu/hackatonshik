@extends('layouts.app')

@section('title', __('ui.auth.verify_email.page_title'))

@section('slot')
    <div class="mx-auto w-full max-w-xl space-y-4">
        <x-mary-card title="{{ __('ui.auth.verify_email.card_title') }}" class="card border border-base-300 bg-base-100">
            <p class="text-sm text-base-content/70">
                {!! __('ui.auth.verify_email.description', ['email' => '<span class="font-medium">'.e(auth()->user()?->email).'</span>']) !!}
            </p>

            @if (session('status') === 'verification-link-sent')
                <div class="alert alert-success mt-4 text-sm">
                    {{ __('ui.auth.verify_email.resent') }}
                </div>
            @endif

            <form method="POST" action="{{ route('verification.send') }}" class="mt-4">
                @csrf
                <button type="submit" class="btn btn-primary w-full">{{ __('ui.auth.verify_email.resend') }}</button>
            </form>
        </x-mary-card>
    </div>
@endsection
