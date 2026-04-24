@props([
    'modalId',
    'buttonLabel',
    'buttonClass' => 'btn btn-primary btn-sm',
    'title' => '',
    'description' => null,
])

<div class="inline-block">
    <label for="{{ $modalId }}" class="{{ $buttonClass }}">
        {{ $buttonLabel }}
    </label>

    <input type="checkbox" id="{{ $modalId }}" class="modal-toggle" />

    <div class="modal modal-bottom sm:modal-middle" role="dialog">
        <div class="modal-box max-w-3xl">
            <div class="mb-4">
                <h3 class="text-lg font-semibold">{{ $title }}</h3>
                @if (filled($description))
                    <p class="mt-1 text-sm text-base-content/70">{{ $description }}</p>
                @endif
            </div>

            {{ $slot }}

            <div class="modal-action">
                <label for="{{ $modalId }}" class="btn btn-ghost">Закрыть</label>
            </div>
        </div>

        <label class="modal-backdrop" for="{{ $modalId }}">close</label>
    </div>
</div>
