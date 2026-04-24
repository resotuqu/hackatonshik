@props(['name', 'label', 'type' => 'text'])
<div class="form-control mt-4 w-full gap-2">
    <label for="{{ $name }}" class="label py-0">
        <span class="label-text">{{ $label }}</span>
    </label>
    <input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}" value="{{ old($name) }}" class="input input-bordered w-full">
    @error($name)
        <p class="text-sm text-error">{{ $message }}</p>
    @enderror
</div>
