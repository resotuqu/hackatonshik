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
        $openSlots % 10 === 1 && $openSlots % 100 !== 11 => 'место',
        in_array($openSlots % 10, [2, 3, 4], true) && ! in_array($openSlots % 100, [12, 13, 14], true) => 'места',
        default => 'мест',
    };

    $participants = $participantUsers ?? collect();

    // Роли: показываем макс 2, остаток — счётчик
    $visibleRoles = array_slice($vacantRoleNames, 0, 2);
    $extraRoles = max(0, count($vacantRoleNames) - 2);
@endphp

<article
    {{ $attributes->class([
        'ui-surface-card ui-surface-card--hover ui-surface-card--team group/card flex h-full min-h-0 flex-col overflow-clip rounded-card',
    ]) }}
    aria-labelledby="{{ $titleId }}"
>
    <x-team-cover
        title=""
        :cover-url="$team->coverImagePublicUrl()"
        :initials="$team->initialsForCover()"
    />

    <div class="flex min-h-0 flex-1 flex-col gap-4 p-5 sm:p-6">

        {{-- Заголовок: строго 1 строка --}}
        <h3 id="{{ $titleId }}" class="truncate text-xl font-bold leading-snug text-base-content sm:text-2xl" title="{{ $team->title }}">
            {{ $team->title }}
        </h3>

        {{-- Описание: ровно 3 строки с резервированием места --}}
        <p class="line-clamp-3 text-sm leading-relaxed text-base-content/70" style="min-height: calc(1.625rem * 3)">
            {{ \App\Support\SafeMarkdown::toPlainExcerpt($team->description ?? '') ?: 'Описание проекта пока не добавлено. Лидер команды скоро это исправит...' }}
        </p>

        {{-- Ищем в команду: макс 2 роли + "+N", резервируем место --}}
        <div class="min-h-[5.5rem] rounded-lg border border-base-300 bg-base-200/40 p-4">
            <p class="mb-2 text-xs font-medium text-base-content/70">
                @if ($hasVacancies || ! empty($vacantRoleNames))
                    Ищем в команду ({{ $openSlots }} {{ $slotWord }}):
                @else
                    <span class="text-base-content/40">Набор закрыт</span>
                @endif
            </p>
            <div class="flex flex-wrap gap-2">
                @forelse ($visibleRoles as $name)
                    <span class="badge badge-outline border-base-300 bg-base-100 px-3 py-3 font-semibold text-base-content/80">
                        {{ $name }}
                    </span>
                @empty
                    @if (! $hasVacancies)
                        {{-- пустое место для выравнивания --}}
                    @else
                        <span class="text-sm font-medium text-base-content/70">Любые роли</span>
                    @endif
                @endforelse
                @if ($extraRoles > 0)
                    <span class="badge badge-outline border-base-300 bg-base-200/50 px-3 py-3 text-xs font-semibold text-base-content/50">
                        +{{ $extraRoles }}
                    </span>
                @endif
            </div>
        </div>

        {{-- Навыки: макс 2 строки бейджей с резервированием места --}}
        <div class="min-h-[4.5rem]">
            @if (! empty($skillTags))
                <p class="mb-2 text-xs font-medium text-base-content/50">Навыки</p>
                <div class="flex flex-wrap gap-2 overflow-hidden" style="max-height: calc(1.75rem * 2 + 0.5rem)">
                    @foreach ($skillTags as $tag)
                        <span class="badge badge-outline border-base-300 bg-base-200/30 px-3 py-3 text-xs font-medium text-base-content/80">
                            {{ $tag }}
                        </span>
                    @endforeach
                </div>
            @endif
        </div>

        @if ($team->updated_at)
            <p class="text-xs text-base-content/50">
                обновлено <x-datetime :value="$team->updated_at" mode="relative" />
            </p>
        @endif

        {{-- Футер: участники и кнопка --}}
        <div class="mt-auto flex flex-col gap-4 pt-2 sm:flex-row sm:items-end sm:justify-between">

            @if ($participants->isNotEmpty())
                <div class="flex flex-col gap-2">
                    <span class="text-xs font-medium text-base-content/50">Участники</span>
                    <div class="flex -space-x-3">
                        @foreach ($participants->take(5) as $member)
                            @php
                                $avatarUrl = filled($member->avatar_path)
                                    ? asset('storage/' . $member->avatar_path)
                                    : 'https://ui-avatars.com/api/?name=' . urlencode($member->publicName()) . '&background=random&color=fff&size=64';
                            @endphp
                            <div class="relative group/avatar transition-transform hover:z-10 hover:scale-110">
                                <img
                                    src="{{ $avatarUrl }}"
                                    alt="{{ $member->publicName() }}"
                                    title="{{ $member->publicName() }}"
                                    class="h-10 w-10 rounded-full border-2 border-base-100 object-cover ring-1 ring-base-300 shadow-sm"
                                    loading="lazy"
                                />
                            </div>
                        @endforeach

                        @if ($participants->count() > 5)
                            <div class="flex h-10 w-10 items-center justify-center rounded-full border-2 border-base-100 bg-base-300 text-xs font-bold text-base-content shadow-sm ring-1 ring-base-300">
                                +{{ $participants->count() - 5 }}
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <div class="mt-2 w-full sm:mt-0 sm:w-auto">
                @if (filled($href))
                    <a
                        href="{{ $href }}"
                        @if ($navigate) wire:navigate @endif
                        class="ui-cta-outline w-full sm:w-auto px-6"
                    >
                        Подробнее
                    </a>
                @else
                    <button
                        type="button"
                        class="ui-cta-outline w-full sm:w-auto px-6"
                        wire:click="openTeam({{ $team->id }})"
                    >
                        Подробнее
                    </button>
                @endif
            </div>
        </div>
    </div>
</article>
