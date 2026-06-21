<div class="mx-auto w-full max-w-5xl">
    <div class="grid grid-cols-1 gap-4 lg:grid-cols-5">
        <section class="flex flex-col justify-between gap-8 rounded-2xl bg-base-content px-6 py-8 text-base-100 lg:col-span-2">
            <div class="space-y-3">
                <p class="text-xs font-semibold uppercase tracking-widest text-base-100/50">Хакатонщик</p>
                <h2 class="text-2xl font-semibold leading-tight">{{ __('ui.auth.login.brand_heading') }}</h2>
                <p class="text-sm text-base-100/70">{{ __('ui.auth.login.brand_subtitle') }}</p>
            </div>
            <div class="grid gap-2 text-sm">
                <div class="rounded-lg border border-base-100/10 bg-base-100/10 px-3 py-2.5 text-base-100/80">
                    {{ __('ui.auth.login.feature_1') }}
                </div>
                <div class="rounded-lg border border-base-100/10 bg-base-100/10 px-3 py-2.5 text-base-100/80">
                    {{ __('ui.auth.login.feature_2') }}
                </div>
                <div class="rounded-lg border border-base-100/10 bg-base-100/10 px-3 py-2.5 text-base-100/80">
                    {{ __('ui.auth.login.feature_3') }}
                </div>
            </div>
        </section>

        <x-maryform wire:submit="save" class="card border border-base-300 bg-base-100 p-4 sm:p-6 lg:col-span-3">
            <x-mary-header title="{{ __('ui.auth.login.form_title') }}" separator />
            <div class="space-y-3">
                <x-mary-input
                    label="{{ __('ui.auth.login.email_label') }}"
                    wire:model="email"
                    placeholder="example@mail.com"
                    hint="{{ __('ui.auth.login.email_hint') }}" />
                <x-marypassword label="{{ __('ui.auth.login.password_label') }}" wire:model="password" />
                <div class="flex items-center justify-between gap-3 pt-1">
                    <x-marytoggle label="{{ __('ui.auth.login.remember_me') }}" wire:model="remember" />
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="link link-primary text-sm">
                            {{ __('ui.auth.login.forgot_password') }}
                        </a>
                    @endif
                </div>
            </div>

            <x-slot:actions class="w-full">
                <x-mary-button class="btn-primary w-full" label="{{ __('ui.auth.login.submit') }}" type="submit" wire:loading.attr="disabled" wire:target="save" spinner="save" />
            </x-slot:actions>

            <p class="text-center text-sm text-base-content/60">
                {{ __('ui.auth.login.no_account') }} <a href="{{ route('register') }}" wire:navigate class="link link-primary font-medium">{{ __('ui.auth.login.register_link') }}</a>
            </p>

            <a href="/auth/yandex/redirect" class="block w-full rounded-xl bg-[#FC3F1D] px-4 py-3 text-white transition hover:brightness-95 focus:outline-none focus:ring-2 focus:ring-[#FC3F1D]/40">
                <span class="inline-flex w-full items-center justify-center gap-3 text-sm font-semibold">
                    <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-white text-sm font-black text-[#FC3F1D]">Я</span>
                    {{ __('ui.auth.login.yandex') }}
                </span>
            </a>
            <a href="/auth/vk/redirect" class="btn btn-outline w-full border-[#2787F5] text-[#2787F5] hover:bg-[#2787F5] hover:text-white">
                {{ __('ui.auth.login.vk') }}
            </a>
        </x-maryform>
    </div>
</div>
