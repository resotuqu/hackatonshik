<div class="mx-auto max-w-6xl space-y-8 px-4 py-8">
    <div class="space-y-2">
        <h1 class="ui-heading-display text-3xl font-bold">Галерея шаблонов хакатонов</h1>
        <p class="text-base-content/70">Готовые пресеты для быстрого старта: выберите шаблон и создайте хакатон в один клик.</p>
    </div>
    <div class="grid grid-cols-1 gap-3 rounded-xl border border-base-300 bg-base-100 p-4 md:grid-cols-3">
        <label class="form-control">
            <span class="label-text text-xs text-base-content/50">Locale</span>
            <input wire:model.live.debounce.250ms="locale" type="text" class="input input-sm input-bordered" placeholder="ru / en" />
        </label>
        <label class="form-control">
            <span class="label-text text-xs text-base-content/50">Level</span>
            <input wire:model.live.debounce.250ms="level" type="text" class="input input-sm input-bordered" placeholder="junior / middle / senior" />
        </label>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
        @forelse($templates as $template)
            <article wire:key="template-card-{{ $template->id }}" class="card border border-base-300 bg-base-100">
                <div class="card-body gap-4">
                    <div class="flex items-start justify-between gap-3">
                        <h2 class="text-lg font-semibold">{{ $template->title }}</h2>
                        @if($template->level)
                            <span class="badge badge-outline">{{ $template->level }}</span>
                        @endif
                    </div>
                    <p class="text-sm text-base-content/70 line-clamp-3">{{ $template->description }}</p>
                    <div class="card-actions justify-end">
                        <a href="{{ route('templates.show', $template->slug) }}" class="btn btn-ghost btn-sm" wire:navigate>Подробнее</a>
                        <a href="{{ route('hackatons.create', ['template' => $template->slug]) }}" class="ui-cta-primary btn-sm" wire:navigate>
                            Создать по шаблону
                        </a>
                    </div>
                </div>
            </article>
        @empty
            <p class="col-span-full text-base-content/70">Публичные шаблоны пока не опубликованы.</p>
        @endforelse
    </div>
</div>
