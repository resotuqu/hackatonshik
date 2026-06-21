<div>
    <section class="card border border-base-300 bg-base-100">
        <div class="card-body gap-4">
            <h2 class="card-title text-base">
                <x-app-icon icon="heroicons:device-phone-mobile" class="h-5 w-5 text-primary" />
                Двухфакторная аутентификация
                @if ($enabled)
                    <span class="badge badge-success badge-sm">Включена</span>
                @endif
            </h2>

            {{-- Disabled state --}}
            @if (! $enabled && ! $confirming)
                <p class="text-sm text-base-content/70">
                    Дополнительная защита аккаунта: при входе потребуется код из приложения-аутентификатора.
                </p>
                <button type="button" wire:click="openEnableModal" class="btn btn-primary btn-sm w-fit">
                    Включить
                </button>
            @endif

            {{-- Confirming state: show QR + code input --}}
            @if ($confirming)
                <p class="text-sm text-base-content/70">
                    Отсканируйте QR-код в приложении (Google Authenticator, 1Password и т.п.), затем введите код для подтверждения.
                </p>
                <div class="w-fit rounded-xl border border-base-300 bg-base-100 p-3">
                    {!! $this->qrCodeSvg() !!}
                </div>

                <div class="max-w-xs">
                    <input
                        type="text"
                        wire:model="code"
                        inputmode="numeric"
                        autocomplete="one-time-code"
                        placeholder="123456"
                        class="input input-bordered w-full tracking-widest @error('code') input-error @enderror"
                    />
                    @error('code')
                        <p class="mt-1 text-xs text-error">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex gap-2">
                    <button type="button" wire:click="confirmTwoFactor" class="btn btn-primary btn-sm w-fit">
                        Подтвердить
                    </button>
                    <button type="button" wire:click="openDisableModal" class="btn btn-ghost btn-sm w-fit">
                        Отменить
                    </button>
                </div>
            @endif

            {{-- Enabled state --}}
            @if ($enabled)
                <p class="text-sm text-base-content/70">
                    Двухфакторная аутентификация активна. Храните резервные коды в надёжном месте — каждый из них можно использовать один раз, если нет доступа к приложению.
                </p>
                <div class="flex flex-wrap gap-2">
                    <button type="button" wire:click="regenerateRecoveryCodes" class="btn btn-ghost btn-sm border border-base-300">
                        Перевыпустить резервные коды
                    </button>
                    <button type="button" wire:click="openDisableModal" class="btn btn-error btn-outline btn-sm">
                        Отключить
                    </button>
                </div>
            @endif

            {{-- Recovery codes (shown right after confirm or regenerate) --}}
            @if ($showingRecoveryCodes && count($this->recoveryCodes) > 0)
                <div class="rounded-xl border border-base-300 bg-base-200/40 p-4">
                    <p class="mb-2 text-sm font-medium">Резервные коды восстановления</p>
                    <div class="grid grid-cols-2 gap-1 font-mono text-sm">
                        @foreach ($this->recoveryCodes as $recoveryCode)
                            <span>{{ $recoveryCode }}</span>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>

    {{-- Enable: password confirmation --}}
    <x-mary-modal wire:model="enableModal" title="Подтвердите пароль" class="backdrop-blur">
        <p class="text-sm text-base-content/70">
            Для включения двухфакторной аутентификации введите текущий пароль.
        </p>
        <div class="mt-4">
            <input
                type="password"
                wire:model="password"
                wire:keydown.enter="enableTwoFactor"
                autocomplete="current-password"
                placeholder="Текущий пароль"
                class="input input-bordered w-full @error('password') input-error @enderror"
            />
            @error('password')
                <p class="mt-1 text-xs text-error">{{ $message }}</p>
            @enderror
        </div>
        <x-slot:actions>
            <button type="button" wire:click="$set('enableModal', false)" class="btn btn-ghost btn-sm">Отмена</button>
            <button type="button" wire:click="enableTwoFactor" class="btn btn-primary btn-sm">Продолжить</button>
        </x-slot:actions>
    </x-mary-modal>

    {{-- Disable: password confirmation --}}
    <x-mary-modal wire:model="disableModal" title="Отключить 2FA" class="backdrop-blur">
        <p class="text-sm text-base-content/70">
            Введите текущий пароль, чтобы отключить двухфакторную аутентификацию.
        </p>
        <div class="mt-4">
            <input
                type="password"
                wire:model="password"
                wire:keydown.enter="disableTwoFactor"
                autocomplete="current-password"
                placeholder="Текущий пароль"
                class="input input-bordered w-full @error('password') input-error @enderror"
            />
            @error('password')
                <p class="mt-1 text-xs text-error">{{ $message }}</p>
            @enderror
        </div>
        <x-slot:actions>
            <button type="button" wire:click="$set('disableModal', false)" class="btn btn-ghost btn-sm">Отмена</button>
            <button type="button" wire:click="disableTwoFactor" class="btn btn-error btn-sm">Отключить</button>
        </x-slot:actions>
    </x-mary-modal>
</div>
