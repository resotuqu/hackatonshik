@props(['recommendations' => []])

@if ($recommendations !== [])
    <section class="space-y-4">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <h2 class="ui-heading-display text-2xl font-bold sm:text-3xl">Рекомендованные команды</h2>
                <p class="mt-1 text-sm text-base-content/70">Подбор по навыкам из вашего профиля</p>
            </div>
            <a href="/teams" class="btn btn-ghost btn-sm gap-2" wire:navigate>
                Все команды
                <x-app-icon icon="heroicons:arrow-right" class="h-4 w-4" />
            </a>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($recommendations as $item)
                @php
                    $team = $item['team'];
                @endphp
                <article class="card border border-base-300 bg-base-100">
                    <div class="card-body gap-3">
                        <div class="flex items-start justify-between gap-2">
                            <h3 class="font-semibold leading-tight">
                                <a href="{{ route('teams.show', $team) }}" class="link link-hover" wire:navigate>{{ $team->title }}</a>
                            </h3>
                            <span class="badge badge-outline border-base-300 tabular-nums text-base-content/80">{{ $item['match_score'] }} навыков</span>
                        </div>
                        <p class="text-sm text-base-content/70">{{ $team->hackaton?->title }}</p>
                        @if (! empty($item['matched_skills']))
                            <div class="flex flex-wrap gap-1">
                                @foreach ($item['matched_skills'] as $skillName)
                                    <span class="badge badge-ghost badge-sm">{{ $skillName }}</span>
                                @endforeach
                            </div>
                        @endif
                        <a href="{{ route('teams.show', $team) }}" class="btn btn-neutral btn-sm mt-1" wire:navigate>Подробнее</a>
                    </div>
                </article>
            @endforeach
        </div>
    </section>
@endif
