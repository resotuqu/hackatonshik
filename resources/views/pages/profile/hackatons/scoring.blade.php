<div class="mx-auto w-full max-w-5xl space-y-6">
    <div class="text-sm breadcrumbs">
        <ul>
            <li><a href="/">Главная</a></li>
            <li><a href="{{ route('profile') }}">Профиль</a></li>
            <li><a href="{{ route('profile.hackatons') }}">Мои хакатоны</a></li>
            <li class="opacity-70">Оценка работ</li>
        </ul>
    </div>

    <header class="space-y-2">
        <h1 class="ui-heading-display text-2xl font-black sm:text-3xl">Оценка работ</h1>
        <p class="max-w-2xl text-sm text-base-content/70">
            Сводка по отправкам решений и финальным оценкам. Детальная работа судей — в разделе для судей; на странице хакатона — кейсы и материалы.
        </p>
    </header>

    @if($scoringRows->isEmpty())
        <section class="ui-surface-card">
            <div class="card-body">
                <x-empty-state
                    title="Пока нет хакатонов"
                    description="Создайте хакатон и опубликуйте кейсы — здесь появится статистика по отправкам и оценкам."
                    icon="heroicons:clipboard-document-check"
                    action-href="{{ route('hackatons.create') }}"
                    action-label="Создать хакатон"
                    secondary-action-href="{{ route('profile.hackatons') }}"
                    secondary-action-label="Дашборд"
                />
            </div>
        </section>
    @else
        <div class="overflow-x-auto rounded-2xl border border-base-300 bg-base-100 shadow-sm">
            <table class="table table-zebra min-w-full">
                <thead>
                    <tr class="text-xs uppercase tracking-wide text-base-content/60">
                        <th class="bg-base-200/80">Хакатон</th>
                        <th class="bg-base-200/80 text-end">Отправки</th>
                        <th class="bg-base-200/80 text-end">Финальных оценок</th>
                        <th class="bg-base-200/80 text-end">Без финала</th>
                        <th class="bg-base-200/80"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($scoringRows as $row)
                        @php
                            /** @var \App\Models\Hackaton $h */
                            $h = $row['hackaton'];
                        @endphp
                        <tr wire:key="scoring-row-{{ $h->id }}">
                            <td class="max-w-[14rem]">
                                <div class="font-semibold leading-tight">{{ $h->title }}</div>
                                <span class="badge badge-ghost badge-xs mt-1">{{ $h->status->label() }}</span>
                            </td>
                            <td class="text-end tabular-nums">{{ $row['submissions_count'] }}</td>
                            <td class="text-end tabular-nums">{{ $row['final_scores_count'] }}</td>
                            <td class="text-end">
                                <span class="@if($row['submissions_without_final'] > 0) font-bold text-warning @else text-base-content/60 @endif tabular-nums">
                                    {{ $row['submissions_without_final'] }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="flex flex-wrap justify-end gap-1">
                                    <a href="{{ route('hackatons.show', $h) }}#hackaton-tab-cases" class="btn btn-ghost btn-xs gap-1" wire:navigate>
                                        <x-app-icon icon="heroicons:puzzle-piece" class="h-3.5 w-3.5" />
                                        Кейсы
                                    </a>
                                    @if(auth()->user()?->isJudge())
                                        <a href="{{ route('judge.dashboard') }}" class="btn btn-secondary btn-xs gap-1" wire:navigate>
                                            <x-app-icon icon="heroicons:scale" class="h-3.5 w-3.5" />
                                            Судья
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
