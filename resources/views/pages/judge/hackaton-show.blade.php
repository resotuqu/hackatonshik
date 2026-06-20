@section('title', 'Оценивание')

<div class="py-6">
    <div class="mx-auto w-full max-w-6xl space-y-6">
        <div class="text-sm breadcrumbs">
            <ul>
                <li><a href="{{ route('judge.dashboard') }}">Судья</a></li>
                <li class="opacity-70">{{ $hackaton->title }}</li>
            </ul>
        </div>

        <section class="ui-page-header">
            <div class="flex flex-col items-start gap-4 pb-5 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h1 class="ui-heading-display text-2xl font-bold">{{ $hackaton->title }}</h1>
                    <p class="mt-1 text-sm text-base-content/70">Выберите кейс для оценивания.</p>
                </div>
                <a href="{{ route('judge.hackatons.scores.export', $hackaton) }}" class="btn btn-outline btn-sm shrink-0">
                    Экспорт моих оценок (CSV)
                </a>
            </div>
        </section>

        @if(($scoringSummary['unratedSubmissions'] ?? 0) > 0)
            <div class="rounded-xl border border-warning/30 bg-warning/10 px-4 py-3 text-sm text-base-content/80">
                Осталось {{ $scoringSummary['unratedSubmissions'] }} сдач без финальной оценки.
            </div>
        @endif

        <div class="overflow-x-auto rounded-xl border border-base-300 bg-base-100">
            <table class="table table-zebra">
                <thead>
                    <tr class="text-xs uppercase tracking-wide text-base-content/60">
                        <th class="bg-base-200/80">Кейс</th>
                        <th class="bg-base-200/80">Всего сдач</th>
                        <th class="bg-base-200/80">Оценено</th>
                        <th class="bg-base-200/80">Осталось</th>
                        <th class="bg-base-200/80"></th>
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
                                   class="btn btn-sm btn-neutral">
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
