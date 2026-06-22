<div class="mx-auto max-w-3xl space-y-8 px-4 py-8">
    <div class="text-sm breadcrumbs">
        <ul>
            <li><a href="{{ route('templates.index') }}" wire:navigate>Шаблоны</a></li>
            <li class="opacity-70">{{ $template->title }}</li>
        </ul>
    </div>

    <header class="space-y-3">
        <h1 class="ui-heading-display text-3xl font-bold">{{ $template->title }}</h1>
        @if($template->level)
            <span class="badge badge-neutral badge-outline">{{ $template->level }}</span>
        @endif
        <p class="text-base-content/80">{{ $template->description }}</p>
        <p class="text-xs text-base-content/50">Версия {{ $template->version }} · {{ strtoupper($template->locale) }}</p>
    </header>

    <div class="flex flex-wrap gap-3">
        <a href="{{ route('hackatons.create', ['template' => $template->slug]) }}" class="ui-cta-primary" wire:navigate>
            Создать хакатон по шаблону
        </a>
        <a href="{{ route('templates.index') }}" class="ui-cta-ghost" wire:navigate>Назад к галерее</a>
    </div>
</div>
