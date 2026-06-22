@props([
    'presentation' => [],
])

@php
    /** @var array{steps: list<array{value: string, label: string}>, current_index: int, current_label: string, next_step_label: string|null} $presentation */
    $steps = $presentation['steps'] ?? [];
    $currentIndex = (int) ($presentation['current_index'] ?? 0);
    $nextLabel = $presentation['next_step_label'] ?? null;
@endphp

@if(count($steps) > 0)
    <section class="ui-surface-soft p-4 sm:p-5" aria-label="Статус хакатона">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-widest text-base-content/50">Жизненный цикл</p>
                <p class="mt-0.5 text-sm font-semibold">Текущий этап: {{ $presentation['current_label'] ?? '' }}</p>
                @if(filled($nextLabel))
                    <p class="text-xs text-base-content/70">Далее: {{ $nextLabel }}</p>
                @endif
            </div>
        </div>
        <div class="mt-4 overflow-x-auto pb-1">
            <ol class="flex min-w-max gap-1 sm:gap-2">
                @foreach($steps as $index => $step)
                    @php
                        $isDone = $index < $currentIndex;
                        $isCurrent = $index === $currentIndex;
                    @endphp
                    <li class="flex items-center gap-1 sm:gap-2">
                        @if($index > 0)
                            <span @class([
                                'hidden h-px w-4 shrink-0 sm:block sm:w-6',
                                'bg-success/50' => $isDone,
                                'bg-primary/40' => $isCurrent,
                                'bg-base-300' => ! $isDone && ! $isCurrent,
                            ]) aria-hidden="true"></span>
                        @endif
                        <span
                            @class([
                                'badge badge-sm whitespace-nowrap',
                                'badge-success' => $isDone,
                                'badge-primary' => $isCurrent,
                                'badge-ghost border border-base-300 text-base-content/50' => ! $isDone && ! $isCurrent,
                            ])
                            title="{{ $step['label'] }}"
                        >
                            <span class="max-w-[6.5rem] truncate sm:max-w-none">{{ $step['label'] }}</span>
                        </span>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>
@endif
