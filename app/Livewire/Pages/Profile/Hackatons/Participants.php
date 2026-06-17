<?php

namespace App\Livewire\Pages\Profile\Hackatons;

use App\Models\Hackaton;
use App\Models\HackatonDocument;
use App\Models\Team;
use App\Models\TeamRole;
use App\Models\UserHackatonDocument;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Participants extends Component
{
    public Hackaton $hackaton;

    public array $expandedTeams = [];

    public array $teamHeaders = [
        ['key' => 'id', 'label' => 'ID', 'hidden' => true],
        ['key' => 'title', 'label' => 'Команда'],
        ['key' => 'participants_count', 'label' => 'Участников'],
        ['key' => 'files_progress', 'label' => 'Файлы'],
    ];

    public function mount(Hackaton $hackaton): void
    {
        if (! Auth::check() || Auth::id() !== $hackaton->user_id) {
            $this->redirect(route('organizer.dashboard'));

            return;
        }

        $this->hackaton = $hackaton;
    }

    public function downloadParticipantFile(int $documentId): mixed
    {
        if (! Auth::check() || Auth::id() !== $this->hackaton->user_id) {
            return null;
        }

        $requiredDocumentIds = $this->hackaton->documents()
            ->where('filling_by_team_member', true)
            ->pluck('id');

        $document = UserHackatonDocument::query()
            ->where('id', $documentId)
            ->whereIn('hackaton_document_id', $requiredDocumentIds)
            ->first();

        if (! $document) {
            return null;
        }

        return Storage::disk('public')->download($document->file_url);
    }

    /**
     * @return array{id: int, name: string, file_url: string|null, uploaded: bool, user_doc_id: int|null}[]
     */
    public function getParticipantDocuments(int $userId): array
    {
        $requiredDocuments = $this->hackaton->documents()
            ->where('filling_by_team_member', true)
            ->get();

        $uploaded = UserHackatonDocument::query()
            ->where('user_id', $userId)
            ->whereIn('hackaton_document_id', $requiredDocuments->pluck('id'))
            ->get()
            ->keyBy('hackaton_document_id');

        return $requiredDocuments->map(function (HackatonDocument $doc) use ($uploaded) {
            $userDoc = $uploaded->get($doc->id);

            return [
                'id' => $doc->id,
                'name' => $doc->name,
                'file_url' => $userDoc?->file_url,
                'uploaded' => $userDoc !== null,
                'user_doc_id' => $userDoc?->id,
            ];
        })->all();
    }

    /**
     * @return array{id: int, user_id: int, fio: string|null, nickname: string|null, email: string|null, phone: string|null, date_of_birth: string|null, role: string|null}[]
     */
    public function getTeamParticipants(int $teamId): array
    {
        $team = Team::query()->with(['roles.user', 'roles.role'])->find($teamId);

        if (! $team instanceof Team) {
            return [];
        }

        return $team->roles
            ->filter(fn (TeamRole $role): bool => $role->user_id !== null)
            ->map(function (TeamRole $role): array {
                $user = $role->user;
                $roleModel = $role->role;

                return [
                    'id' => $role->id,
                    'user_id' => $role->user_id,
                    'fio' => $user?->fio,
                    'nickname' => $user?->nickname,
                    'email' => $user?->email,
                    'phone' => $user?->phone,
                    'date_of_birth' => $user?->date_of_birth,
                    'role' => $roleModel?->name,
                ];
            })
            ->values()
            ->all();
    }

    #[Computed]
    public function teamRows(): array
    {
        $requiredDocumentIds = $this->hackaton->documents()
            ->where('filling_by_team_member', true)
            ->pluck('id');

        $requiredCount = $requiredDocumentIds->count();

        $teams = $this->hackaton->teams()
            ->with(['roles' => fn ($q) => $q->whereNotNull('user_id')])
            ->get();

        $allParticipantIds = $teams
            ->flatMap(fn (Team $team) => $team->roles->pluck('user_id'))
            ->filter()
            ->unique()
            ->values();

        $uploadedCountsByUser = $allParticipantIds->isEmpty() || $requiredDocumentIds->isEmpty()
            ? collect()
            : UserHackatonDocument::query()
                ->whereIn('user_id', $allParticipantIds)
                ->whereIn('hackaton_document_id', $requiredDocumentIds)
                ->selectRaw('user_id, COUNT(*) as uploaded_count')
                ->groupBy('user_id')
                ->pluck('uploaded_count', 'user_id');

        return $teams
            ->map(function (Team $team) use ($requiredCount, $uploadedCountsByUser) {
                $participantIds = $team->roles->pluck('user_id')->filter();
                $participantsCount = $participantIds->count();

                $totalUploaded = $participantIds->sum(
                    fn (int $userId): int => (int) ($uploadedCountsByUser[$userId] ?? 0)
                );
                $totalRequired = $participantsCount * $requiredCount;

                return [
                    'id' => $team->id,
                    'title' => $team->title,
                    'participants_count' => $participantsCount,
                    'files_progress' => $totalRequired > 0
                        ? $totalUploaded.'/'.$totalRequired
                        : '—',
                ];
            })
            ->all();
    }

    #[Layout('layouts::app', ['title' => 'Участники хакатона'])]
    public function render()
    {
        return view('pages.profile.hackatons.participants');
    }
}
