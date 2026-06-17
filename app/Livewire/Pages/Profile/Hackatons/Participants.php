<?php

namespace App\Livewire\Pages\Profile\Hackatons;

use App\Models\Hackaton;
use App\Models\HackatonDocument;
use App\Models\Team;
use App\Models\TeamRole;
use App\Models\UserHackatonDocument;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Participants extends Component
{
    use AuthorizesRequests;

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
        $this->authorize('update', $hackaton);

        $this->hackaton = $hackaton;
    }

    public function downloadParticipantFile(int $documentId): mixed
    {
        $this->authorize('update', $this->hackaton);

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
     * @return array<int, list<array{id: int, user_id: int, fio: string|null, nickname: string|null, email: string|null, phone: string|null, date_of_birth: string|null, role: string|null, documents: list<array{id: int, name: string, file_url: string|null, uploaded: bool, user_doc_id: int|null>}>}>>
     */
    #[Computed]
    public function expansionDataByTeamId(): array
    {
        $requiredDocuments = $this->hackaton->documents()
            ->where('filling_by_team_member', true)
            ->get();

        $requiredDocumentIds = $requiredDocuments->pluck('id');

        $teams = $this->hackaton->teams()
            ->with(['roles.user', 'roles.role'])
            ->get();

        $allParticipantIds = $teams
            ->flatMap(fn (Team $team) => $team->roles->pluck('user_id'))
            ->filter()
            ->unique()
            ->values();

        $uploadedByUser = $allParticipantIds->isEmpty() || $requiredDocumentIds->isEmpty()
            ? collect()
            : UserHackatonDocument::query()
                ->whereIn('user_id', $allParticipantIds)
                ->whereIn('hackaton_document_id', $requiredDocumentIds)
                ->get()
                ->groupBy('user_id');

        $expansionData = [];

        foreach ($teams as $team) {
            $expansionData[$team->id] = $team->roles
                ->filter(fn (TeamRole $role): bool => $role->user_id !== null)
                ->map(function (TeamRole $role) use ($requiredDocuments, $uploadedByUser): array {
                    $user = $role->user;
                    $roleModel = $role->role;
                    $userUploads = $uploadedByUser->get($role->user_id, collect())->keyBy('hackaton_document_id');

                    $documents = $requiredDocuments->map(function (HackatonDocument $doc) use ($userUploads) {
                        $userDoc = $userUploads->get($doc->id);

                        return [
                            'id' => $doc->id,
                            'name' => $doc->name,
                            'file_url' => $userDoc?->file_url,
                            'uploaded' => $userDoc !== null,
                            'user_doc_id' => $userDoc?->id,
                        ];
                    })->values()->all();

                    return [
                        'id' => $role->id,
                        'user_id' => $role->user_id,
                        'fio' => $user?->fio,
                        'nickname' => $user?->nickname,
                        'email' => $user?->email,
                        'phone' => $user?->phone,
                        'date_of_birth' => $user?->date_of_birth,
                        'role' => $roleModel?->name,
                        'documents' => $documents,
                    ];
                })
                ->values()
                ->all();
        }

        return $expansionData;
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
