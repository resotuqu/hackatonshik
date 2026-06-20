@props([
    'fieldPrefix',
    'heading',
    'description' => null,
    'badge' => null,
    'highlight' => false,
    'lockedLabel' => null,
    'removeAction' => null,
    'removeLabel' => 'Удалить',
    'popularRoleTitles' => [],
    'rolesData' => [],
    'skillsData' => [],
    'config' => [],
    'quickFillMethod' => null,
    'quickFillIndex' => null,
])

@php
    $titleModel = $fieldPrefix.'.title';
    $descriptionModel = $fieldPrefix.'.description';
    $roleModel = $fieldPrefix.'.role';
    $skillsModel = $fieldPrefix.'.skills';

    $hasValidationError = $errors->has($titleModel)
        || $errors->has($descriptionModel)
        || $errors->has($roleModel)
        || $errors->has($skillsModel)
        || $errors->has($skillsModel.'.*');
@endphp

<x-mary-card
    {{ $attributes->class([
        'border bg-base-200/50 shadow-sm transition motion-safe:duration-200 motion-safe:hover:border-primary/20 motion-safe:hover:shadow-md',
        'border-base-200' => ! $highlight && ! $hasValidationError,
        'border-primary/35 bg-primary/5 shadow-primary/10' => $highlight && ! $hasValidationError,
        'border-error/50 ring-1 ring-error/20' => $hasValidationError,
    ]) }}
>
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div class="min-w-0 space-y-2">
            <div class="flex flex-wrap items-center gap-2">
                @if (filled($badge))
                    <x-marybadge class="badge-primary badge-sm font-medium" :value="$badge" />
                @endif

                <h3 class="text-base font-semibold tracking-tight text-base-content">
                    {{ $heading }}
                </h3>

                @if (filled($lockedLabel))
                    <span class="badge badge-sm gap-1 border-0 bg-warning/15 text-warning ring-1 ring-warning/25">
                        <x-app-icon icon="heroicons:lock-closed" class="h-3.5 w-3.5" />
                        {{ $lockedLabel }}
                    </span>
                @endif
            </div>

            @if (filled($description))
                <p class="text-sm leading-relaxed text-base-content/65">
                    {{ $description }}
                </p>
            @endif
        </div>

        @if (filled($removeAction))
            <x-mary-button
                type="button"
                wire:click="{{ $removeAction }}"
                :label="$removeLabel"
                class="btn-ghost btn-xs gap-1 text-base-content/50 hover:bg-error/10 hover:text-error motion-safe:transition-colors"
                icon="o-x-mark"
            />
        @endif
    </div>

    @if ($hasValidationError)
        <div class="mt-4 rounded-xl border border-error/25 bg-error/10 px-4 py-3 text-sm text-error">
            Проверьте поля этой роли: обязательные данные заполнены не полностью.
        </div>
    @endif

    <div class="mt-4 space-y-4 border-t border-base-200/70 pt-4">
        @if (filled($quickFillMethod) && filled($popularRoleTitles))
            <div>
                <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-base-content/50">
                    Быстрый выбор названия
                </p>

                <div class="flex flex-wrap gap-2">
                    @foreach ($popularRoleTitles as $roleTitle)
                        <button
                            type="button"
                            @if ($quickFillIndex === null)
                                wire:click='{{ $quickFillMethod }}({{ Illuminate\Support\Js::from($roleTitle) }})'
                            @else
                                wire:click='{{ $quickFillMethod }}({{ $quickFillIndex }}, {{ Illuminate\Support\Js::from($roleTitle) }})'
                            @endif
                            class="btn btn-ghost btn-xs rounded-full border border-transparent px-3 font-normal text-base-content/80 motion-safe:transition-all motion-safe:hover:border-primary/25 motion-safe:hover:bg-primary/5 motion-safe:active:scale-[0.98]"
                        >
                            {{ $roleTitle }}
                        </button>
                    @endforeach
                </div>
            </div>
        @endif

        <x-mary-input wire:model="{{ $titleModel }}" label="Название роли" />

        <x-marymarkdown
            disk="public"
            folder="team_markdown"
            wire:model="{{ $descriptionModel }}"
            label="Описание роли"
            :config="$config"
        />

        <x-maryselect
            label="Категория роли"
            wire:model="{{ $roleModel }}"
            :options="$rolesData"
        />

        <div
            class="rounded-xl border border-base-200 bg-base-100 p-2 focus-within:ring-2 focus-within:ring-primary/25 motion-safe:transition-shadow"
        >
            <x-marychoices-offline
                label="Навыки роли"
                wire:model="{{ $skillsModel }}"
                :options="$skillsData"
                placeholder="Начните вводить название навыка…"
                hint="Поиск по списку навыков платформы. Можно выбрать несколько."
                clearable
                searchable
            />
        </div>
    </div>
</x-mary-card>
