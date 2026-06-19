@extends('layouts.app')

@section('title', 'Подтверждение e-mail')

@section('slot')
    <div class="mx-auto w-full max-w-xl space-y-4">
        <x-mary-card title="Подтвердите адрес электронной почты" class="card border border-base-300 bg-base-100">
            <p class="text-sm text-base-content/70">
                Ссылка для подтверждения отправлена на <span class="font-medium">{{ auth()->user()?->email }}</span>.
                Перейдите по ссылке в письме, чтобы активировать аккаунт, затем вы сможете подтвердить телефон.
            </p>

            @if (session('status') === 'verification-link-sent')
                <div class="alert alert-success mt-4 text-sm">
                    Новая ссылка отправлена на вашу почту.
                </div>
            @endif

            <form method="POST" action="{{ route('verification.send') }}" class="mt-4">
                @csrf
                <button type="submit" class="btn btn-primary w-full">Выслать письмо ещё раз</button>
            </form>
        </x-mary-card>
    </div>
@endsection
