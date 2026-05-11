@section('title', 'Оценивание')

<div class="py-6">
    <div class="mx-auto w-full max-w-6xl space-y-6">
        <div class="text-sm breadcrumbs">
            <ul>
                <li><a href="{{ route('judge.dashboard') }}">Судья</a></li>
                <li class="opacity-70">{{ $hackaton->title }}</li>
            </ul>
        </div>

        <div class="flex items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold">{{ $hackaton->title }}</h1>
                <p class="text-sm text-base-content/70">Выберите кейс для оценивания.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($hackaton->cases as $case)
                <a href="{{ route('judge.cases.submissions', [$hackaton, $case]) }}"
                   class="card bg-base-100 border border-base-200 shadow-sm hover:shadow-md transition-shadow">
                    <div class="card-body">
                        <div class="font-semibold">{{ $case->title }}</div>
                        <div class="card-actions justify-end">
                            <span class="btn btn-sm btn-primary">Открыть</span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</div>

