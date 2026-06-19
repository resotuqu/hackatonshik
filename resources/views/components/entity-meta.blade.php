@props([
    'updatedAt' => null,
    'createdAt' => null,
    'actor' => null,
    'actorLabel' => null,
])

@php
    use App\Models\User;
@endphp

<div {{ $attributes->merge(['class' => 'space-y-2 text-sm text-base-content/60']) }}>
    @if ($updatedAt)
        <p class="flex flex-wrap items-center gap-1.5">
            <x-app-icon icon="heroicons:arrow-path" class="h-4 w-4 shrink-0 opacity-70" />
            <span>Обновлено <x-datetime :value="$updatedAt" mode="relative" class="font-medium text-base-content/80" /></span>
        </p>
    @endif

    @if ($createdAt)
        <p class="flex flex-wrap items-center gap-1.5">
            <x-app-icon icon="heroicons:calendar-days" class="h-4 w-4 shrink-0 opacity-70" />
            <span>
                @if ($updatedAt)
                    Создано <x-datetime :value="$createdAt" mode="date" class="font-medium text-base-content/80" />
                @else
                    На платформе с <x-datetime :value="$createdAt" mode="date" class="font-medium text-base-content/80" />
                @endif
            </span>
        </p>
    @endif

    @if ($actor instanceof User)
        <p class="flex flex-wrap items-center gap-1.5">
            <x-app-icon icon="heroicons:user" class="h-4 w-4 shrink-0 opacity-70" />
            <span>
                @if (filled($actorLabel))
                    {{ $actorLabel }}:
                @endif
                <a href="{{ route('profile.public.show', ['user' => $actor->nickname]) }}" class="link link-hover font-medium text-base-content/80">
                    {{ $actor->publicName() }}
                </a>
            </span>
        </p>
    @endif
</div>
