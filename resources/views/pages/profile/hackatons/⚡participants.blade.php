<?php

use App\Models\Hackaton;
use App\Models\Team;
use App\Models\UserHackatonDocument;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::app', ['title' => 'Участники хакатона'])]
class extends Component {
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
        if (!Auth::check() || Auth::id() !== $hackaton->user_id) {
            $this->redirect('/profile/hackatons');

            return;
        }

        $this->hackaton = $hackaton;
    }

    public function downloadParticipantFile(int $documentId): mixed
    {
        if (!Auth::check() || Auth::id() !== $this->hackaton->user_id) {
            return null;
        }

        $requiredDocumentIds = $this->hackaton->documents()
            ->where('filling_by_team_member', true)
            ->pluck('id');

        $document = UserHackatonDocument::query()
            ->where('id', $documentId)
            ->whereIn('hackaton_document_id', $requiredDocumentIds)
            ->first();

        if (!$document) {
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

        return $requiredDocuments->map(function ($doc) use ($uploaded) {
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
        $team = Team::with(['roles.user', 'roles.role'])->find($teamId);

        if (!$team) {
            return [];
        }

        return $team->roles
            ->filter(fn ($role) => $role->user_id !== null)
            ->map(fn ($role) => [
                'id' => $role->id,
                'user_id' => $role->user_id,
                'fio' => $role->user?->fio,
                'nickname' => $role->user?->nickname,
                'email' => $role->user?->email,
                'phone' => $role->user?->phone,
                'date_of_birth' => $role->user?->date_of_birth,
                'role' => $role->role?->name,
            ])
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
                        ? $totalUploaded . '/' . $totalRequired
                        : '—',
                ];
            })
            ->all();
    }
};
?>

<div>
    <x-mary-card class="card card-border bg-base-100">
        <div class="text-sm breadcrumbs mb-4">
            <ul>
                <li><a href="/">Главная</a></li>
                <li><a href="/profile">Профиль</a></li>
                <li><a href="/profile/hackatons">Мои хакатоны</a></li>
                <li class="opacity-70">Участники</li>
            </ul>
        </div>

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
            <x-marytable wire:model="expandedTeams" :headers="$teamHeaders" :rows="$this->teamRows" striped expandable>
                @scope('expansion', $team)
                    @php $participants = $this->getTeamParticipants($team['id']); @endphp

                    @if(empty($participants))
                        <p class="opacity-70 p-4">Нет участников в команде.</p>
                    @else
                        <div class="p-2">
                            <x-maryaccordion>
                                @foreach($participants as $participant)
                                    <x-marycollapse name="participant-{{ $participant['id'] }}">
                                        <x-slot:heading>
                                            <span class="font-semibold">{{ $participant['fio'] ?? 'Без имени' }}</span>
                                            <span class="opacity-60 ml-2">{{ $participant['role'] ?? '' }}</span>
                                        </x-slot:heading>
                                        <x-slot:content>
                                            <div class="space-y-4">
                                                {{-- Личные данные --}}
                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-sm">
                                                    <div><span class="font-semibold">ФИО:</span> {{ $participant['fio'] ?? '—' }}</div>
                                                    <div><span class="font-semibold">Никнейм:</span> {{ $participant['nickname'] ?? '—' }}</div>
                                                    <div><span class="font-semibold">Email:</span> {{ $participant['email'] ?? '—' }}</div>
                                                    <div><span class="font-semibold">Телефон:</span> {{ $participant['phone'] ?? '—' }}</div>
                                                    <div><span class="font-semibold">Дата рождения:</span> {{ $participant['date_of_birth'] ?? '—' }}</div>
                                                    <div><span class="font-semibold">Роль:</span> {{ $participant['role'] ?? '—' }}</div>
                                                </div>

                                                {{-- Документы --}}
                                                @php $documents = $this->getParticipantDocuments($participant['user_id']); @endphp

                                                @if(!empty($documents))
                                                    <div>
                                                        <p class="font-semibold mb-2">Документы</p>
                                                        <div class="space-y-2">
                                                            @foreach($documents as $doc)
                                                                <div class="flex items-center justify-between gap-2 p-2 rounded-lg {{ $doc['uploaded'] ? 'bg-success/10' : 'bg-error/10' }}">
                                                                    <div class="flex items-center gap-2">
                                                                        @if($doc['uploaded'])
                                                                            <x-mary-icon name="o-check-circle" class="text-success w-5 h-5" />
                                                                        @else
                                                                            <x-mary-icon name="o-x-circle" class="text-error w-5 h-5" />
                                                                        @endif
                                                                        <span>{{ $doc['name'] }}</span>
                                                                    </div>

                                                                    @if($doc['uploaded'])
                                                                        <a href="/uploads/{{ $doc['file_url'] }}" download>
                                                                            <x-mary-button class="btn-primary btn-sm" label="Скачать" />
                                                                        </a>
                                                                    @endif
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </x-slot:content>
                                    </x-marycollapse>
                                @endforeach
                            </x-maryaccordion>
                        </div>
                    @endif
                @endscope
            </x-mary-table>
        </div>
    </x-mary-card>
</div>
