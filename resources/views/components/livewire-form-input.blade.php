@props(['model', 'label', 'type', 'name'])
<div class="flex flex-col mt-4 w-full" >
    <label for="{{$name}}" class="text-white">{{$label}}</label>
    <input id="{{$name}}" wire:model="{{$model}}" type="{{$type}}" value="{{old($name), null}}" class="bg-white rounded-sm py-2 mt-2">
    @error($name)
        <p class="mt-2 text-red-500">{{$message}}</p>
    @enderror
</div>
