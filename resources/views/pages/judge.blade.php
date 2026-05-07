@if ($judgeHackatonsCount === 0)
    <div class="rounded-xl border border-base-200 bg-base-100 p-6 text-center shadow-sm" data-test="judge-dashboard-empty">
        <p class="text-base-content/80">У вас пока нет назначенных хакатонов. Когда организатор добавит вас в судьи, события появятся здесь.</p>
        <a href="/hackatons" class="btn btn-primary mt-4">Каталог хакатонов</a>
    </div>
@else
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <x-marycard title="Назначенные хакатоны" class="card card-border bg-base-100 shadow-sm">
            <p class="text-3xl font-semibold tabular-nums">{{ $judgeHackatonsCount }}</p>
            <x-slot:menu>
                <a href="/hackatons" class="btn btn-ghost btn-sm">Каталог</a>
            </x-slot:menu>
        </x-marycard>
    </div>
    @if (count($judgeHackatonsPreview) > 0)
        <x-marycard title="Ближайшие по дате начала" class="card card-border bg-base-100 shadow-sm w-full max-w-2xl">
            <ul class="space-y-2">
                @foreach ($judgeHackatonsPreview as $row)
                    <li class="flex flex-wrap items-center justify-between gap-2 border-b border-base-200 pb-2 last:border-0">
                        <div class="flex min-w-0 flex-1 flex-wrap items-baseline gap-2">
                            <a href="{{ route('hackatons.show', $row['id']) }}" class="link link-primary font-medium">{{ $row['title'] }}</a>
                            @if ($row['start_at'])
                                <span class="text-sm text-base-content/70">{{ $row['start_at'] }}</span>
                            @endif
                        </div>
                        <a href="{{ route('hackatons.show', $row['id']) }}#hackaton-cases" class="btn btn-ghost btn-xs shrink-0">К кейсам</a>
                    </li>
                @endforeach
            </ul>
        </x-marycard>
    @endif
@endif
<div class="flex flex-wrap gap-3">
    <a href="/hackatons" class="btn btn-primary">Все хакатоны</a>
    <a href="/profile" class="btn btn-outline">Профиль</a>
</div>
