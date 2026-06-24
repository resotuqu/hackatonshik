@props(['hint' => '', 'tooltip' => ''])

@if ($tooltip)
    <div class="tooltip tooltip-left cursor-help" data-tip="{{ $tooltip }}">
        <x-mary-icon name="o-question-mark-circle" class="h-4 w-4 text-base-content/50 hover:text-base-content/70" />
    </div>
@elseif ($hint)
    <p class="text-xs text-base-content/60 leading-relaxed">{{ $hint }}</p>
@endif
