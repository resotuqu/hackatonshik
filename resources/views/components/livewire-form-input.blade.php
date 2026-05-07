@props(['model', 'label', 'type' => 'text', 'name', 'placeholder' => ''])
<div class="form-control w-full gap-2 group">
    <label for="{{ $name }}" class="label py-0 transition-colors group-focus-within:text-primary">
        <span class="label-text font-medium">{{ $label }}</span>
    </label>
    <input
        id="{{ $name }}"
        wire:model.live="{{ $model }}"
        type="{{ $type }}"
        placeholder="{{ $placeholder }}"
        @class([
            'input input-bordered w-full transition-all duration-200 focus:ring-2 focus:ring-primary/20',
            'input-error border-error shadow-sm shadow-error/10' => $errors->has($name),
        ])
    />
    @error($name)
        <div
            x-data="{ show: false }"
            x-init="setTimeout(() => show = true, 50)"
            x-show="show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 -translate-y-1"
            x-transition:enter-end="opacity-100 translate-y-0"
            class="flex items-center gap-1.5 px-1 pt-0.5"
        >
            <x-app-icon icon="heroicons:exclamation-circle" class="h-4 w-4 text-error" />
            <p class="text-xs font-medium text-error leading-tight">{{ $message }}</p>
        </div>
    @enderror
</div>
