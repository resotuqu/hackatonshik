<?php

namespace App\Livewire\Pages\Profile\Hackatons;

use App\Models\Hackaton;
use App\Models\HackatonDocument;
use App\Models\Role;
use App\Models\Team;
use App\Models\TeamRole;
use App\Models\User;
use App\Models\UserHackatonDocument;
use App\Notifications\DocumentUploadReminder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Mary\Traits\Toast;

class Participants extends Component
{
    use AuthorizesRequests, Toast;

    public Hackaton $hackaton;

    public array $expandedTeams = [];

    public string $documentsFilter = 'all';

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

    public function sendDocumentReminders(): void
    {
        $this->authorize('update', $this->hackaton);

        $userIds = $this->incompleteDocumentUserIds();

        if ($userIds->isEmpty()) {
            $this->warning('Все участники уже загрузили обязательные документы.', position: 'toast-center toast-top');

            return;
        }

        $users = User::query()->whereIn('id', $userIds)->get();
        Notification::send($users, new DocumentUploadReminder($this->hackaton));

        $this->success("Напоминания отправлены: {$users->count()} участникам.", position: 'toast-center toast-top');
    }

    /**
     * @return Collection<int, int>
     */
    private function incompleteDocumentUserIds(): Collection
    {
        $requiredDocumentIds = $this->hackaton->documents()
            ->where('filling_by_team_member', true)
            ->pluck('id');

        if ($requiredDocumentIds->isEmpty()) {
            return collect();
        }

        $teams = $this->hackaton->teams()->with('roles')->get();
        $participantIds = $teams
            ->flatMap(fn (Team $team) => $team->roles->pluck('user_id'))
            ->filter()
            ->unique()
            ->values();

        if ($participantIds->isEmpty()) {
            return collect();
        }

        $uploadedCounts = UserHackatonDocument::query()
            ->whereIn('user_id', $participantIds)
            ->whereIn('hackaton_document_id', $requiredDocumentIds)
            ->selectRaw('user_id, COUNT(*) as uploaded_count')
            ->groupBy('user_id')
            ->pluck('uploaded_count', 'user_id');

        $requiredCount = $requiredDocumentIds->count();

        return $participantIds->filter(
            fn (int $userId): bool => (int) ($uploadedCounts[$userId] ?? 0) < $requiredCount
        )->values();
    }

    private function teamPassesDocumentsFilter(int $teamId, int $totalUploaded, int $totalRequired): bool
    {
        return match ($this->documentsFilter) {
            'complete' => $totalRequired > 0 && $totalUploaded >= $totalRequired,
            'incomplete' => $totalRequired > 0 && $totalUploaded < $totalRequired,
            default => true,
        };
    }

    /**
     * @return array<int, list<array<string, mixed>>>
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
                        'fio' => $user instanceof User ? $user->fio : null,
                        'nickname' => $user instanceof User ? $user->nickname : null,
                        'email' => $user instanceof User ? $user->email : null,
                        'phone' => $user instanceof User ? $user->phone : null,
                        'date_of_birth' => $user instanceof User ? $user->date_of_birth : null,
                        'role' => $roleModel instanceof Role ? $roleModel->name : null,
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
                    'total_uploaded' => $totalUploaded,
                    'total_required' => $totalRequired,
                ];
            })
            ->filter(fn (array $row): bool => $this->teamPassesDocumentsFilter(
                $row['id'],
                $row['total_uploaded'],
                $row['total_required'],
            ))
            ->map(fn (array $row): array => collect($row)->except(['total_uploaded', 'total_required'])->all())
            ->values()
            ->all();
    }

    #[Layout('layouts::app', ['title' => 'Участники хакатона'])]
    public function render()
    {
        return view('pages.profile.hackatons.participants');
    }
}
