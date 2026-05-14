@props([
    'metrics' => [],
])

@php
    /** @var array<string, mixed> $metrics */
    $pending = (int) ($metrics['applications_pending'] ?? 0);
    $casesPct = (int) ($metrics['cases_published_percent'] ?? 0);
    $judges = (int) ($metrics['judges_assigned'] ?? 0);
    $invites = (int) ($metrics['judges_pending_invites'] ?? 0);
    $nextLabel = $metrics['next_deadline_label'] ?? null;
    $nextAt = $metrics['next_deadline_at'] ?? null;
@endphp

<section class="grid grid-cols-2 gap-3 lg:grid-cols-4" aria-label="Ключевые метрики">
    <div class="ui-stat-tile rounded-2xl border border-base-300/50 bg-base-100/80 p-3 sm:p-4">
        <p class="text-[10px] font-bold uppercase tracking-widest text-base-content/50">Заявки</p>
        <p class="ui-heading-display mt-1 text-2xl font-black tabular-nums">{{ $pending }}</p>
        <p class="text-xs text-base-content/60">На рассмотрении</p>
    </div>
    <div class="ui-stat-tile rounded-2xl border border-base-300/50 bg-base-100/80 p-3 sm:p-4">
        <p class="text-[10px] font-bold uppercase tracking-widest text-base-content/50">Кейсы</p>
        <p class="ui-heading-display mt-1 text-2xl font-black tabular-nums">{{ $casesPct }}%</p>
        <p class="text-xs text-base-content/60">Опубликовано сейчас</p>
    </div>
    <div class="ui-stat-tile rounded-2xl border border-base-300/50 bg-base-100/80 p-3 sm:p-4">
        <p class="text-[10px] font-bold uppercase tracking-widest text-base-content/50">Судьи</p>
        <p class="ui-heading-display mt-1 text-2xl font-black tabular-nums">{{ $judges }}</p>
        <p class="text-xs text-base-content/60">
            @if($invites > 0)
                +{{ $invites }} приглаш.
            @else
                Назначено
            @endif
        </p>
    </div>
    <div class="ui-stat-tile col-span-2 rounded-2xl border border-base-300/50 bg-base-100/80 p-3 sm:col-span-1 sm:p-4">
        <p class="text-[10px] font-bold uppercase tracking-widest text-base-content/50">Ближайший дедлайн</p>
        @if(filled($nextLabel) && $nextAt)
            <p class="mt-1 text-sm font-semibold leading-snug">{{ $nextLabel }}</p>
            <p class="text-xs text-base-content/65 tabular-nums">{{ \Illuminate\Support\Carbon::parse($nextAt)->format('d.m.Y H:i') }}</p>
        @else
            <p class="mt-1 text-sm text-base-content/60">Нет предстоящих дат</p>
        @endif
    </div>
</section>
