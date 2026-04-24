@props(['model', 'label', 'type' => 'text', 'name', 'placeholder' => ''])
<div class="form-control w-full gap-2">
    <label for="{{ $name }}" class="label py-0">
        <span class="label-text">{{ $label }}</span>
    </label>
    <input
        id="{{ $name }}"
        wire:model.live="{{ $model }}"
        type="{{ $type }}"
        placeholder="{{ $placeholder }}"
        class="input input-bordered w-full"
    />
    @error($name)
        <p class="text-sm text-error">{{ $message }}</p>
    @enderror
</div>
