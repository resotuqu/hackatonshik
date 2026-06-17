@props(['hackaton', 'isWatched' => false])

@auth
    <div class="flex flex-wrap gap-2">
        @if ($isWatched)
            <form method="POST" action="{{ route('hackaton.watches.destroy', $hackaton) }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline gap-2">
                    <x-app-icon icon="heroicons:bookmark-slash" class="h-4 w-4" />
                    Не следить
                </button>
            </form>
        @else
            <form method="POST" action="{{ route('hackaton.watches.store', $hackaton) }}">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline gap-2">
                    <x-app-icon icon="heroicons:bookmark" class="h-4 w-4" />
                    Следить
                </button>
            </form>
        @endif

        @can('viewPublicResults', $hackaton)
            <a href="{{ route('hackatons.results', $hackaton) }}" class="btn btn-sm btn-primary gap-2" wire:navigate>
                <x-app-icon icon="heroicons:trophy" class="h-4 w-4" />
                Итоги
            </a>
        @endcan
    </div>
@endauth
