<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use League\CommonMark\CommonMarkConverter;
use Livewire\Component;
use Livewire\Attributes\Layout;

new #[Layout('layouts::app', ['title' => 'Команда'])]
class extends Component {
    use \Livewire\WithFileUploads;

    public \App\Models\Team $team;
    public array $documentUploads = [];

    public function mount(\App\Models\Team $team): void
    {
        $this->team = $team;
    }

    public function appendToRole($id)
    {
        if (!Auth::check()) {
            return;
        }

        $role = \App\Models\TeamRole::find($id);
        $role->user_id = Auth::id();
        $role->save();
    }

    public function truncateFromRole($id)
    {
        if (!Auth::check()) {
            return;
        }

        $role = \App\Models\TeamRole::find($id);
        if ($role->user_id == Auth::id()) {
            $role->user_id = null;
            $role->save();

            $hasOtherOccupiedRoles = $this->team->roles()
                ->where('user_id', Auth::id())
                ->exists();

            if (!$hasOtherOccupiedRoles) {
                $requiredHackatonDocumentIds = $this->team->hackaton->documents()
                    ->where('filling_by_team_member', true)
                    ->pluck('id');

                $participantDocuments = \App\Models\UserHackatonDocument::query()
                    ->where('user_id', Auth::id())
                    ->whereIn('hackaton_document_id', $requiredHackatonDocumentIds)
                    ->get();

                foreach ($participantDocuments as $participantDocument) {
                    if (!empty($participantDocument->file_url)) {
                        Storage::disk('public')->delete($participantDocument->file_url);
                    }
                }

                \App\Models\UserHackatonDocument::query()
                    ->where('user_id', Auth::id())
                    ->whereIn('hackaton_document_id', $requiredHackatonDocumentIds)
                    ->delete();
            }

            return;
        }
    }

    public function saveRequiredDocument($hackatonDocumentId): void
    {
        if (!Auth::check()) {
            return;
        }

        $hasOccupiedRole = $this->team->roles()->where('user_id', Auth::id())->exists();
        if (!$hasOccupiedRole) {
            return;
        }

        $hackatonDocument = $this->team->hackaton->documents()
            ->where('id', $hackatonDocumentId)
            ->where('filling_by_team_member', true)
            ->first();

        if (!$hackatonDocument) {
            return;
        }

        $this->validate([
            'documentUploads.' . $hackatonDocumentId => ['required', 'file', 'max:10240'],
        ], [
            'documentUploads.' . $hackatonDocumentId . '.required' => 'Необходимо выбрать файл.',
            'documentUploads.' . $hackatonDocumentId . '.file' => 'Некорректный формат файла.',
            'documentUploads.' . $hackatonDocumentId . '.max' => 'Размер файла не должен превышать 10 МБ.',
        ]);

        $file = $this->documentUploads[$hackatonDocumentId];
        $storedFile = $file->storePublicly('user_hackaton_documents', options: 'public');

        \App\Models\UserHackatonDocument::query()->updateOrCreate(
            [
                'user_id' => Auth::id(),
                'hackaton_document_id' => $hackatonDocumentId,
            ],
            [
                'file_url' => $storedFile,
            ]
        );

        unset($this->documentUploads[$hackatonDocumentId]);
    }

    public function downloadRequiredDocument($hackatonDocumentId)
    {
        if (!Auth::check()) {
            return;
        }

        $document = \App\Models\UserHackatonDocument::query()
            ->where('user_id', Auth::id())
            ->where('hackaton_document_id', $hackatonDocumentId)
            ->first();

        if (!$document) {
            return;
        }

        return Storage::disk('public')->download($document->file_url);
    }

    public function participantRequiredDocuments()
    {
        if (!Auth::check()) {
            return Collection::make();
        }

        return \App\Models\UserHackatonDocument::query()
            ->where('user_id', Auth::id())
            ->whereIn('hackaton_document_id', $this->team->hackaton->documents()->where('filling_by_team_member', true)->pluck('id'))
            ->get()
            ->keyBy('hackaton_document_id');
    }
};
?>

<div>
    <div class="flex flex-col lg:flex-row gap-4">
        <x-mary-card class="card card-border h-fit w-full">
            <img src="/uploads/{{$team->image_url}}" alt="">
        </x-mary-card>
        <x-mary-card class="card card-border card-body w-full max-h-full overflow-y-auto">
            {{--Title--}}
            <h3 class="text-3xl card-title">{{$team->title}}</h3>

            {{--Description--}}
            <x-mary-card class="card card-border bg-base-300 mt-2 markdownContainer">
                <p class="card-title">Описание</p>

                @php
                    $html = Str::markdown(Str::replace("\n", "  \n", $team->description));
                    $config = [
                        'renderer' => [
                            'block_separator' => "  \n",
                            'inner_separator' => "  \n",
                            'soft_break'      => "  \n",
                        ],
                        'commonmark' => [
                            'enable_em' => true,
                            'enable_strong' => true,
                            'use_asterisk' => true,
                            'use_underscore' => true,
                            'unordered_list_markers' => ['-', '*', '+'],
                        ],
                        'html_input' => 'escape',
                        'allow_unsafe_links' => false,
                        'max_nesting_level' => PHP_INT_MAX,
                        'max_delimiters_per_line' => PHP_INT_MAX,
                        'slug_normalizer' => [
                            'max_length' => 255,
                        ],
                    ];
                @endphp
                <main class="prose">
                    {!! $html !!}
                </main>
                <x-markdown :options="$config">{{ $team->description }}</x-markdown>
            </x-mary-card>

            {{--Hackaton--}}
            <x-mary-card class="card card-border bg-base-300 mt-2">
                <p class="card-title">Хакатон</p>
                <p>{{$team->hackaton->title}}</p>
            </x-mary-card>

            {{--Author--}}
            <x-mary-card class="card card-border bg-base-300 mt-2">
                <p class="card-title">Авторство</p>
                <p>Создатель: {{$team->user->nickname}}</p>
            </x-mary-card>

            {{--SocialLinks--}}
            <x-mary-card class="card card-border bg-base-300 mt-2">
                <p class="card-title">Социальные ссылки</p>
                <div class="space-y-2">
                    @foreach($team->socialLinks as $social)
                        <x-mary-card>
                            <a href="{{$social->url}}" target="_blank" class="btn btn-primary">{{$social->name}}</a>
                        </x-mary-card>
                    @endforeach
                </div>
            </x-mary-card>

            {{--Roles--}}
            <x-mary-card class="card card-border bg-base-300 mt-2">
                <div class="space-y-4">
                    <p class="card-title">Роли</p>

                    @foreach($team->roles as $role)
                        <x-mary-card class="card card-border">
                            <h5 class="card-title">{{$role->title}}
                                <div class="badge badge-primary">
                                    @if($role->user_id == null)
                                        Свободна

                                    @else
                                        Занята
                                    @endif
                                </div>
                            </h5>
                            <x-mary-card class="card mt-2">
                                <p class="card-title">Описание</p>
                                <x-mary-card class="card card-border">
                                    <x-markdown>{{$role->description}}</x-markdown>
                                </x-mary-card>
                            </x-mary-card>

                            <x-mary-card class="card">
                                <p class="card-title">Навыки</p>
                                <x-mary-card class="card card-border">
                                    @foreach($role->skills as $skill)
                                        <x-marybadge value="{{$skill->name}}"/>
                                    @endforeach
                                </x-mary-card>
                            </x-mary-card>


                            @if($role->user_id == null)
                                <x-mary-button wire:click="appendToRole({{$role->id}})" class="btn-primary"
                                               label="Занять роль"/>
                            @elseif($role->user_id == Auth::id())
                                <x-mary-button wire:click="truncateFromRole({{$role->id}})" class="btn-secondary"
                                               label="Уйти с роли"/>

                                @php
                                    $requiredDocuments = $team->hackaton->documents->where('filling_by_team_member', true);
                                    $participantDocuments = $this->participantRequiredDocuments();
                                @endphp

                                @if($requiredDocuments->isNotEmpty())
                                    <x-marycard class="card card-border bg-base-200 mt-3">
                                        <p class="card-title">Документы для заполнения</p>

                                        <div class="space-y-3 mt-2">
                                            @foreach($requiredDocuments as $requiredDocument)
                                                <x-mary-card class="card card-border" wire:key="required-document-{{$requiredDocument->id}}">
                                                    <div class="flex items-center justify-between gap-2">
                                                        <p class="font-semibold">{{$requiredDocument->name}}</p>
                                                        @if($participantDocuments->has($requiredDocument->id))
                                                            <x-marybadge value="Загружен" class="badge-success" />
                                                        @else
                                                            <x-marybadge value="Не загружен" class="badge-warning" />
                                                        @endif
                                                    </div>
                                                    <x-markdown>{{$requiredDocument->description}}</x-markdown>

                                                    <x-maryfile wire:model="documentUploads.{{$requiredDocument->id}}" />

                                                    <div class="flex flex-col sm:flex-row gap-2 mt-2">
                                                        <x-marybutton
                                                            wire:click="saveRequiredDocument({{$requiredDocument->id}})"
                                                            class="btn-primary"
                                                            label="Загрузить файл"
                                                        />

                                                    </div>
                                                </x-mary-card>
                                            @endforeach
                                        </div>
                                    </x-mary-card>
                                @endif
                            @endif


                        </x-mary-card>
                    @endforeach


                </div>
            </x-mary-card>

        </x-mary-card>
    </div>

</div>
