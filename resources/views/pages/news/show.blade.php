@section('title', $post->title)
@section('meta_description', $post->excerpt)
@section('canonical_url', route('news.show', $post))

<div class="mx-auto mt-8 w-full max-w-4xl space-y-6 sm:mt-12">
    <div class="text-sm breadcrumbs">
        <ul>
            <li><a href="/">Главная</a></li>
            <li><a href="/news">Новости</a></li>
            <li class="opacity-70">{{ $post->title }}</li>
        </ul>
    </div>

    <article class="card card-border bg-base-100 p-6 shadow-sm sm:p-8">
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