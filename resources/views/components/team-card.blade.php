@props([
    'team',
    'canQuickApply' => false,
    'vacantRoleNames' => [],
    'skillTags' => [],
    'participantUsers' => null,
    'href' => null,
    'navigate' => true,
])

@php
    $titleId = 'team-card-title-' . $team->id;
    $openSlots = (int) ($team->empty_roles_count ?? 0);
    $hasVacancies = $openSlots > 0;
    $slotWord = match (true) {
        $openSlots % 10 === 1 && $openSlots % 100 !== 11 => 'роль',
        in_array($openSlots % 10, [2, 3, 4], true) && ! in_array($openSlots % 100, [12, 13, 14], true) => 'роли',
        default => 'ролей',
    };
    $participants = $participantUsers ?? collect();
    $hackatonTitle = $team->relationLoaded('hackaton') && $team->hackaton ? $team->hackaton->title : null;
@endphp

<article
    class="group/card flex h-full flex-col overflow-hidden rounded-3xl border border-base-300 bg-base-100 shadow-sm transition duration-300 ease-out hover:-translate-y-0.5 hover:border-accent/55 hover:shadow-2xl hover:shadow-accent/10"
    aria-labelledby="{{ $titleId }}"
>
    <x-team-cover
        :title="$team->title"
        :cover-url="$team->coverImagePublicUrl()"
        :initials="$team->initialsForCover()"
        :show-recruiting-badge="$hasVacancies"
        :hackaton-title="$hackatonTitle"
    />

    <div class="flex min-h-0 flex-1 flex-col gap-4 p-4 sm:p-5">
        <div class="space-y-2">
            <h3 id="{{ $titleId }}" class="font-display text-xl font-bold leading-snug tracking-tight text-base-content sm:text-2xl">
                {{ $team->title }}
            </h3>
            <p class="line-clamp-2 text-sm leading-relaxed text-base-content/70">
                {{ $team->description ?? '—' }}
            </p>
        </div>

        @if ($hasVacancies)
            <p class="text-sm font-semibold text-secondary">
                {{ $openSlots }} {{ $slotWord }} свободно
            </p>
        @else
            <span class="badge badge-ghost badge-sm w-fit text-base-content/60">Набор закрыт</span>
        @endif

        @if (! empty($vacantRoleNames))
            <div class="rounded-xl border border-primary/20 border-l-4 border-l-primary bg-primary/5 px-3 py-3 sm:px-4">
                <p class="mb-2 text-xs font-bold uppercase tracking-wide text-primary">Нужны роли:</p>
                <div class="flex flex-wrap gap-2">
                    @foreach ($vacantRoleNames as $name)
                        <span class="badge badge-secondary border-0 font-semibold">{{ $name }}</span>
                    @endforeach
                </div>
            </div>
        @endif

        @if (! empty($skillTags))
            <div>
                <p class="mb-2 text-xs font-medium uppercase tracking-wider text-base-content/50">Навыки</p>
                <div class="flex flex-wrap gap-1.5">
                    @foreach ($skillTags as $tag)
                        <span class="badge badge-outline badge-sm border-base-300/80 text-base-content/85">{{ $tag }}</span>
                    @endforeach
                </div>
            </div>
        @endif

        @if ($participants->isNotEmpty())
            <div class="flex items-center gap-2 pt-1">
                <span class="text-xs font-medium uppercase tracking-wide text-base-content/50">Участники</span>
                <div class="flex -space-x-2">
                    @foreach ($participants as $member)
                        @php
                            $avatarUrl = filled($member->avatar_path)
                                ? asset('storage/' . $member->avatar_path)
                                : 'https://ui-avatars.com/api/?name=' . urlencode($member->fio ?: $member->nickname ?: 'U') . '&background=6366f1&color=fff&size=64';
                        @endphp
                        <img
                            src="{{ $avatarUrl }}"
                            alt=""
                            class="relative h-9 w-9 rounded-full border-2 border-base-100 object-cover ring-2 ring-base-200"
                            loading="lazy"
                        />
                    @endforeach
                </div>
            </div>
        @endif

        <div class="mt-auto flex flex-col gap-2 pt-1 sm:flex-row sm:flex-wrap">
            @if ($canQuickApply && $hasVacancies)
                <button
                    type="button"
                    class="btn btn-primary btn-sm order-1 sm:order-0 sm:btn-md sm:flex-1"
                    wire:click="quickApplyTeam({{ $team->id }})"
                    wire:loading.attr="disabled"
                    wire:target="quickApplyTeam({{ $team->id }})"
                >
                    <span wire:loading.remove wire:target="quickApplyTeam({{ $team->id }})">Откликнуться</span>
                    <span wire:loading wire:target="quickApplyTeam({{ $team->id }})" class="loading loading-spinner loading-sm"></span>
                </button>
            @elseif (! auth()->check() && $hasVacancies)
                <a href="{{ route('login') }}" class="btn btn-primary btn-sm order-1 sm:order-0 sm:btn-md sm:flex-1">
                    Откликнуться
                </a>
            @endif
            @if (filled($href))
                <a
                    href="{{ $href }}"
                    @if ($navigate) wire:navigate @endif
                    class="btn btn-outline btn-sm border-base-300 sm:btn-md sm:flex-1"
                >
                    Подробнее
                </a>
            @else
                <button
                    type="button"
                    class="btn btn-outline btn-sm border-base-300 sm:btn-md sm:flex-1"
                    wire:click="openTeam({{ $team->id }})"
                >
                    Подробнее
                </button>
            @endif
        </div>
    </div>
</article>
