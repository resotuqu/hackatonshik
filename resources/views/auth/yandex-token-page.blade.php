<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('ui.auth.oauth.yandex_completing') }}</title>
    <script src="{{ asset('js/yandex-sdk-token.js') }}"></script>
</head>
<body>
    <p style="font-family: system-ui, sans-serif; text-align: center; margin-top: 2rem; color: #555;">
        {{ __('ui.auth.oauth.yandex_completing') }}
    </p>

    <form id="yandex-token-form" action="{{ route('auth.yandex.token') }}" method="POST" style="display:none">
        @csrf
        <input type="hidden" name="oauth_token_nonce" value="{{ $oauthTokenNonce }}">
        <input type="hidden" name="access_token" id="yandex-access-token">
    </form>

    <script>
    (function () {
        var origin = @json($suggestOrigin);
        var loginUrl = @json(route('login'));

        function parseHashAccessToken() {
            if (!window.location.hash || window.location.hash.length < 2) {
                return '';
            }

            return new URLSearchParams(window.location.hash.substring(1)).get('access_token') || '';
        }

        function submitTokenDirectly() {
            var token = parseHashAccessToken();

            if (!token) {
                return false;
            }

            document.getElementById('yandex-access-token').value = token;
            document.getElementById('yandex-token-form').submit();

            return true;
        }

        window.onload = function () {
            if (window.opener && typeof window.YaSendSuggestToken === 'function') {
                window.YaSendSuggestToken(origin)
                    .then(function () {
                        window.close();
                    })
                    .catch(function () {
                        if (!submitTokenDirectly()) {
                            window.location.href = loginUrl;
                        }
                    });

                return;
            }

            if (!submitTokenDirectly()) {
                window.location.href = loginUrl;
            }
        };
    }());
    </script>
</body>
</html>
