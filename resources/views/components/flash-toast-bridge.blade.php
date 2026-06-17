@php
    use App\Support\FlashToast;

    $flashToasts = FlashToast::fromSession();
@endphp

@if ($flashToasts !== [])
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toasts = @json($flashToasts);

            toasts.forEach(function (toast) {
                if (typeof window.toast === 'function') {
                    window.toast({ toast: toast });
                }
            });
        });
    </script>
@endif
