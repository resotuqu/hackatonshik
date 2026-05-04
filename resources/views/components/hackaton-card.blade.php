@props([
    'hackaton',
    'canQuickApply' => false,
])

@php
    use Illuminate\Support\Carbon;

    $titleId = 'hackaton-card-title-' . $hackaton->id;

    $imageUrl = filled($hackaton->image_url)
        ? (str_starts_with($hackaton->image_url, 'http') ? $hackaton->image_url : asset('storage/' . $hackaton->image_url))
        : null;

    $startAt = $hackaton->start_at ? Carbon::parse($hackaton->start_at) : null;
    $endAt = $hackaton->end_at ? Carbon::parse($hackaton->end_at) : null;
    $deadlineAt = $hackaton->registration_deadline_at ? Carbon::parse($hackaton->registration_deadline_at) : null;
    $now = now();

    $startTimestamp = $startAt?->timestamp ?? 0;
    $endTimestamp = $endAt?->timestamp ?? 0;
    $nowTimestamp = $now->timestamp;
    $progressPercent = ($endTimestamp > $startTimestamp)
        ? max(0, min(100, (int) round((($nowTimestamp - $startTimestamp) / ($endTimestamp - $startTimestamp)) * 100)))
        : ($endTimestamp > 0 && $nowTimestamp >= $endTimestamp ? 100 : 0);

    $status = $hackaton->status;
    $isFinished = $status?->isFinishedLike() ?? false;
    $isActive = $status?->isActive() ?? false;

    $stageLabel = match (true) {
        $status === \App\Enums\HackatonStatus::DRAFT => 'Подготовка',
        $status === \App\Enums\HackatonStatus::PUBLISHED => 'Анонс',
        $status === \App\Enums\HackatonStatus::REGISTRATION_OPEN => 'Регистрация',
        $status === \App\Enums\HackatonStatus::IN_PROGRESS => 'Идёт сейчас',
        $status === \App\Enums\HackatonStatus::JUDGING => 'Судейство',
        $status === \App\Enums\HackatonStatus::FINISHED => 'Завершён',
        $status === \App\Enums\HackatonStatus::ARCHIVED => 'В архиве',
        default => null,
    };

    $progressBarClass = match (true) {
        $status === \App\Enums\HackatonStatus::IN_PROGRESS => 'progress-primary',
        $status === \App\Enums\HackatonStatus::JUDGING => 'progress-warning',
        $status === \App\Enums\HackatonStatus::REGISTRATION_OPEN, $status === \App\Enums\HackatonStatus::PUBLISHED => 'progress-success',
        $isFinished => 'progress-neutral',
        default => 'progress-info',
    };

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
        in_array($daysToDeadline % 10, [2, 3, 4], true) && ! in_array($daysToDeadline % 100, [12, 13, 14], true) => 'дня',
        default => 'дней',
    };

    $deadlineClasses = match ($deadlineUrgency) {
        'critical' => 'border-error/40 bg-error/10 text-error',
        'high' => 'border-warning/40 bg-warning/10 text-warning',
        'medium' => 'border-info/40 bg-info/10 text-info',
        default => 'border-success/40 bg-success/10 text-success',
    };

    $teamsCount = (int) ($hackaton->teams_count ?? 0);
    $participantsCount = (int) ($hackaton->participants_count ?? 0);
    $prizeFund = $hackaton->prize_fund;
    $prizePlacesCount = (int) ($hackaton->prize_places_count ?? 0);
@endphp

<article
    @class([
        'group/card flex h-full flex-col overflow-hidden rounded-3xl border bg-base-100 shadow-sm transition duration-300 ease-out hover:-translate-y-0.5 hover:shadow-2xl motion-reduce:transition-none motion-reduce:hover:translate-y-0',
        'border-base-300 hover:border-primary/55 hover:shadow-primary/10' => ! $isFinished,
        'border-base-300/70 opacity-85 hover:border-base-content/30 hover:opacity-100 hover:shadow-base-content/5' => $isFinished,
    ])
    aria-labelledby="{{ $titleId }}"
>
    <x-hackaton-cover
        :title="$hackaton->title"
        :image-url="$imageUrl"
        :status="$status"
        :level="$hackaton->level ?? null"
        :is-finished="$isFinished"
    />

    <div class="flex min-h-0 flex-1 flex-col gap-4 p-4 sm:p-5">
        <h3 id="{{ $titleId }}" class="sr-only">{{ $hackaton->title }}</h3>

        @if ($startAt && $endAt)
            <div class="flex items-center gap-2 text-sm text-base-content/80">
                <x-app-icon icon="heroicons:calendar-days" class="h-4 w-4 text-primary" />
                <span class="font-medium tabular-nums">
                    {{ $startAt->format('d.m.Y') }} <span class="text-base-content/50">—</span> {{ $endAt->format('d.m.Y') }}
                </span>
            </div>
        @endif

        @if ($daysToDeadline !== null)
            <div @class([
                'flex items-center gap-2 rounded-xl border px-3 py-2 text-sm font-semibold',
                $deadlineClasses,
            ])>
                <x-app-icon icon="heroicons:bolt" class="h-4 w-4" />
                <span>
                    До дедлайна:
                    <span class="tabular-nums">{{ $daysToDeadline }} {{ $deadlineWord }}</span>
                </span>
            </div>
        @endif

        <div class="grid grid-cols-2 gap-2">
            <div class="rounded-xl border border-base-300 bg-base-200/40 px-3 py-2 text-center">
                <p class="text-[10px] font-semibold uppercase tracking-wider text-base-content/55">Команд</p>
                <p class="font-display text-xl font-black tabular-nums text-base-content">{{ $teamsCount }}</p>
            </div>
            <div class="rounded-xl border border-base-300 bg-base-200/40 px-3 py-2 text-center">
                <p class="text-[10px] font-semibold uppercase tracking-wider text-base-content/55">Участников</p>
                <p class="font-display text-xl font-black tabular-nums text-base-content">{{ $participantsCount }}</p>
            </div>
        </div>

        @if ($prizeFund || $prizePlacesCount > 0)
            <div class="flex flex-wrap items-center gap-2">
                @if ($prizeFund)
                    <span class="badge badge-warning gap-1 border-0 font-semibold">
                        <x-app-icon icon="heroicons:trophy" class="h-3.5 w-3.5" />
                        Фонд: {{ number_format((float) $prizeFund, 0, '.', ' ') }} ₽
                    </span>
                @endif
                @if ($prizePlacesCount > 0)
                    <span class="badge badge-outline gap-1 border-warning/40 text-warning">
                        <x-app-icon icon="heroicons:star" class="h-3.5 w-3.5" />
                        {{ $prizePlacesCount }} призовых мест
                    </span>
                @endif
            </div>
        @endif

        <div class="space-y-1.5 pt-1">
            <div class="flex items-center justify-between text-xs font-medium text-base-content/70">
                <span class="inline-flex items-center gap-1.5">
                    @if ($stageLabel)
                        <span class="inline-block h-1.5 w-1.5 rounded-full bg-current"></span>
                        {{ $stageLabel }}
                    @else
                        Таймлайн
                    @endif
                </span>
                <span class="tabular-nums">{{ $progressPercent }}%</span>
            </div>
            <progress
                class="progress {{ $progressBarClass }} h-2 w-full"
                value="{{ $progressPercent }}"
                max="100"
                aria-label="Прогресс таймлайна хакатона"
            ></progress>
        </div>

        <div class="mt-auto flex flex-col gap-2 pt-1 sm:flex-row sm:flex-wrap">
            <button
                type="button"
                class="btn btn-primary btn-sm sm:btn-md sm:flex-1"
                wire:click="openHackaton({{ $hackaton->id }})"
            >
                Подробнее
            </button>
            @if ($canQuickApply && ! $isFinished)
                <button
                    type="button"
                    class="btn btn-outline btn-sm border-base-300 sm:btn-md sm:flex-1"
                    wire:click="quickApplyHackaton({{ $hackaton->id }})"
                    wire:loading.attr="disabled"
                    wire:target="quickApplyHackaton({{ $hackaton->id }})"
                >
                    <span wire:loading.remove wire:target="quickApplyHackaton({{ $hackaton->id }})">Подать заявку</span>
                    <span wire:loading wire:target="quickApplyHackaton({{ $hackaton->id }})" class="loading loading-spinner loading-sm"></span>
                </button>
            @endif
        </div>
    </div>
</article>
