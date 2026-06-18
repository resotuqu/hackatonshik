<div
    class="flex flex-col h-[560px] bg-base-100 border border-base-200 rounded-xl overflow-hidden shadow-sm"
    x-data="{
        allowedEmoji: @js(\App\Livewire\TeamChat::ALLOWED_EMOJI),
        pickerFor: null,
        togglePicker(id) { this.pickerFor = this.pickerFor === id ? null : id; },
        closePicker() { this.pickerFor = null; },
    }"
    @click.outside="closePicker()"
>
    {{-- Header --}}
    <div class="p-4 border-b border-base-200 bg-base-200/30 flex items-center justify-between shrink-0">
        <h3 class="font-bold flex items-center gap-2">
            <x-app-icon icon="heroicons:chat-bubble-left-right" class="w-5 h-5 text-primary" />
            Командный чат
        </h3>
        <span class="badge badge-sm badge-outline">{{ $team->title }}</span>
    </div>

    {{-- Messages --}}
    <div
        class="flex-1 overflow-y-auto p-4 space-y-1 flex flex-col"
        id="chat-messages"
        x-data="{
            scrollToBottom() {
                this.$el.scrollTop = this.$el.scrollHeight;
            }
        }"
        x-init="scrollToBottom()"
        x-on:livewire:navigated.window="scrollToBottom()"
    >
        @forelse($messages as $msg)
            @php
                $isMine = $msg->user_id === auth()->id();
                /** @var array<string, \Illuminate\Support\Collection> $reactionGroups */
                $reactionGroups = $msg->reactions->groupBy('emoji')->map(fn ($g) => $g);
            @endphp

            <div
                class="group relative flex flex-col gap-0.5 @if($isMine) items-end @else items-start @endif"
                wire:key="msg-{{ $msg->id }}"
            >
                {{-- Avatar + name --}}
                <div class="flex items-center gap-1.5 @if($isMine) flex-row-reverse @endif">
                    <div class="w-6 h-6 rounded-full bg-base-300 shrink-0 flex items-center justify-center overflow-hidden">
                        @if($msg->user->avatar_path)
                            <img src="{{ asset('storage/' . $msg->user->avatar_path) }}" class="w-full h-full object-cover" />
                        @else
                            <span class="text-[9px] font-bold text-base-content/50">{{ $msg->user->initials() }}</span>
                        @endif
                    </div>
                    <span class="text-[11px] text-base-content/50 font-medium">{{ $msg->user->fio }}</span>
                    <span class="text-[10px] text-base-content/30">{{ $msg->created_at->format('H:i') }}</span>
                </div>

                {{-- Reply context --}}
                @if($msg->parent)
                    <div class="@if($isMine) mr-8 @else ml-8 @endif px-2 py-1 rounded border-l-2 border-primary/40 bg-base-200/60 text-[11px] text-base-content/60 max-w-[260px] truncate">
                        <span class="font-semibold text-primary/70">{{ $msg->parent->user?->fio }}</span>:
                        {{ Str::limit($msg->parent->content, 60) }}
                    </div>
                @endif

                {{-- Bubble + action buttons --}}
                <div class="flex items-end gap-1 @if($isMine) flex-row-reverse @endif">
                    <div class="chat-bubble @if($isMine) chat-bubble-primary @else bg-base-200 text-base-content @endif text-sm max-w-xs lg:max-w-sm break-words">
                        @if($msg->type === 'image')
                            <a href="{{ asset('storage/' . $msg->content) }}" target="_blank" class="block">
                                <img src="{{ asset('storage/' . $msg->content) }}" class="max-w-full rounded-lg shadow-sm hover:opacity-90 transition-opacity" />
                            </a>
                        @elseif($msg->type === 'file')
                            <div class="flex items-center gap-2">
                                <x-app-icon icon="heroicons:document" class="w-5 h-5 shrink-0" />
                                <a href="{{ asset('storage/' . $msg->content) }}" target="_blank" class="link link-hover break-all text-sm">
                                    {{ basename($msg->content) }}
                                </a>
                            </div>
                        @else
                            @php
                                $mentionClass = $isMine ? 'font-semibold text-white/80' : 'font-semibold text-primary/90';
                                $rendered = preg_replace(
                                    '/@([\w\-\.]+)/u',
                                    '<span class="'.$mentionClass.'">@$1</span>',
                                    e($msg->content)
                                );
                            @endphp
                            {!! $rendered !!}
                        @endif
                    </div>

                    {{-- Hover actions --}}
                    <div class="flex items-center gap-0.5 opacity-0 group-hover:opacity-100 transition-opacity">
                        <div class="relative">
                            <button
                                type="button"
                                class="btn btn-ghost btn-xs btn-circle text-base-content/40 hover:text-base-content"
                                @click.stop="togglePicker({{ $msg->id }})"
                                title="Реакция"
                            >
                                <x-app-icon icon="heroicons:face-smile" class="w-4 h-4" />
                            </button>

                            {{-- Emoji picker --}}
                            <div
                                x-show="pickerFor === {{ $msg->id }}"
                                x-cloak
                                @click.stop
                                class="absolute bottom-full @if($isMine) right-0 @else left-0 @endif mb-1 z-50 flex gap-1 rounded-2xl border border-base-300 bg-base-100 px-2 py-1.5 shadow-lg"
                            >
                                <template x-for="emoji in allowedEmoji" :key="emoji">
                                    <button
                                        type="button"
                                        class="text-lg hover:scale-125 transition-transform"
                                        @click="$wire.toggleReaction({{ $msg->id }}, emoji); closePicker()"
                                        x-text="emoji"
                                    ></button>
                                </template>
                            </div>
                        </div>

                        <button
                            type="button"
                            class="btn btn-ghost btn-xs btn-circle text-base-content/40 hover:text-base-content"
                            wire:click="setReply({{ $msg->id }})"
                            title="Ответить"
                        >
                            <x-app-icon icon="heroicons:arrow-uturn-left" class="w-4 h-4" />
                        </button>
                    </div>
                </div>

                {{-- Reaction pills --}}
                @if($reactionGroups->isNotEmpty())
                    <div class="flex flex-wrap gap-1 @if($isMine) justify-end mr-8 @else ml-8 @endif">
                        @foreach($reactionGroups as $emoji => $group)
                            @php
                                $meReacted = $group->contains('user_id', auth()->id());
                                $names = $group->map(fn($r) => $r->user?->fio)->filter()->join(', ');
                            @endphp
                            <button
                                type="button"
                                wire:click="toggleReaction({{ $msg->id }}, '{{ $emoji }}')"
                                title="{{ $names }}"
                                class="inline-flex items-center gap-0.5 rounded-full border px-1.5 py-0.5 text-xs transition-colors
                                    @if($meReacted) border-primary/40 bg-primary/10 text-primary font-semibold @else border-base-300 bg-base-200/60 text-base-content/70 hover:bg-base-200 @endif"
                            >
                                {{ $emoji }}<span class="tabular-nums">{{ $group->count() }}</span>
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>
        @empty
            <div class="flex flex-col items-center justify-center h-full text-base-content/30 space-y-2">
                <x-app-icon icon="heroicons:chat-bubble-oval-left" class="w-12 h-12 opacity-20" />
                <p class="text-sm">Здесь пока нет сообщений. Начните общение!</p>
            </div>
        @endforelse
    </div>

    {{-- Input area --}}
    <div class="border-t border-base-200 bg-base-200/10 shrink-0">
        {{-- Reply preview --}}
        @if($replyToId)
            @php
                $replyMsg = $messages->firstWhere('id', $replyToId);
                $replyPreview = $replyMsg
                    ? Str::limit($replyMsg->content, 80)
                    : '#'.$replyToId;
                $replyAuthor = $replyMsg?->user?->fio ?? '';
            @endphp
            <div class="flex items-center justify-between gap-2 border-b border-base-300/60 bg-primary/5 px-4 py-2 text-xs text-base-content/70">
                <div class="flex items-center gap-2 min-w-0">
                    <x-app-icon icon="heroicons:arrow-uturn-left" class="w-3.5 h-3.5 shrink-0 text-primary" />
                    <span class="font-semibold text-primary">{{ $replyAuthor }}</span>
                    <span class="truncate">{{ $replyPreview }}</span>
                </div>
                <button type="button" wire:click="cancelReply" class="btn btn-ghost btn-xs btn-circle shrink-0">
                    <x-app-icon icon="heroicons:x-mark" class="w-3.5 h-3.5" />
                </button>
            </div>
        @endif

        <div class="p-4">
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
                        class="input input-bordered w-full input-sm focus:outline-primary {{ $fileUploadEnabled ? 'pr-10' : '' }}"
                        autocomplete="off"
                    />
                    @if($fileUploadEnabled)
                        <label class="absolute right-2 top-1/2 -translate-y-1/2 cursor-pointer hover:text-primary transition-colors" title="Прикрепить файл (до {{ $maxFileMb }} МБ)">
                            <input type="file" wire:model="file" class="hidden" />
                            <x-app-icon icon="heroicons:paper-clip" class="w-4 h-4" />
                        </label>
                    @endif
                </div>
                <button type="submit" class="btn btn-primary btn-sm btn-square" wire:loading.attr="disabled">
                    <x-app-icon icon="heroicons:paper-airplane" class="w-4 h-4" />
                </button>
            </form>
            @if($fileUploadEnabled)
                <div wire:loading wire:target="file" class="text-[10px] text-primary mt-1">Загрузка файла...</div>
            @endif
        </div>
    </div>
</div>
