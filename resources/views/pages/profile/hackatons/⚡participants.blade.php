<?php

use App\Models\Hackaton;
use App\Models\TeamRole;
use App\Models\UserHackatonDocument;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::app', ['title' => 'Участники хакатона'])]
class extends Component {
    public Hackaton $hackaton;
    public array $expandedRows = [];

    public array $headers = [
        ['key' => 'id', 'label' => 'ID', 'hidden' => true],
        ['key' => 'fio', 'label' => 'ФИО'],
        ['key' => 'nickname', 'label' => 'Никнейм'],
        ['key' => 'email', 'label' => 'Email'],
        ['key' => 'phone', 'label' => 'Телефон'],
        ['key' => 'date_of_birth', 'label' => 'Дата рождения'],
        ['key' => 'team_title', 'label' => 'Команда'],
        ['key' => 'role_category', 'label' => 'Категория роли'],
        ['key' => 'required_files_progress', 'label' => 'Обязательные файлы'],
    ];

    public function participantDocuments(int $userId)
    {
        $requiredDocumentIds = $this->hackaton->documents()
            ->where('filling_by_team_member', true)
            ->pluck('id');

        if ($requiredDocumentIds->isEmpty()) {
            return Collection::make();
        }

        return UserHackatonDocument::query()
            ->with('hackatonDocument')
            ->where('user_id', $userId)
            ->whereIn('hackaton_document_id', $requiredDocumentIds)
            ->get();
    }

    public function downloadParticipantFile(int $documentId)
    {
        if (!Auth::check() || Auth::id() !== $this->hackaton->user_id) {
            return;
        }

        $requiredDocumentIds = $this->hackaton->documents()
            ->where('filling_by_team_member', true)
            ->pluck('id');

        $document = UserHackatonDocument::query()
            ->where('id', $documentId)
            ->whereIn('hackaton_document_id', $requiredDocumentIds)
            ->first();

        if (!$document) {
            return;
        }

        return Storage::disk('public')->download($document->file_url);
    }

    public function mount(Hackaton $hackaton): void
    {
        if (!Auth::check() || Auth::id() !== $hackaton->user_id) {
            $this->redirect('/profile/hackatons');
            return;
        }

        $this->hackaton = $hackaton;

        // $this->expandedRows = TeamRole::query()
        //     ->whereNotNull('user_id')
        //     ->whereHas('team', function ($query) use ($hackaton) {
        //         $query->where('hackaton_id', $hackaton->id);
        //     })
        //     ->pluck('id')
        //     ->all();
    }

    #[Computed]
    public function rows()
    {
        $requiredDocumentIds = $this->hackaton->documents()
            ->where('filling_by_team_member', true)
            ->pluck('id');

        $requiredDocumentsCount = $requiredDocumentIds->count();

        return TeamRole::query()
            ->with(['user', 'team', 'role'])
            ->whereNotNull('user_id')
            ->whereHas('team', function ($query) {
                $query->where('hackaton_id', $this->hackaton->id);
            })
            ->paginate(20)
            ->through(function (TeamRole $teamRole) use ($requiredDocumentIds, $requiredDocumentsCount) {
                $user = $teamRole->user;

                $uploadedRequiredCount = 0;
                if ($requiredDocumentsCount > 0) {
                    $uploadedRequiredCount = UserHackatonDocument::query()
                        ->where('user_id', $teamRole->user_id)
                        ->whereIn('hackaton_document_id', $requiredDocumentIds)
                        ->count();
                }

                $requiredPercent = $requiredDocumentsCount > 0
                    ? (int) floor(($uploadedRequiredCount / $requiredDocumentsCount) * 100)
                    : 100;

                return [
                    'id' => $teamRole->id,
                    'user_id' => $teamRole->user_id,
                    'fio' => $user?->fio,
                    'nickname' => $user?->nickname,
                    'email' => $user?->email,
                    'phone' => $user?->phone,
                    'date_of_birth' => $user?->date_of_birth,
                    'team_title' => $teamRole->team?->title,
                    'role_category' => $teamRole->role?->name,
                    'required_files_progress' => $uploadedRequiredCount . '/' . $requiredDocumentsCount . ' (' . $requiredPercent . '%)',
                ];
            });
    }
};
?>

<div>
    <x-mary-card class="card card-border bg-base-100">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
            <div>
                <h3 class="text-2xl font-bold">Участники хакатона</h3>
                <p class="opacity-80">{{ $hackaton->title }}</p>
            </div>
            <a href="/profile/hackatons">
                <x-mary-button label="Назад" class="btn-secondary" />
            </a>
        </div>

        <div class="overflow-x-auto">
        <x-marytable wire:model="expandedRows" :headers="$headers" :rows="$this->rows" striped with-pagination per-page="20" expandable>
            @scope('expansion', $row)
            @php
                $documents = $this->participantDocuments($row['user_id']);
            @endphp

            <x-mary-card class="card card-border bg-base-200">
                <p class="font-semibold mb-2">Файлы участника</p>

                @if($documents->isEmpty())
                    <p class="opacity-70">Файлы не загружены.</p>
                @else
                    <div class="space-y-2">
                        @foreach($documents as $document)
                            <div class="flex items-center justify-between gap-2">
                                <p>{{ $document->hackatonDocument?->name ?? 'Документ' }}</p>
                                <x-mary-button
                                    class="btn-primary btn-sm"
                                    label="Скачать"
                                    wire:click="downloadParticipantFile({{ $document->id }})"
                                />
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-mary-card>
            @endscope
        </x-marytable>
        </div>
    </x-mary-card>
</div>
