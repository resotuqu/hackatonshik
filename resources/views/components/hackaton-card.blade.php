@props(['hackaton', 'canQuickApply' => false, 'href' => null, 'navigate' => true])

@php
    use App\Support\PublicStorageUrl;
    use Illuminate\Support\Carbon;

    $titleId = 'hackaton-card-title-' . $hackaton->id;

    $imageUrl = PublicStorageUrl::for($hackaton->image_url);

    $startAt = $hackaton->start_at ? Carbon::parse($hackaton->start_at) : null;
    $endAt = $hackaton->end_at ? Carbon::parse($hackaton->end_at) : null;
    $deadlineAt = $hackaton->registration_deadline_at ? Carbon::parse($hackaton->registration_deadline_at) : null;
    $now = now();

    $startTimestamp = $startAt?->timestamp ?? 0;
    $endTimestamp = $endAt?->timestamp ?? 0;
    $nowTimestamp = $now->timestamp;
    $progressPercent =
        $endTimestamp > $startTimestamp
            ? max(
                0,
                min(100, (int) round((($nowTimestamp - $startTimestamp) / ($endTimestamp - $startTimestamp)) * 100)),
            )
            : ($endTimestamp > 0 && $nowTimestamp >= $endTimestamp
                ? 100
                : 0);

    $status = $hackaton->status;
    $isFinished = $status?->isFinishedLike() ?? false;
    $isActive = $status?->isActive() ?? false;

    $stageLabel = match (true) {
        $status === \App\Enums\HackatonStatus::DRAFT => 'Подготовка',
        $status === \App\Enums\HackatonStatus::PUBLISHED => 'Анонс',
        $status === \App\Enums\HackatonStatus::REGISTRATION_OPEN => 'Регистрация',
        $status === \App\Enums\HackatonStatus::REGISTRATION_CLOSED => 'Регистрация закрыта',
        $status === \App\Enums\HackatonStatus::WAITING_START => 'Ожидание старта',
        $status === \App\Enums\HackatonStatus::CASES_ANNOUNCED => 'Кейсы объявлены',
        $status === \App\Enums\HackatonStatus::IN_PROGRESS => 'Идёт сейчас',
        $status === \App\Enums\HackatonStatus::JUDGING => 'Судейство',
        $status === \App\Enums\HackatonStatus::FINISHED => 'Завершён',
        $status === \App\Enums\HackatonStatus::ARCHIVED => 'В архиве',
        default => null,
    };

    // Прогресс-бар — нейтральный для всех статусов; цветом выделяется только срочность дедлайна ниже.
    $progressBarClass = 'progress-neutral';

    $daysToDeadline = null;
    $deadlineUrgency = 'normal';
    if ($isActive && $deadlineAt && $deadlineAt->isFuture()) {
        $daysToDeadline = (int) ceil($now->diffInHours($deadlineAt, true) / 24);
        $deadlineUrgency = match (true) {
            $daysToDeadline <= 1 => 'critical',
            $daysToDeadline <= 3 => 'high',
            $daysToDeadline <= 7 => 'medium',
            default => 'normal',
        };
    }

    $deadlineWord = match (true) {
        $daysToDeadline === null => '',
        $daysToDeadline % 10 === 1 && $daysToDeadline % 100 !== 11 => 'день',
        in_array($daysToDeadline % 10, [2, 3, 4], true) && !in_array($daysToDeadline % 100, [12, 13, 14], true)
            => 'дня',
        default => 'дней',
    };

    $deadlineClasses = match ($deadlineUrgency) {
        'critical' => 'border-error/30 bg-error/10 text-error',
        'high' => 'border-warning/30 bg-warning/10 text-warning',
        'medium' => 'border-info/30 bg-info/10 text-info',
        default => 'border-success/30 bg-success/10 text-success',
    };

    $teamsCount = (int) ($hackaton->teams_count ?? 0);
    $participantsCount = (int) ($hackaton->participants_count ?? 0);
    $prizeFund = $hackaton->prize_fund;
    $prizePlacesCount = (int) ($hackaton->prize_places_count ?? 0);
@endphp

<article @class([
    'ui-surface-card ui-surface-card--hover group/card flex h-full flex-col overflow-hidden',
    'border border-base-300 bg-base-100' => !$isFinished,
    'border border-base-300 bg-base-200/30 opacity-90' => $isFinished,
]) aria-labelledby="{{ $titleId }}">
    <x-hackaton-cover :image-url="$imageUrl" :is-finished="$isFinished" :label="$stageLabel" />

    <div class="flex min-h-0 flex-1 flex-col gap-4 p-4 sm:p-5">

        {{-- Заголовок --}}
        <h3 id="{{ $titleId }}" class="line-clamp-2 font-display text-lg font-bold leading-snug text-base-content sm:text-xl">{{ $hackaton->title }}</h3>

        {{-- 2×2 мета-сетка --}}
        <dl class="grid grid-cols-2 gap-2">
            <div class="rounded-lg border border-base-300 bg-base-200 px-3 py-2">
                <dt class="text-[10px] font-semibold uppercase tracking-wide text-base-content/50">Призовой фонд</dt>
                <dd class="mt-0.5 text-sm font-bold text-base-content">
                    {{ $prizeFund ? number_format((float) $prizeFund, 0, '.', ' ') . ' ₽' : '—' }}
                </dd>
            </div>
            <div class="rounded-lg border border-base-300 bg-base-200 px-3 py-2">
                <dt class="text-[10px] font-semibold uppercase tracking-wide text-base-content/50">Приём заявок</dt>
                <dd class="mt-0.5 text-sm font-medium text-base-content">
                    @if ($deadlineAt)
                        {{ $deadlineAt->format('d.m.Y') }}
                    @elseif ($startAt)
                        до {{ $startAt->format('d.m') }}
                    @else
                        —
                    @endif
                </dd>
            </div>
            <div class="rounded-lg border border-base-300 bg-base-200 px-3 py-2">
                <dt class="text-[10px] font-semibold uppercase tracking-wide text-base-content/50">Старт</dt>
                <dd class="mt-0.5 text-sm font-medium text-base-content">
                    {{ $startAt ? $startAt->format('d.m.Y') : '—' }}
                </dd>
            </div>
            <div class="rounded-lg border border-base-300 bg-base-200 px-3 py-2">
                <dt class="text-[10px] font-semibold uppercase tracking-wide text-base-content/50">Команд / участников</dt>
                <dd class="mt-0.5 text-sm font-medium tabular-nums text-base-content">
                    {{ $teamsCount }} / {{ $participantsCount }}
                </dd>
            </div>
        </dl>

        {{-- Дедлайн (только при срочности, без цветного блока) --}}
        @if ($daysToDeadline !== null && $deadlineUrgency !== 'normal')
            <p @class([
                'flex items-center gap-1.5 text-xs font-medium',
                'text-error' => $deadlineUrgency === 'critical',
                'text-warning' => $deadlineUrgency === 'high',
                'text-info' => $deadlineUrgency === 'medium',
            ])>
                <x-app-icon icon="{{ $deadlineUrgency === 'critical' ? 'heroicons:fire' : 'heroicons:bolt' }}" class="h-3.5 w-3.5 shrink-0" />
                @if ($deadlineUrgency === 'critical')
                    Регистрация закрывается скоро
                @else
                    До дедлайна: {{ $daysToDeadline }} {{ $deadlineWord }}
                @endif
            </p>
        @endif

        {{-- Прогресс + обновлено --}}
        <div class="mt-auto flex flex-col gap-2 pt-1">
            @if ($hackaton->updated_at)
                <p class="text-[11px] text-base-content/50">
                    Обновлено <x-datetime :value="$hackaton->updated_at" mode="relative" />
                </p>
            @endif
            <div class="flex items-center justify-between text-[11px] font-medium text-base-content/50">
                <span>{{ $stageLabel ?? 'Таймлайн' }}</span>
                <span class="tabular-nums">{{ $progressPercent }}%</span>
            </div>
            <progress class="progress {{ $progressBarClass }} h-1 w-full bg-base-300"
                value="{{ $progressPercent }}" max="100" aria-label="Прогресс таймлайна хакатона"></progress>
        </div>

        {{-- Кнопка --}}
        <div class="pt-1">
            @if (filled($href))
                <a href="{{ $href }}" @if ($navigate) wire:navigate @endif
                    class="btn btn-neutral btn-sm w-full sm:btn-md">
                    Подробнее
                </a>
            @else
                <button type="button" class="btn btn-neutral btn-sm w-full sm:btn-md"
                    wire:click="openHackaton({{ $hackaton->id }})" wire:loading.attr="disabled"
                    wire:target="openHackaton({{ $hackaton->id }})">
                    <span wire:loading.remove wire:target="openHackaton({{ $hackaton->id }})">Подробнее</span>
                    <span wire:loading wire:target="openHackaton({{ $hackaton->id }})"
                        class="loading loading-spinner loading-sm"></span>
                </button>
            @endif
        </div>
    </div>
</article>
