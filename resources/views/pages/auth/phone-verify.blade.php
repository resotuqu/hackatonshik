@extends('layouts.app')

@section('title', 'Подтверждение телефона')

@section('slot')
    <div class="mx-auto w-full max-w-xl space-y-4">
        <x-mary-card title="Подтверждение телефона по SMS" class="card card-border bg-base-100">
            <p class="text-sm text-base-content/70">
                Мы отправим код на номер <span class="font-medium">{{ auth()->user()?->phone }}</span>.
                Код действует 10 минут.
            </p>

            <div class="mt-4 flex flex-col gap-3">
                <form method="POST" action="{{ route('phone.verify.send') }}">
                    @csrf
                    <button type="submit" class="btn btn-outline w-full">Отправить код</button>
                </form>

                <form method="POST" action="{{ route('phone.verify') }}" class="space-y-2">
                    @csrf
                    <label class="form-control w-full">
                        <span class="label-text">Код подтверждения</span>
                        <input
                            type="text"
                            name="code"
                            maxlength="6"
                            inputmode="numeric"
                            class="input input-bordered w-full"
                            placeholder="123456"
                            required
                        >
                    </label>
                    @error('code')
                        <p class="text-sm text-error">{{ $message }}</p>
                    @enderror
                    <button type="submit" class="btn btn-primary w-full">Подтвердить номер</button>
                </form>
            </div>
        </x-mary-card>
    </div>
@endsection
