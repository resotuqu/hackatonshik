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
    
    // Склонение слова "роль"
    $slotWord = match (true) {
        $openSlots % 10 === 1 && $openSlots % 100 !== 11 => 'место',
        in_array($openSlots % 10, [2, 3, 4], true) && ! in_array($openSlots % 100, [12, 13, 14], true) => 'места',
        default => 'мест',
    };
    
    $participants = $participantUsers ?? collect();
    $hackatonTitle = $team->relationLoaded('hackaton') && $team->hackaton ? $team->hackaton->title : null;
@endphp

<article
    class="ui-surface-card ui-surface-card--hover ui-surface-card--team group/card flex h-full flex-col overflow-hidden"
    aria-labelledby="{{ $titleId }}"
>
    {{-- 1. Убрали дублирование заголовка. Оставляем инициалы и градиент/обложку --}}
    <x-team-cover
        title="" {{-- Передаем пустоту, чтобы не дублировать текст поверх градиента --}}
        :cover-url="$team->coverImagePublicUrl()"
        :initials="$team->initialsForCover()"
        :show-recruiting-badge="$hasVacancies"
        :hackaton-title="$hackatonTitle"
    />

    <div class="flex min-h-0 flex-1 flex-col gap-5 p-5 sm:p-6">
        
        {{-- 3. Заголовок и проект --}}
        <div class="space-y-1">
            <h3 id="{{ $titleId }}" class="ui-heading-display text-xl font-bold leading-snug text-base-content sm:text-2xl">
                {{ $team->title }}
            </h3>
            {{-- Задел на будущее, если решите разделить Название команды и Название проекта в БД --}}
            {{-- <p class="text-sm font-medium text-base-content/60">Проект: <span class="text-base-content/80">{{ $team->project_name }}</span></p> --}}
        </div>

        {{-- 5. Ограничение высоты описания (Трункация) --}}
        <p class="line-clamp-2 text-sm leading-relaxed text-base-content/70">
            {{ \App\Support\SafeMarkdown::toPlainExcerpt($team->description ?? '') ?: 'Описание проекта пока не добавлено. Лидер команды скоро это исправит...' }}
        </p>

        {{-- 4 & 6. Сгруппированный блок ролей со спокойным акцентом --}}
        @if ($hasVacancies || ! empty($vacantRoleNames))
            <div class="rounded-xl border border-secondary/20 bg-secondary/5 p-4">
                <p class="mb-3 text-xs font-bold uppercase tracking-wide text-secondary">
                    Ищем в команду ({{ $openSlots }} {{ $slotWord }}):
                </p>
                <div class="flex flex-wrap gap-2">
                    @forelse ($vacantRoleNames as $name)
                        {{-- Полупрозрачные бейджи вместо "кричащих" сплошных --}}
                        <span class="badge badge-secondary badge-outline border-secondary/30 bg-secondary/10 px-3 py-3 font-semibold">
                            {{ $name }}
                        </span>
                    @empty
                        <span class="text-sm font-medium text-base-content/60">Любые роли</span>
                    @endforelse
                </div>
            </div>
        @else
            <div class="w-fit rounded-lg bg-base-200/50 px-3 py-1.5 border border-base-300">
                <span class="text-xs font-semibold uppercase tracking-wider text-base-content/60">Набор закрыт</span>
            </div>
        @endif

        {{-- Навыки --}}
        @if (! empty($skillTags))
            <div>
                <p class="mb-2 text-xs font-bold uppercase tracking-wider text-base-content/50">Навыки</p>
                <div class="flex flex-wrap gap-2">
                    @foreach ($skillTags as $tag)
                        <span class="badge badge-outline border-base-300 bg-base-200/30 px-3 py-3 text-xs font-medium text-base-content/80">
                            {{ $tag }}
                        </span>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Футер карточки: Участники и кнопка --}}
        <div class="mt-auto flex flex-col gap-4 pt-2 sm:flex-row sm:items-end sm:justify-between">
            
            @if ($participants->isNotEmpty())
                <div class="flex flex-col gap-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-base-content/50">Участники</span>
                    <div class="flex -space-x-3">
                        @foreach ($participants->take(5) as $member)
                            @php
                                $avatarUrl = filled($member->avatar_path)
                                    ? asset('storage/' . $member->avatar_path)
                                    : 'https://ui-avatars.com/api/?name=' . urlencode($member->fio ?: $member->nickname ?: 'U') . '&background=random&color=fff&size=64';
                            @endphp
                            <div class="relative group/avatar transition-transform hover:z-10 hover:scale-110">
                                <img
                                    src="{{ $avatarUrl }}"
                                    alt="{{ $member->fio }}"
                                    title="{{ $member->fio }}"
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
                        class="btn btn-neutral w-full sm:w-auto px-6 rounded-xl shadow-sm"
                    >
                        Подробнее
                    </a>
                @else
                    <button
                        type="button"
                        class="btn btn-neutral w-full sm:w-auto px-6 rounded-xl shadow-sm"
                        wire:click="openTeam({{ $team->id }})"
                    >
                        Подробнее
                    </button>
                @endif
            </div>
        </div>
    </div>
</article>