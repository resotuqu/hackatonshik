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

    $progressBarClass = match (true) {
        $status === \App\Enums\HackatonStatus::IN_PROGRESS => 'progress-primary',
        $status === \App\Enums\HackatonStatus::JUDGING => 'progress-warning',
        $status === \App\Enums\HackatonStatus::REGISTRATION_OPEN,
        $status === \App\Enums\HackatonStatus::PUBLISHED
            => 'progress-success',
        $status === \App\Enums\HackatonStatus::REGISTRATION_CLOSED,
        $status === \App\Enums\HackatonStatus::WAITING_START,
        $status === \App\Enums\HackatonStatus::CASES_ANNOUNCED
            => 'progress-info',
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
    'border border-base-200 bg-base-200/30 opacity-90 grayscale-[20%]' => $isFinished,
]) aria-labelledby="{{ $titleId }}">
    {{-- Обложка хакатона остается без изменений --}}
    <x-hackaton-cover :image-url="$imageUrl" :is-finished="$isFinished" />

    <div class="flex min-h-0 flex-1 flex-col p-5 sm:p-6 gap-5">
        <h3 id="{{ $titleId }}" class="line-clamp-2 font-display text-xl font-semibold leading-tight text-base-content sm:text-2xl">{{ $hackaton->title }}</h3>

        {{-- Группа 1: Даты и дедлайны --}}
        <div class="flex flex-col gap-3">
            @if ($startAt && $endAt)
                <div class="flex items-center gap-2.5 text-sm font-medium text-base-content/80">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary/10 text-primary">
                        <x-app-icon icon="heroicons:calendar-days" class="h-4 w-4" />
                    </div>
                    <span class="tabular-nums tracking-wide">
                        {{ $startAt->format('d.m.Y') }} <span class="text-base-content/40 mx-1">—</span>
                        {{ $endAt->format('d.m.Y') }}
                    </span>
                </div>
            @endif

            @if ($daysToDeadline !== null)
                <div @class([
                    'flex items-center gap-2.5 rounded-lg border px-4 py-2.5 text-sm font-medium',
                    $deadlineClasses,
                ])>
                    <x-app-icon icon="{{ $deadlineUrgency === 'critical' ? 'heroicons:fire' : 'heroicons:bolt' }}"
                        class="h-4 w-4" />
                    <span>
                        @if ($deadlineUrgency === 'critical')
                            Регистрация закроется скоро!
                        @else
                            До дедлайна: <span class="tabular-nums">{{ $daysToDeadline }} {{ $deadlineWord }}</span>
                        @endif
                    </span>
                </div>
            @endif
        </div>

        {{-- Группа 2: Статистика (объединенная в один легкий блок) --}}
        <div
            class="flex items-center divide-x divide-base-300/50 rounded-lg border border-base-300/50 bg-base-200/40 py-3">
            <div class="flex flex-1 flex-col items-center gap-0.5">
                <span class="text-xs text-base-content/50">Команд</span>
                <span
                    class="text-lg font-semibold tabular-nums text-base-content">{{ $teamsCount }}</span>
            </div>
            <div class="flex flex-1 flex-col items-center gap-0.5">
                <span class="text-xs text-base-content/50">Участников</span>
                <span
                    class="text-lg font-semibold tabular-nums text-base-content">{{ $participantsCount }}</span>
            </div>
        </div>

        {{-- Группа 3: Призовой фонд (смягченные акценты) --}}
        @if ($prizeFund || $prizePlacesCount > 0)
            <div class="flex flex-wrap items-center gap-2">
                @if ($prizeFund)
                    <span
                        class="badge badge-warning badge-outline gap-1.5 border-warning/30 bg-warning/10 px-3 py-3 text-sm font-bold text-warning shadow-sm">
                        <x-app-icon icon="heroicons:trophy" class="h-4 w-4" />
                        Фонд: {{ number_format((float) $prizeFund, 0, '.', ' ') }} ₽
                    </span>
                @endif
                @if ($prizePlacesCount > 0)
                    <span
                        class="badge badge-outline gap-1.5 border-base-300 bg-base-200/50 px-3 py-3 text-xs font-semibold text-base-content/70">
                        <x-app-icon icon="heroicons:star" class="h-3.5 w-3.5" />
                        {{ $prizePlacesCount }} призовых мест
                    </span>
                @endif
            </div>
        @endif

        {{-- Группа 4: Прогресс-бар (опущен вниз) --}}
        @if ($hackaton->updated_at)
            <p class="text-xs text-base-content/45">
                Обновлено <x-datetime :value="$hackaton->updated_at" mode="relative" />
            </p>
        @endif
        <div class="mt-auto flex flex-col gap-2 pt-2">
            <div
                class="flex items-center justify-between text-xs font-medium text-base-content/60">
                <span class="flex items-center gap-1.5">
                    @if ($stageLabel)
                        <span class="h-1.5 w-1.5 rounded-full bg-current opacity-80"></span>
                        {{ $stageLabel }}
                    @else
                        Таймлайн
                    @endif
                </span>
                <span class="tabular-nums">{{ $progressPercent }}%</span>
            </div>
            <progress class="progress {{ $progressBarClass }} h-1.5 w-full bg-base-300/50"
                value="{{ $progressPercent }}" max="100" aria-label="Прогресс таймлайна хакатона"></progress>
        </div>

        {{-- Кнопка --}}
        <div class="pt-2">
            @if (filled($href))
                <a href="{{ $href }}" @if ($navigate) wire:navigate @endif
                    class="btn btn-primary w-full sm:btn-md">
                    Подробнее
                </a>
            @else
                <button type="button" class="btn btn-primary w-full rounded-xl shadow-sm sm:btn-md"
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
