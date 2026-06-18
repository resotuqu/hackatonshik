<?php

use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Attributes\Poll;
use Livewire\Component;

new #[Poll('15s')] class extends Component
{
    public int $userId = 0;

    public function mount(): void
    {
        $this->userId = (int) auth()->id();
    }

    public function markAsRead(string $id): void
    {
        auth()->user()
            ?->notifications()
            ->where('id', $id)
            ->first()
            ?->markAsRead();
    }

    public function markAllAsRead(): void
    {
        auth()->user()?->unreadNotifications()->update(['read_at' => now()]);
    }

    #[On('echo-private:notifications.{userId},NewNotification')]
    public function onNewNotification(): void {}

    public function render(): \Illuminate\View\View
    {
        $user = auth()->user();

        $count = $user?->unreadNotifications()->count() ?? 0;
        $notifications = $user?->notifications()->latest()->limit(5)->get() ?? collect();

        return view('components.⚡notification-bell', compact('count', 'notifications'));
    }
};
?>

<div class="dropdown dropdown-end dropdown-bottom">
    <div tabindex="0" role="button" class="btn btn-ghost btn-circle" aria-label="Уведомления">
        <div class="indicator">
            <x-app-icon icon="heroicons:bell" class="h-5 w-5" />
            @if($count > 0)
                <span class="badge badge-xs badge-error indicator-item">{{ min($count, 9) }}</span>
            @endif
        </div>
    </div>
    <div tabindex="-1" class="card card-compact dropdown-content z-50 mt-3 w-[min(20rem,calc(100vw-2rem))] max-w-[min(20rem,calc(100vw-2rem))] border border-base-200 bg-base-100 shadow-xl">
        <div class="card-body gap-2">
            <div class="flex items-center justify-between">
                <p class="font-medium">Уведомления</p>
                @if($count > 0)
                    <button wire:click="markAllAsRead" class="btn btn-ghost btn-xs">Прочитать все</button>
                @endif
            </div>
            @if($notifications->isEmpty())
                <p class="text-sm text-base-content/70">Пока нет уведомлений.</p>
            @else
                <div class="space-y-2">
                    @foreach($notifications as $notification)
                        @php
                            $data = $notification->data ?? [];
                            $url = $data['url'] ?? route('home');
                            $title = $data['title'] ?? 'Новое уведомление';
                            $message = $data['message'] ?? null;
                        @endphp
                        <div @class([
                            'rounded-lg border border-base-200 p-2',
                            'bg-base-200/40' => $notification->read_at === null,
                            'opacity-80' => $notification->read_at !== null,
                        ])>
                            <a href="{{ $url }}" wire:navigate class="block">
                                <p class="text-sm font-medium">{{ $title }}</p>
                                @if(filled($message))
                                    <p class="text-xs text-base-content/70">{{ Str::limit($message, 80) }}</p>
                                @endif
                                <p class="mt-1 text-[0.65rem] text-base-content/50">
                                    <x-datetime :value="$notification->created_at" mode="relative" />
                                </p>
                            </a>
                            @if($notification->read_at === null)
                                <button
                                    wire:click="markAsRead('{{ $notification->id }}')"
                                    class="btn btn-ghost btn-xs px-1 mt-1"
                                >
                                    Прочитать
                                </button>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
