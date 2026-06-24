@section('title', $post->title)
@section('meta_description', $post->excerpt)
@section('canonical_url', route('news.show', $post))

<div class="mx-auto mt-8 w-full max-w-4xl space-y-6 sm:mt-12">
    <x-breadcrumbs :items="[
        ['label' => __('ui.nav.home'), 'href' => '/'],
        ['label' => __('ui.nav.hackatons'), 'href' => route('news.index')],
        ['label' => $post->title],
    ]" />

    <article class="card border border-base-300 bg-base-100 p-6 sm:p-8">
        <div class="space-y-2">
            <span class="badge badge-outline">{{ $post->category }}</span>
            <h1 class="font-display text-3xl font-bold">{{ $post->title }}</h1>
            <p class="text-sm text-base-content/70">{{ $post->published_at?->format('d.m.Y H:i') }}</p>
        </div>

        <figure class="mt-5 overflow-hidden rounded-xl border border-base-300 bg-base-200">
            <img src="{{ $post->cover_image ?: '/logo.svg' }}" alt="Изображение новости {{ $post->title }}" class="h-full w-full object-cover">
        </figure>

        <div class="markdown-body mt-5">
            {!! \App\Support\SafeMarkdown::toHtml($post->body) !!}
        </div>

        @if (! empty($post->tags))
            <div class="mt-5 flex flex-wrap gap-2">
                @foreach ($post->tags as $postTag)
                    <a href="/news?tag={{ urlencode($postTag) }}" class="badge badge-ghost">#{{ $postTag }}</a>
                @endforeach
            </div>
        @endif
    </article>
</div>