@extends('layouts.app')

@section('title', 'Восстановление пароля')

@section('slot')
    <div class="mx-auto w-full max-w-xl space-y-4">
        <x-mary-card title="Забыли пароль?" class="card card-border bg-base-100">
            <p class="text-sm text-base-content/70">
                Укажите email аккаунта — мы отправим ссылку для сброса пароля.
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
                        <span class="label-text">Адрес электронной почты</span>
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
                <button type="submit" class="btn btn-primary w-full">Отправить ссылку</button>
            </form>

            <p class="mt-4 text-center text-sm text-base-content/70">
                <a href="{{ route('login') }}" class="link link-primary">Вернуться ко входу</a>
            </p>
        </x-mary-card>
    </div>
@endsection
