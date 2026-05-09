<div class="flex flex-col h-[500px] bg-base-100 border border-base-200 rounded-xl overflow-hidden shadow-sm">
    <div class="p-4 border-b border-base-200 bg-base-200/30 flex items-center justify-between">
        <h3 class="font-bold flex items-center gap-2">
            <x-app-icon icon="heroicons:chat-bubble-left-right" class="w-5 h-5 text-primary" />
            Командный чат
        </h3>
        <span class="badge badge-sm badge-outline">{{ $team->title }}</span>
    </div>

    <div class="flex-1 overflow-y-auto p-4 space-y-4 flex flex-col" id="chat-messages" x-data="{ 
        scrollToBottom() { 
            this.$el.scrollTop = this.$el.scrollHeight; 
        } 
    }" x-init="scrollToBottom(); $watch('messages', () => scrollToBottom())">
        @forelse($messages as $msg)
            <div class="chat @if($msg->user_id === auth()->id()) chat-end @else chat-start @endif">
                <div class="chat-image avatar">
                    <div class="w-10 rounded-full bg-base-300">
                        @if($msg->user->avatar_path)
                            <img src="{{ asset('storage/' . $msg->user->avatar_path) }}" />
                        @else
                            <div class="flex items-center justify-center h-full text-xs font-bold text-base-content/50">
                                {{ $msg->user->initials() }}
                            </div>
                        @endif
                    </div>
                </div>
                <div class="chat-header opacity-70 text-xs mb-1">
                    {{ $msg->user->fio }}
                    <time class="text-[10px]">{{ $msg->created_at->format('H:i') }}</time>
                </div>
                <div class="chat-bubble @if($msg->user_id === auth()->id()) chat-bubble-primary @else bg-base-200 text-base-content @endif text-sm">
                    @if($msg->type === 'image')
                        <a href="{{ asset('storage/' . $msg->content) }}" target="_blank" class="block">
                            <img src="{{ asset('storage/' . $msg->content) }}" class="max-w-xs rounded-lg shadow-sm hover:opacity-90 transition-opacity" />
                        </a>
                    @elseif($msg->type === 'file')
                        <div class="flex items-center gap-2">
                            <x-app-icon icon="heroicons:document" class="w-5 h-5" />
                            <a href="{{ asset('storage/' . $msg->content) }}" target="_blank" class="link link-hover break-all">
                                {{ basename($msg->content) }}
                            </a>
                        </div>
                    @else
                        {{ $msg->content }}
                    @endif
                </div>
            </div>
        @empty
            <div class="flex flex-col items-center justify-center h-full text-base-content/30 space-y-2">
                <x-app-icon icon="heroicons:chat-bubble-oval-left" class="w-12 h-12 opacity-20" />
                <p class="text-sm">Здесь пока нет сообщений. Начните общение!</p>
            </div>
        @endforelse
    </div>

    <div class="p-4 border-t border-base-200 bg-base-200/10">
        @if($file)
            <div class="mb-2 p-2 bg-base-200 rounded-lg flex items-center justify-between text-xs">
                <div class="flex items-center gap-2">
                    <x-app-icon icon="heroicons:paper-clip" class="w-4 h-4" />
                    <span>{{ $file->getClientOriginalName() }}</span>
                </div>
                <button type="button" wire:click="$set('file', null)" class="btn btn-ghost btn-xs btn-circle">
                    <x-app-icon icon="heroicons:x-mark" class="w-4 h-4" />
                </button>
            </div>
        @endif

        <form wire:submit.prevent="sendMessage" class="flex gap-2">
            <div class="flex-1 relative">
                <input 
                    type="text" 
                    wire:model="message" 
                    placeholder="Ваше сообщение..." 
                    class="input input-bordered w-full input-sm focus:outline-primary pr-10"
                    autocomplete="off"
                />
                <label class="absolute right-2 top-1/2 -translate-y-1/2 cursor-pointer hover:text-primary transition-colors">
                    <input type="file" wire:model="file" class="hidden" />
                    <x-app-icon icon="heroicons:paper-clip" class="w-4 h-4" />
                </label>
            </div>
            <button type="submit" class="btn btn-primary btn-sm btn-square" wire:loading.attr="disabled">
                <x-app-icon icon="heroicons:paper-airplane" class="w-4 h-4" />
            </button>
        </form>
        <div wire:loading wire:target="file" class="text-[10px] text-primary mt-1">Загрузка файла...</div>
    </div>
</div>
