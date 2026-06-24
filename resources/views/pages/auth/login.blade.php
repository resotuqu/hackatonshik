<div class="mx-auto w-full max-w-5xl">
    <div class="grid grid-cols-1 items-start gap-4 lg:grid-cols-5">
        <x-auth-brand-panel
            :heading="__('ui.auth.login.brand_heading')"
            :subtitle="__('ui.auth.login.brand_subtitle')"
        >
            <x-auth-brand-panel.feature>{{ __('ui.auth.login.feature_1') }}</x-auth-brand-panel.feature>
            <x-auth-brand-panel.feature>{{ __('ui.auth.login.feature_2') }}</x-auth-brand-panel.feature>
            <x-auth-brand-panel.feature>{{ __('ui.auth.login.feature_3') }}</x-auth-brand-panel.feature>
        </x-auth-brand-panel>

        <x-maryform wire:submit="save" class="card border border-base-300 bg-base-100 p-4 sm:p-6 lg:col-span-3">
            <x-mary-header title="{{ __('ui.auth.login.form_title') }}" separator />
            <div class="animate-form-slide-in space-y-3">
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
                <x-mary-button class="btn-primary w-full transition-all duration-200 hover:scale-105 active:scale-95" label="{{ __('ui.auth.login.submit') }}" type="submit" wire:loading.attr="disabled" wire:target="save" spinner="save" />
            </x-slot:actions>

            <p class="text-center text-sm text-base-content/70">
                {{ __('ui.auth.login.no_account') }} <a href="{{ route('register') }}" wire:navigate class="link link-primary font-medium">{{ __('ui.auth.login.register_link') }}</a>
            </p>

            <x-oauth-buttons mode="login" />
        </x-maryform>
    </div>
</div>
