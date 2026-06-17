<?php

namespace App\Livewire;

use App\Events\MessageSent;
use App\Models\Team;
use App\Models\TeamMessage;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * @property-read Collection<int, TeamMessage> $messages
 */
class TeamChat extends Component
{
    use WithFileUploads;

    public Team $team;

    public string $message = '';

    public $file;

    public function mount(Team $team): void
    {
        Gate::authorize('chat', $team);

        $this->team = $team;
    }

    /**
     * Get the messages for the team.
     *
     * @return Collection<int, TeamMessage>
     */
    public function getMessagesProperty(): Collection
    {
        return $this->team->messages()
            ->with('user')
            ->oldest()
            ->get();
    }

    public function sendMessage(): void
    {
        Gate::authorize('chat', $this->team);

        $this->validate([
            'message' => 'required_without:file|string|max:2000',
            'file' => 'nullable|file|max:10240', // 10MB limit
        ]);

        $type = 'text';
        $content = $this->message;

        if ($this->file) {
            $path = $this->file->store('team-chat', 'public');
            $content = $path;
            $type = $this->isImage($this->file) ? 'image' : 'file';
        }

        $teamMessage = $this->team->messages()->create([
            'user_id' => auth()->id(),
            'content' => $content,
            'type' => $type,
        ]);

        $this->message = '';
        $this->file = null;

        broadcast(new MessageSent($teamMessage))->toOthers();
    }

    private function isImage($file): bool
    {
        return str_starts_with($file->getMimeType(), 'image/');
    }

    #[On('echo-private:team.{team.id},MessageSent')]
    public function onMessageSent($event): void
    {
        // This will trigger a re-render
    }

    public function render(): View
    {
        return view('livewire.team-chat', [
            'messages' => $this->messages,
        ]);
    }
}
