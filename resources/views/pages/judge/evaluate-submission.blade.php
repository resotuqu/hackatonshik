@section('title', 'Оценка решения')

<div class="mx-auto w-full max-w-6xl space-y-6 py-6">
    <nav class="text-sm breadcrumbs" aria-label="{{ __('ui.breadcrumbs.aria_label') }}">
        <ul>
            <li><a href="{{ route('home') }}">{{ __('ui.nav.home') }}</a></li>
            <li><a href="{{ route('judge.dashboard') }}">{{ __('ui.nav.judge_panel') }}</a></li>
            @if ($hackaton)
                <li><a href="{{ route('judge.hackatons.show', $hackaton) }}">{{ $hackaton->title }}</a></li>
            @endif
            <li class="opacity-70">{{ __('ui.nav.hackatons') }}</li>
        </ul>
    </div>

    <livewire:judge.evaluate-submission :submission="$submission" />
</div>

