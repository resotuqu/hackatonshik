@props(['mode' => 'login'])

@php
    if (! session()->has('oauth_token_nonce')) {
        session([
            'oauth_token_nonce' => \Illuminate\Support\Str::random(40),
            'oauth_token_nonce_at' => now()->timestamp,
        ]);
    }
    $oauthTokenNonce = (string) session('oauth_token_nonce');
@endphp

<div class="space-y-3">
    <div class="flex items-center gap-3">
        <div class="h-px flex-1 bg-base-300"></div>
        <span class="text-xs text-base-content/50">{{ __('ui.auth.oauth.or') }}</span>
        <div class="h-px flex-1 bg-base-300"></div>
    </div>

    <div class="grid min-w-0 gap-2">
        @if (request()->isSecure())
            {{-- wire:ignore keeps SDK-rendered content across Livewire re-renders --}}
            <div id="yandex-btn-{{ $mode }}" class="oauth-provider-btn w-full min-w-0 max-w-full" wire:ignore></div>
            <div id="vk-btn-{{ $mode }}" class="oauth-provider-btn w-full min-w-0 max-w-full" style="min-height: 44px;" wire:ignore></div>
        @else
            <a href="{{ route('auth.yandex.redirect') }}"
               class="group flex w-full min-w-0 items-center justify-center gap-2 rounded-xl border border-base-300 bg-base-100 px-3 py-2.5 text-xs font-semibold text-base-content transition hover:border-[#FC3F1D]/30 hover:bg-[#FC3F1D]/5 focus:outline-none focus:ring-2 focus:ring-[#FC3F1D]/30 sm:gap-2.5 sm:px-4 sm:text-sm">
                <svg class="shrink-0" width="20" height="20" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <circle cx="12" cy="12" r="12" fill="#FC3F1D"/>
                    <text x="12" y="16" text-anchor="middle" fill="white" font-size="12" font-weight="bold" font-family="Arial, sans-serif">Я</text>
                </svg>
                <span class="min-w-0 truncate">{{ $mode === 'login' ? __('ui.auth.login.yandex') : __('ui.auth.register.yandex') }}</span>
            </a>

            <a href="{{ route('auth.vk.redirect') }}"
               class="group flex w-full min-w-0 items-center justify-center gap-2 rounded-xl border border-base-300 bg-base-100 px-3 py-2.5 text-xs font-semibold text-base-content transition hover:border-[#2787F5]/30 hover:bg-[#2787F5]/5 focus:outline-none focus:ring-2 focus:ring-[#2787F5]/30 sm:gap-2.5 sm:px-4 sm:text-sm">
                <svg class="shrink-0" width="20" height="20" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="#2787F5" fill-rule="evenodd" aria-hidden="true">
                    <path d="M15.684 0H8.316C1.592 0 0 1.592 0 8.316v7.368C0 22.408 1.592 24 8.316 24h7.368C22.408 24 24 22.408 24 15.684V8.316C24 1.592 22.408 0 15.684 0zm3.692 17.123h-1.744c-.66 0-.862-.523-2.049-1.714-1.033-1-1.49-1.135-1.745-1.135-.356 0-.458.102-.458.593v1.575c0 .424-.135.678-1.253.678-1.846 0-3.896-1.118-5.335-3.202C5.014 11.465 4.4 9.76 4.4 9.376c0-.254.102-.491.593-.491h1.744c.44 0 .61.203.78.677.863 2.49 2.303 4.675 2.896 4.675.22 0 .322-.102.322-.66V11.16c-.068-1.186-.695-1.287-.695-1.71 0-.203.17-.407.44-.407h2.744c.373 0 .508.203.508.643v3.473c0 .372.17.508.271.508.22 0 .407-.136.813-.542 1.253-1.406 2.151-3.574 2.151-3.574.119-.254.322-.491.763-.491h1.744c.525 0 .644.27.525.643-.22 1.017-2.354 4.031-2.354 4.031-.186.305-.254.44 0 .78.186.254.796.779 1.202 1.253.745.847 1.32 1.558 1.473 2.049.17.474-.085.712-.576.712z"/>
                </svg>
                <span class="min-w-0 truncate">{{ $mode === 'login' ? __('ui.auth.login.vk') : __('ui.auth.register.vk') }}</span>
            </a>
        @endif
    </div>
</div>

@if (request()->isSecure())
    {{-- Forms teleported to <body> to avoid nesting inside the page's <form> element --}}
    <template x-teleport="body">
        <form id="ya-form-{{ $mode }}" action="{{ route('auth.yandex.token') }}" method="POST" style="display:none">
            @csrf
            <input type="hidden" name="oauth_token_nonce" value="{{ $oauthTokenNonce }}">
            <input type="hidden" name="access_token" id="ya-token-{{ $mode }}">
        </form>
    </template>
    <template x-teleport="body">
        <form id="vk-form-{{ $mode }}" action="{{ route('auth.vk.token') }}" method="POST" style="display:none">
            @csrf
            <input type="hidden" name="oauth_token_nonce" value="{{ $oauthTokenNonce }}">
            <input type="hidden" name="access_token" id="vk-token-{{ $mode }}">
        </form>
    </template>

    <script src="{{ asset('js/vkid-sdk.js') }}"></script>
    <script src="{{ asset('js/yandex-sdk.js') }}"></script>
    <script>
    (function () {
        var mode = @json($mode);

        // Guard against double-init on Livewire re-renders (scripts may re-execute).
        // wire:ignore on the containers preserves SDK content, so skip init if already rendered.
        var yaContainer = document.getElementById('yandex-btn-' + mode);
        var vkContainer = document.getElementById('vk-btn-' + mode);

        if (yaContainer && !yaContainer.hasChildNodes()) {
            window.YaAuthSuggest.init(
                {
                    client_id: '{{ config('services.yandex.client_id') }}',
                    response_type: 'token',
                    redirect_uri: '{{ \App\Support\OAuthRedirectUris::yandexTokenPage() }}'
                },
                '{{ rtrim(url('/'), '/') }}',
                {
                    view: 'button',
                    parentId: 'yandex-btn-' + mode,
                    buttonView: 'main',
                    buttonTheme: 'dark',
                    buttonSize: 'm',
                    buttonBorderRadius: 12,
                }
            )
            .then(function (result) { return result.handler(); })
            .then(function (data) {
                document.getElementById('ya-token-' + mode).value = data.access_token;
                document.getElementById('ya-form-' + mode).submit();
            })
            .catch(function (error) { console.error('Яндекс ID ошибка:', error); });
        }

        if (vkContainer && !vkContainer.hasChildNodes()) {
            var VKID = window.VKIDSDK;

            VKID.Config.init({
                app: parseInt('{{ config('services.vkontakte.client_id') }}'),
                redirectUrl: '{{ \App\Support\OAuthRedirectUris::vkCallback() }}',
                mode: VKID.ConfigAuthMode.InNewTab,
                responseMode: VKID.ConfigResponseMode.Callback,
            });

            var oneTap = new VKID.OneTap();
            oneTap.render({ container: vkContainer });

            oneTap.on(VKID.OneTapInternalEvents.LOGIN_SUCCESS, function (data) {
                var auth = new VKID.Auth();
                auth.exchangeCode(data.code, data.device_id)
                    .then(function (result) {
                        document.getElementById('vk-token-' + mode).value = result.access_token;
                        document.getElementById('vk-form-' + mode).submit();
                    })
                    .catch(function (err) { console.error('VK ID ошибка:', err); });
            });
        }
    }());
    </script>
@endif
