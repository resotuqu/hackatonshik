<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Events\MessageSent;
use App\Events\ReactionUpdated;
use App\Models\PlatformSetting;
use App\Models\Team;
use App\Models\TeamMessage;
use App\Models\TeamMessageReaction;
use App\Models\User;
use App\Notifications\TeamChatMention;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

/**
 * @property-read Collection<int, TeamMessage> $messages
 */
class TeamChat extends Component
{
    use WithFileUploads;

    /** @var array<string> */
    public const ALLOWED_EMOJI = ['👍', '❤️', '😂', '🎉', '😮'];

    public const FILE_SIZE_DEFAULT_KB = 10240;

    public const FILE_SIZE_LARGE_KB = 51200;

    public Team $team;

    public string $message = '';

    public ?int $replyToId = null;

    public $file;

    public function mount(Team $team): void
    {
        Gate::authorize('chat', $team);

        $this->team = $team;
    }

    /**
     * @return Collection<int, TeamMessage>
     */
    public function getMessagesProperty(): Collection
    {
        return $this->team->messages()
            ->with([
                'user:id,fio,nickname,avatar_path',
                'reactions.user:id,fio',
                'parent.user:id,fio,nickname',
            ])
            ->whereNull('parent_id')
            ->latest()
            ->limit(50)
            ->get()
            ->reverse()
            ->values();
    }

    public function sendMessage(): void
    {
        Gate::authorize('chat', $this->team);

        $largeFilesEnabled = PlatformSetting::isEnabled('feature.chat_large_files');
        $maxKb = $largeFilesEnabled ? self::FILE_SIZE_LARGE_KB : self::FILE_SIZE_DEFAULT_KB;

        $this->validate([
            'message' => 'required_without:file|string|max:2000',
            'file' => "nullable|file|max:{$maxKb}|mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx,xls,xlsx,txt,zip",
            'replyToId' => 'nullable|integer|exists:team_messages,id',
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
            'parent_id' => $this->replyToId,
        ]);

        $this->message = '';
        $this->file = null;
        $this->replyToId = null;

        broadcast(new MessageSent($teamMessage))->toOthers();

        if ($type === 'text') {
            $this->notifyMentions($teamMessage);
        }
    }

    public function setReply(int $messageId): void
    {
        Gate::authorize('chat', $this->team);

        $this->replyToId = $messageId;
    }

    public function cancelReply(): void
    {
        $this->replyToId = null;
    }

    public function toggleReaction(int $messageId, string $emoji): void
    {
        Gate::authorize('chat', $this->team);

        if (! in_array($emoji, self::ALLOWED_EMOJI, true)) {
            return;
        }

        $message = TeamMessage::query()
            ->where('id', $messageId)
            ->where('team_id', $this->team->id)
            ->firstOrFail();

        $existing = TeamMessageReaction::query()
            ->where('team_message_id', $messageId)
            ->where('user_id', auth()->id())
            ->where('emoji', $emoji)
            ->first();

        if ($existing) {
            $existing->delete();
        } else {
            TeamMessageReaction::query()->create([
                'team_message_id' => $messageId,
                'user_id' => auth()->id(),
                'emoji' => $emoji,
            ]);
        }

        broadcast(new ReactionUpdated($message))->toOthers();
    }

    #[On('echo-private:team.{team.id},MessageSent')]
    public function onMessageSent(): void {}

    #[On('echo-private:team.{team.id},ReactionUpdated')]
    public function onReactionUpdated(): void {}

    /**
     * Parse @nickname tokens and notify mentioned team members.
     * Only notifies users who are actually in the team (owner or role holder).
     * Never notifies the sender themselves.
     */
    private function notifyMentions(TeamMessage $teamMessage): void
    {
        $nicknames = $this->parseMentions($teamMessage->content);

        if ($nicknames === []) {
            return;
        }

        $teamMessage->load('team:id,user_id');

        $mentioned = User::query()
            ->whereIn('nickname', $nicknames)
            ->where('id', '!=', auth()->id())
            ->where(fn ($q) => $q
                ->whereHas('teamRoles', fn ($r) => $r
                    ->where('team_id', $this->team->id)
                    ->whereNotNull('user_id')
                )
                ->orWhere('id', $this->team->user_id)
            )
            ->get();

        foreach ($mentioned as $user) {
            $user->notify(new TeamChatMention($teamMessage));
        }
    }

    /**
     * Extract unique lowercased nicknames from @mention tokens.
     *
     * @return list<string>
     */
    private function parseMentions(string $content): array
    {
        preg_match_all('/@([\w\-\.]+)/u', $content, $matches);

        return array_values(array_unique(
            array_map('mb_strtolower', $matches[1] ?? [])
        ));
    }

    private function isImage(TemporaryUploadedFile $file): bool
    {
        return str_starts_with($file->getMimeType(), 'image/');
    }

    public function render(): View
    {
        $largeFilesEnabled = PlatformSetting::isEnabled('feature.chat_large_files');

        return view('livewire.team-chat', [
            'messages' => $this->messages,
            'fileUploadEnabled' => $largeFilesEnabled,
            'maxFileMb' => $largeFilesEnabled ? 50 : 10,
        ]);
    }
}
