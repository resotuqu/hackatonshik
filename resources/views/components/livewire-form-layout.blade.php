<div class="w-full flex justify-center">
    <form wire:submit="save" class="bg-slate-700 w-1/2 rounded-sm  py-4 px-6">
        <h3 class="text-2xl text-center text-white">{{$title}}</h3>
        <div class="flex flex-col">
            @csrf
            {{$slot}}
            <button class="py-2 px-4 bg-blue-400 text-white mt-6 rounded-sm cursor-pointer" type="submit">{{$submitButtonTitle}}</button>
        </div>
    </form>
</div>
