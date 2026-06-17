<div class="mx-auto mt-8 w-full max-w-7xl space-y-8 sm:mt-12">
    <header class="ui-page-hero px-6 py-5 sm:px-8">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="font-display text-3xl font-bold">Новости</h1>
                <p class="mt-1 text-base-content/75">Обновления платформы, релизы и события сообщества</p>
            </div>
            <a href="/news/rss" class="btn btn-outline btn-sm">RSS</a>
        </div>
    </header>

    <section class="rounded-2xl border border-base-300 bg-base-100 p-4 shadow-sm sm:p-6">
        <div class="grid grid-cols-1 gap-3 md:grid-cols-2 lg:grid-cols-5">
            <x-mary-input label="Поиск" wire:model.live.debounce.300ms="search" placeholder="Название новости" />
            <x-maryselect
                label="Категория"
                wire:model.live="category"
                :options="collect([['id' => 'all', 'name' => 'Все']])->merge(collect($this->categories)->map(fn ($item) => ['id' => $item, 'name' => $item]))->all()"
            />
            <x-mary-input label="Дата с" type="date" wire:model.live="from" />
            <x-mary-input label="Дата по" type="date" wire:model.live="to" />
            <x-maryselect
                label="Тег"
                wire:model.live="tag"
                :options="collect([['id' => '', 'name' => 'Любой']])->merge(collect($this->tags)->map(fn ($item) => ['id' => $item, 'name' => '#'.$item]))->all()"
            />
        </div>
    </section>

    <section class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
        @forelse($this->posts as $post)
            <article class="card card-border border-base-300 bg-base-100 shadow-sm">
                <figure class="aspect-video overflow-hidden bg-base-200">
                    <img
                        src="{{ $post->cover_image ?: '/logo.svg' }}"
                        alt="Превью новости {{ $post->title }}"
                        class="h-full w-full object-cover"
                        loading="lazy"
                    >
                </figure>
                <div class="card-body">
                    <div class="flex items-center justify-between gap-2">
                        <span class="badge badge-outline">{{ $post->category }}</span>
                        <span class="text-xs text-base-content/60">{{ $post->published_at?->format('d.m.Y') }}</span>
                    </div>
                    <h2 class="card-title text-lg">{{ $post->title }}</h2>
                    <p class="text-sm text-base-content/75">{{ $post->excerpt }}</p>
                    @if (! empty($post->tags))
                        <div class="mt-2 flex flex-wrap gap-1">
                            @foreach ($post->tags as $postTag)
                                <button type="button" class="badge badge-ghost" wire:click="$set('tag', '{{ $postTag }}')">#{{ $postTag }}</button>
                            @endforeach
                        </div>
                    @endif
                    <div class="card-actions mt-3 justify-end">
                        <a href="{{ route('news.show', $post) }}" class="btn btn-primary btn-sm">Читать дальше</a>
                    </div>
                </div>
            </article>
        @empty
            <p class="text-base-content/70">По текущим фильтрам новости не найдены.</p>
        @endforelse
    </section>

    <div>
        {{ $this->posts->links() }}
    </div>
</div>
