@section('title', 'Оценка решения')

<div class="mx-auto w-full max-w-6xl space-y-6 py-6">
    <div class="text-sm breadcrumbs">
        <ul>
            <li><a href="{{ route('home') }}">Главная</a></li>
            <li><a href="{{ route('judge.dashboard') }}">Судья</a></li>
            @if ($hackaton)
                <li><a href="{{ route('judge.hackatons.show', $hackaton) }}">{{ $hackaton->title }}</a></li>
            @endif
            <li class="opacity-70">Оценка решения</li>
        </ul>
    </div>

    <livewire:judge.evaluate-submission :submission="$submission" />
</div>

