@extends('layouts.app')

@section('title', __('ui.auth.verify_email.page_title'))

@section('slot')
    <div class="mx-auto w-full max-w-5xl">
        <div class="grid grid-cols-1 items-start gap-4 lg:grid-cols-5">
            <x-auth-brand-panel
                :heading="__('ui.auth.verify_email.brand_heading')"
                :subtitle="__('ui.auth.verify_email.brand_subtitle')"
            >
                <x-auth-brand-panel.feature>{{ __('ui.auth.verify_email.feature_inbox') }}</x-auth-brand-panel.feature>
                <x-auth-brand-panel.feature>{{ __('ui.auth.verify_email.feature_spam') }}</x-auth-brand-panel.feature>
                <x-auth-brand-panel.feature>{{ __('ui.auth.verify_email.feature_link') }}</x-auth-brand-panel.feature>
            </x-auth-brand-panel>

            <div class="card border border-base-300 bg-base-100 p-4 sm:p-6 lg:col-span-3">
                <div class="space-y-5">

                    {{-- Иконка и заголовок --}}
                    <div class="flex items-center gap-3">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-primary/10">
                            <x-app-icon icon="heroicons:envelope" class="h-5 w-5 text-primary" />
                        </div>
                        <div>
                            <h2 class="font-semibold text-base-content">{{ __('ui.auth.verify_email.card_title') }}</h2>
                            <p class="text-sm text-base-content/60">{{ __('ui.auth.verify_email.page_title') }}</p>
                        </div>
                    </div>

                    {{-- Описание с адресом --}}
                    <div class="rounded-xl border border-primary/20 bg-primary/5 px-4 py-3 text-sm text-base-content/80">
                        {!! __('ui.auth.verify_email.description', ['email' => '<span class="font-semibold text-base-content">'.e(auth()->user()?->email).'</span>']) !!}
                    </div>

                    {{-- Успешная отправка --}}
                    @if (session('status') === 'verification-link-sent')
                        <div class="alert alert-success gap-2 text-sm">
                            <x-app-icon icon="heroicons:check-circle" class="h-5 w-5 shrink-0" />
                            {{ __('ui.auth.verify_email.resent') }}
                        </div>
                    @endif

                    {{-- Кнопка повторной отправки --}}
                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <button type="submit" class="btn btn-primary w-full gap-2">
                            <x-app-icon icon="heroicons:paper-airplane" class="h-4 w-4" />
                            {{ __('ui.auth.verify_email.resend') }}
                        </button>
                    </form>

                    {{-- Ссылка назад --}}
                    <p class="text-center text-sm text-base-content/60">
                        <a href="{{ route('login') }}" class="link link-primary">
                            {{ __('ui.auth.verify_email.back_to_login') }}
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
