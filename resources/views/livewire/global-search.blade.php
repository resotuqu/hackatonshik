<div class="relative" x-data="{ open: false }" x-on:click.outside="open = false">
    <label class="input input-bordered input-sm flex items-center gap-2">
        <x-app-icon icon="heroicons:magnifying-glass" class="h-4 w-4 shrink-0 text-base-content/50" />
        <input
            type="search"
            wire:model.live.debounce.350ms="q"
            x-on:focus="open = true"
            x-on:input="open = true"
            placeholder="{{ __('ui.search.placeholder') }}"
            class="grow"
            aria-label="{{ __('ui.search.aria_label') }}"
        />
        <span wire:loading wire:target="q" class="loading loading-spinner loading-xs text-base-content/50"></span>
    </label>

    <div
        x-show="open && $wire.q.trim().length >= 2"
        x-cloak
        x-transition
        class="absolute left-0 right-0 z-50 mt-2 max-h-96 overflow-y-auto rounded-box border border-base-300 bg-base-100 p-2 shadow-lg"
    >
        @if ($this->hasResults)
            @if ($this->results['hackatons']->isNotEmpty())
                <p class="px-2 pb-1 pt-2 text-xs font-medium text-base-content/50">{{ __('ui.search.section_hackatons') }}</p>
                @foreach ($this->results['hackatons'] as $hackaton)
                    <a href="{{ route('hackatons.show', $hackaton) }}" wire:navigate
                        class="flex items-center gap-2 rounded-lg px-2 py-1.5 text-sm hover:bg-base-200">
                        <x-app-icon icon="heroicons:rocket-launch" class="h-4 w-4 shrink-0 text-base-content/50" />
                        <span class="truncate">{{ $hackaton->title }}</span>
                    </a>
                @endforeach
            @endif

            @if ($this->results['teams']->isNotEmpty())
                <p class="px-2 pb-1 pt-2 text-xs font-medium text-base-content/50">{{ __('ui.search.section_teams') }}</p>
                @foreach ($this->results['teams'] as $team)
                    <a href="{{ route('teams.show', $team) }}" wire:navigate
                        class="flex items-center gap-2 rounded-lg px-2 py-1.5 text-sm hover:bg-base-200">
                        <x-app-icon icon="heroicons:user-group" class="h-4 w-4 shrink-0 text-base-content/50" />
                        <span class="truncate">{{ $team->title }}</span>
                    </a>
                @endforeach
            @endif

            @if ($this->results['users']->isNotEmpty())
                <p class="px-2 pb-1 pt-2 text-xs font-medium text-base-content/50">{{ __('ui.search.section_users') }}</p>
                @foreach ($this->results['users'] as $user)
                    <a href="{{ route('profile.public.show', $user->nickname) }}" wire:navigate
                        class="flex min-w-0 items-center gap-2 rounded-lg px-2 py-1.5 text-sm hover:bg-base-200">
                        <x-app-icon icon="heroicons:user-circle" class="h-4 w-4 shrink-0 text-base-content/50" />
                        <span class="min-w-0 truncate">{{ $user->fio ?: '@'.$user->nickname }}</span>
                        <span class="ml-auto shrink-0 truncate text-xs text-base-content/50">{{ '@'.$user->nickname }}</span>
                    </a>
                @endforeach
            @endif
        @else
            <p class="px-2 py-3 text-center text-sm text-base-content/70">{{ __('ui.search.empty') }}</p>
        @endif
    </div>
</div>
