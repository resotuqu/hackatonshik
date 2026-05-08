<?php

namespace App\Livewire\Pages\Profile\Hackatons;

use App\Models\Hackaton;
use App\Models\HackatonDocument;
use App\Models\Role;
use App\Models\Team;
use App\Models\TeamRole;
use App\Models\User;
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
            $this->redirect('/profile/hackatons');

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
            ->filter(function ($role): bool {
                return $role instanceof TeamRole && $role->user_id !== null;
            })
            ->map(function (TeamRole $role): array {
                $user = User::query()->find($role->user_id);
                $roleModel = Role::query()->find($role->role_id);

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

        return $this->hackaton->teams()
            ->with(['roles' => fn ($q) => $q->whereNotNull('user_id')])
            ->get()
            ->map(function (Team $team) use ($requiredDocumentIds, $requiredCount) {
                $participantIds = $team->roles->pluck('user_id')->filter();
                $participantsCount = $participantIds->count();

                $totalUploaded = 0;
                $totalRequired = $participantsCount * $requiredCount;

                if ($totalRequired > 0) {
                    $totalUploaded = UserHackatonDocument::query()
                        ->whereIn('user_id', $participantIds)
                        ->whereIn('hackaton_document_id', $requiredDocumentIds)
                        ->count();
                }

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
