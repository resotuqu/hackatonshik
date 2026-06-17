<div class="mx-auto w-full max-w-7xl space-y-6">
    <div class="text-sm breadcrumbs">
        <ul>
            <li><a href="/">Главная</a></li>
            <li><a href="{{ route('hackatons.show', $hackaton) }}">{{ $hackaton->title }}</a></li>
            <li class="opacity-70">Итоги</li>
        </ul>
    </div>

    <header class="space-y-2">
        <h1 class="ui-heading-display text-3xl font-bold sm:text-4xl">Итоги хакатона</h1>
        <p class="text-lg text-base-content/80">{{ $hackaton->title }}</p>
        <x-marybadge class="{{ $hackaton->status->badgeClass() }}" value="{{ $hackaton->status->label() }}" />
    </header>

    <div class="card border border-base-300 bg-base-100">
        <div class="card-body">
            <h2 class="card-title text-xl">Рейтинг команд</h2>

            @if ($leaderboard === [])
                <x-empty-state
                    embedded
                    title="Итоги пока не опубликованы"
                    description="Организатор ещё не завершил оценку или не опубликовал результаты."
                    icon="heroicons:trophy"
                />
            @else
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Место</th>
                                <th>Команда</th>
                                <th>Баллы</th>
                                <th>Выполнение</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($leaderboard as $row)
                                <tr>
                                    <td class="font-semibold tabular-nums">{{ $row['place'] }}</td>
                                    <td>
                                        @if ($row['team'])
                                            <a href="{{ route('teams.show', $row['team']) }}" class="link link-primary font-medium" wire:navigate>
                                                {{ $row['team']->title }}
                                            </a>
                                        @else
                                            <span class="text-base-content/60">—</span>
                                        @endif
                                    </td>
                                    <td class="tabular-nums">{{ $row['total_score'] }} / {{ $row['max_score'] }}</td>
                                    <td class="tabular-nums">{{ $row['completion_percent'] }}%</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <div class="flex flex-wrap gap-3">
        <a href="{{ route('hackatons.show', $hackaton) }}" class="btn btn-outline" wire:navigate>К странице хакатона</a>
    </div>
</div>
