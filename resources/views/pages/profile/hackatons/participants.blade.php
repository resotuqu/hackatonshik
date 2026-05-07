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
            @if ($this->teamRows === [])
                <x-empty-state
                    title="Команд пока нет"
                    description="Когда команды зарегистрируются на хакатон, они появятся в этой таблице."
                    icon="heroicons:user-group"
                    compact
                />
            @else
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
                                                                                <x-app-icon icon="heroicons:check-circle" class="h-5 w-5 text-success" />
                                                                            @else
                                                                                <x-app-icon icon="heroicons:x-circle" class="h-5 w-5 text-error" />
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
                </x-marytable>
            @endif
        </div>
    </x-mary-card>
</div>
