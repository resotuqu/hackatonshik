@section('title', 'Оценивание')

<div class="py-6">
    <div class="mx-auto w-full max-w-6xl space-y-6">
        <div class="text-sm breadcrumbs">
            <ul>
                <li><a href="{{ route('judge.dashboard') }}">Судья</a></li>
                <li class="opacity-70">{{ $hackaton->title }}</li>
            </ul>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold">{{ $hackaton->title }}</h1>
                <p class="text-sm text-base-content/70">Выберите кейс для оценивания.</p>
            </div>
            <a href="{{ route('judge.hackatons.scores.export', $hackaton) }}" class="btn btn-outline btn-sm">
                Экспорт моих оценок (CSV)
            </a>
        </div>

        @if(($scoringSummary['unratedSubmissions'] ?? 0) > 0)
            <div class="alert alert-warning">
                <span>Осталось {{ $scoringSummary['unratedSubmissions'] }} сдач без финальной оценки.</span>
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="table table-zebra">
                <thead>
                    <tr>
                        <th>Кейс</th>
                        <th>Всего сдач</th>
                        <th>Оценено</th>
                        <th>Осталось</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($scoringSummary['cases'] ?? [] as $caseSummary)
                        <tr>
                            <td class="font-medium">{{ $caseSummary['title'] }}</td>
                            <td>{{ $caseSummary['total'] }}</td>
                            <td>{{ $caseSummary['rated'] }}</td>
                            <td>
                                @if($caseSummary['unrated'] > 0)
                                    <span class="badge badge-warning">{{ $caseSummary['unrated'] }}</span>
                                @else
                                    <span class="badge badge-success">0</span>
                                @endif
                            </td>
                            <td class="text-right">
                                <a href="{{ route('judge.cases.submissions', [$hackaton, $caseSummary['id']]) }}?status=unrated"
                                   class="btn btn-sm btn-primary">
                                    Оценить
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
