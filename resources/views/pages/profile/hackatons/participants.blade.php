<div>
    <x-mary-card class="card border border-base-300 bg-base-100">
        <nav class="text-sm breadcrumbs mb-4" aria-label="{{ __('ui.breadcrumbs.aria_label') }}">
            <ul>
                <li><a href="/">{{ __('ui.nav.home') }}</a></li>
                <li><a href="/profile">{{ __('ui.nav.profile') }}</a></li>
                <li><a href="{{ route('organizer.dashboard') }}">{{ __('ui.nav.my_hackatons') }}</a></li>
                <li class="opacity-70">{{ __('ui.hackatons.tabs.participants') }}</li>
            </ul>
        </nav>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
            <div>
                <h3 class="text-2xl font-bold">Участники хакатона</h3>
                <p class="opacity-80">{{ $hackaton->title }}</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <select class="select select-bordered select-sm" wire:model.live="documentsFilter">
                    <option value="all">Все команды</option>
                    <option value="incomplete">Неполный комплект документов</option>
                    <option value="complete">Документы загружены</option>
                </select>
                <x-mary-button
                    label="Напомнить о документах"
                    class="btn-warning btn-sm"
                    wire:click="sendDocumentReminders"
                    wire:confirm="Отправить напоминание участникам с незагруженными документами?"
                />
                <div class="dropdown dropdown-end">
                    <div tabindex="0" role="button" class="btn btn-outline btn-sm gap-1">
                        <x-app-icon icon="heroicons:arrow-down-tray" class="h-4 w-4" />
                        Экспорт
                        <x-app-icon icon="heroicons:chevron-down" class="h-3 w-3" />
                    </div>
                    <ul tabindex="-1" class="dropdown-content menu menu-sm z-50 mt-1 w-52 rounded-box border border-base-200 bg-base-100 p-1.5 shadow-lg">
                        <li>
                            <a href="{{ route('hackatons.export.applications', $hackaton) }}">
                                <x-app-icon icon="heroicons:inbox" class="h-4 w-4" />
                                Заявки (CSV)
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('hackatons.export.teams', $hackaton) }}">
                                <x-app-icon icon="heroicons:user-group" class="h-4 w-4" />
                                Команды (CSV)
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('hackatons.export.participants', $hackaton) }}">
                                <x-app-icon icon="heroicons:users" class="h-4 w-4" />
                                Участники (CSV)
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('hackatons.export.documents-zip', $hackaton) }}">
                                <x-app-icon icon="heroicons:archive-box-arrow-down" class="h-4 w-4" />
                                Документы (ZIP)
                            </a>
                        </li>
                    </ul>
                </div>
                <a href="{{ route('organizer.dashboard') }}">
                    <x-mary-button label="Назад" class="btn-secondary btn-sm" />
                </a>
            </div>
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
                        @php $participants = $this->expansionDataByTeamId[$team['id']] ?? []; @endphp

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
                                                    @if(!empty($participant['documents']))
                                                        <div>
                                                            <p class="font-semibold mb-2">Документы</p>
                                                            <div class="space-y-2">
                                                                @foreach($participant['documents'] as $doc)
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
                                                                            <a href="{{ \App\Support\PublicStorageUrl::for($doc['file_url']) }}" download>
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
