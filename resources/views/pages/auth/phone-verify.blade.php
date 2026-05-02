@extends('layouts.app')

@section('title', 'Подтверждение телефона')

@section('slot')
    <div class="mx-auto w-full max-w-xl space-y-4" x-data="{ code: '', cooldown: 0, isSubmitting: false }" x-init="setInterval(() => { if (cooldown > 0) cooldown--; }, 1000)">
        <x-mary-card title="Подтверждение телефона по SMS" class="card card-border bg-base-100 shadow-sm">
            <p class="text-sm text-base-content/70">
                Мы отправим код на номер <span class="font-medium">{{ auth()->user()?->phone }}</span>. После ввода 6 цифр подтверждение выполнится автоматически.
            </p>

            <div class="mt-4 space-y-3">
                <div class="steps steps-horizontal w-full">
                    <div class="step step-primary">Отправка кода</div>
                    <div class="step" :class="code.length > 0 ? 'step-primary' : ''">Ввод кода</div>
                    <div class="step" :class="isSubmitting ? 'step-primary' : ''">Подтверждение</div>
                </div>

                <form method="POST" action="{{ route('phone.verify.send') }}" @submit="cooldown = 60">
                    @csrf
                    <button type="submit" class="btn btn-outline w-full" :disabled="cooldown > 0">
                        <span x-show="cooldown === 0">Отправить код</span>
                        <span x-show="cooldown > 0">Повторная отправка через <span x-text="cooldown"></span> сек.</span>
                    </button>
                </form>

                <form method="POST" action="{{ route('phone.verify') }}" class="space-y-2" x-ref="verifyForm">
                    @csrf
                    <label class="form-control w-full">
                        <span class="label-text">Код подтверждения</span>
                        <input
                            type="text"
                            name="code"
                            maxlength="6"
                            inputmode="numeric"
                            x-model="code"
                            @input="if (code.length === 6) { isSubmitting = true; $refs.verifyForm.submit(); }"
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
