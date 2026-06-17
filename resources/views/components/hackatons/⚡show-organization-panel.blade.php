<?php

use App\Actions\Hackaton\BuildHackatonOrganizationMetrics;
use App\Actions\Hackaton\BuildHackatonTeamLeaderboard;
use App\Actions\Hackaton\ResolveJudgeManagementDataset;
use App\Actions\Hackaton\ResolveParticipantUsersForHackatonCertificates;
use App\Models\Hackaton;
use Livewire\Attributes\Lazy;
use Livewire\Component;

new #[Lazy] class extends Component
{
    public Hackaton $hackaton;

    public bool $isOrganizer = false;

    public bool $isAssignedJudge = false;

    /**
     * @var array<string, string>
     */
    public array $modals = [];

    /**
     * @var array<string, mixed>|null
     */
    public ?array $organizationPreload = null;

    public function mount(
        Hackaton $hackaton,
        bool $isOrganizer,
        bool $isAssignedJudge,
        array $modals,
        ?array $organizationPreload = null,
    ): void {
        $this->authorize('view', $hackaton);
        $this->hackaton = $hackaton;
        $this->isOrganizer = $isOrganizer;
        $this->isAssignedJudge = $isAssignedJudge;
        $this->modals = $modals;
        $this->organizationPreload = $organizationPreload;
    }

    /**
     * @return \Illuminate\View\View
     */
    public function render(
        BuildHackatonOrganizationMetrics $buildMetrics,
        BuildHackatonTeamLeaderboard $buildLeaderboard,
        ResolveJudgeManagementDataset $resolveJudgeManagement,
        ResolveParticipantUsersForHackatonCertificates $resolveParticipantUsers,
    ) {
        if ($this->organizationPreload !== null) {
            return view('components.hackatons.⚡show-organization-panel', [
                'metrics' => $this->organizationPreload['metrics'],
                'leaderboard' => $this->organizationPreload['leaderboard'],
                'judgeCandidates' => $this->organizationPreload['judgeCandidates'],
                'pendingJudgeInvitations' => $this->organizationPreload['pendingJudgeInvitations'],
                'participantUsers' => $this->organizationPreload['participantUsers'],
                'issuedCertificatesByUser' => $this->organizationPreload['issuedCertificatesByUser'],
            ]);
        }

        $metrics = ($this->isOrganizer || $this->isAssignedJudge)
            ? $buildMetrics->handle($this->hackaton)
            : $buildMetrics->empty();

        $leaderboard = ($this->isOrganizer || $this->isAssignedJudge)
            ? $buildLeaderboard->handle($this->hackaton)
            : collect();

        $judgeManagement = $resolveJudgeManagement->handle($this->hackaton, $this->isOrganizer);

        $participantUsers = $this->isOrganizer
            ? $resolveParticipantUsers->handle($this->hackaton)
            : collect();

        $issuedCertificatesByUser = $this->isOrganizer
            ? $this->hackaton->certificates->groupBy('user_id')
            : collect();

        return view('components.hackatons.⚡show-organization-panel', [
            'metrics' => $metrics,
            'leaderboard' => $leaderboard,
            'judgeCandidates' => $judgeManagement['judgeCandidates'],
            'pendingJudgeInvitations' => $judgeManagement['pendingJudgeInvitations'],
            'participantUsers' => $participantUsers,
            'issuedCertificatesByUser' => $issuedCertificatesByUser,
        ]);
    }
};
?>

<div class="space-y-6">
    <div class="divider">{{ $isOrganizer ? 'Для организатора' : 'Для судьи' }}</div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="card bg-base-100 border border-base-200 shadow-sm">
            <div class="card-body">
                <p class="text-sm text-base-content/70">Заявки (всего)</p>
                <p class="text-3xl font-semibold">{{ $metrics['applications_total'] }}</p>
                <p class="text-xs">В работе: {{ $metrics['applications_pending'] }}</p>
            </div>
        </div>
        <div class="card bg-base-100 border border-base-200 shadow-sm">
            <div class="card-body">
                <p class="text-sm text-base-content/70">Решения кейсов</p>
                <p class="text-3xl font-semibold">{{ $metrics['submissions_total'] }}</p>
                <p class="text-xs">Оценено: {{ $metrics['submissions_scored'] }}</p>
            </div>
        </div>
        <div class="card bg-base-100 border border-base-200 shadow-sm">
            <div class="card-body">
                <p class="text-sm text-base-content/70">Прогресс оценивания</p>
                <p class="text-3xl font-semibold">{{ $metrics['submissions_scored_percent'] }}%</p>
            </div>
        </div>
    </div>

    <div class="card bg-base-100 border border-base-200 shadow-sm">
        <div class="card-body">
            <h2 class="card-title text-xl">Рейтинг команд</h2>
            @if($leaderboard === [])
                <p class="text-base-content/60">Пока нет оцененных решений команд.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Команда</th>
                                <th>Баллы</th>
                                <th>Макс. баллы</th>
                                <th>Прогресс</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($leaderboard as $row)
                                <tr>
                                    <td>{{ $row['team']->title }}</td>
                                    <td>{{ $row['total_score'] }}</td>
                                    <td>{{ $row['max_score'] }}</td>
                                    <td>{{ $row['completion_percent'] }}%</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

@if($isOrganizer)
        <div class="card bg-base-100 border border-base-200 shadow-sm">
            <div class="card-body">
                <h2 class="card-title text-xl">Экспорт данных</h2>
                <p class="text-sm text-base-content/70">Скачайте команды, участников или архив подписанных документов.</p>
                <div class="flex flex-wrap gap-2 mt-2">
                    <a href="{{ route('hackatons.export.teams', $hackaton) }}" class="btn btn-sm btn-outline">Экспорт команд (CSV)</a>
                    <a href="{{ route('hackatons.export.participants', $hackaton) }}" class="btn btn-sm btn-outline">Экспорт участников (CSV)</a>
                    <a href="{{ route('hackatons.export.documents-zip', $hackaton) }}" class="btn btn-sm btn-primary">Документы (ZIP)</a>
                </div>
            </div>
        </div>

        <div class="card bg-base-100 border border-base-200 shadow-sm">
            <div class="card-body space-y-4">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <h2 class="card-title text-xl">Судьи хакатона</h2>
                    <div class="flex flex-wrap gap-2">
                        <x-organizer-action-modal
                            :modal-id="$modals['judge_invite']"
                            button-label="Пригласить судью"
                            title="Приглашение судьи по email">
                            <form method="POST" action="{{ route('hackatons.judges.invite', $hackaton) }}" class="space-y-3">
                                @csrf
                                <input type="hidden" name="_open_modal" value="{{ $modals['judge_invite'] }}">
                                <input name="email" type="email" class="input input-bordered w-full" placeholder="email судьи для приглашения" required autofocus>
                                <button class="btn btn-primary btn-sm">Отправить инвайт</button>
                            </form>
                        </x-organizer-action-modal>

                        <x-organizer-action-modal
                            :modal-id="$modals['judge_assign']"
                            button-label="Назначить судью"
                            button-class="btn btn-outline btn-sm"
                            title="Назначение зарегистрированного судьи">
                            <form method="POST" action="{{ route('hackatons.judges.assign', $hackaton) }}" class="space-y-3">
                                @csrf
                                <input type="hidden" name="_open_modal" value="{{ $modals['judge_assign'] }}">
                                <select name="user_id" class="select select-bordered w-full" required autofocus>
                                    <option value="">Выберите зарегистрированного судью</option>
                                    @foreach($judgeCandidates as $candidate)
                                        <option value="{{ $candidate->id }}">{{ $candidate->fio }} ({{ $candidate->email }})</option>
                                    @endforeach
                                </select>
                                <select name="domain" class="select select-bordered w-full">
                                    <option value="dev">Разработка</option>
                                    <option value="design">Дизайн</option>
                                    <option value="business">Бизнес</option>
                                </select>
                                <button class="btn btn-outline btn-sm">Назначить</button>
                            </form>
                        </x-organizer-action-modal>
                    </div>
                </div>
                @if($hackaton->judges->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Судья</th>
                                    <th>Email</th>
                                    <th>Домен</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($hackaton->judges as $judge)
                                    <tr>
                                        <td>{{ $judge->fio }}</td>
                                        <td>{{ $judge->email }}</td>
                                        <td class="text-xs">{{ $judge->pivot->domain ?? 'dev' }}</td>
                                        <td class="text-right">
                                            <form method="POST" action="{{ route('hackatons.judges.unassign', [$hackaton, $judge]) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-xs btn-error">Снять</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
                @if($pendingJudgeInvitations->isNotEmpty())
                    <div class="space-y-1">
                        <p class="text-sm font-medium">Ожидают подтверждения</p>
                        @foreach($pendingJudgeInvitations as $invite)
                            <p class="text-xs text-base-content/70">{{ $invite->invited_email }} - {{ route('judges.invitations.accept', $invite->token) }}</p>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="card bg-base-100 border border-base-200 shadow-sm">
            <div class="card-body space-y-4">
                <div class="flex items-center justify-between gap-3">
                    <h2 class="card-title text-xl">Управление кейсами</h2>
                    <x-organizer-action-modal
                        :modal-id="$modals['case_create']"
                        button-label="Новый кейс"
                        title="Создание нового кейса"
                        description="Заполните параметры публикации и дедлайна.">
                        <form method="POST" action="{{ route('hackatons.cases.store', $hackaton) }}" class="grid grid-cols-1 gap-3 md:grid-cols-2">
                            @csrf
                            <input type="hidden" name="_open_modal" value="{{ $modals['case_create'] }}">
                            <input name="title" class="input input-bordered" placeholder="Название кейса" required autofocus>
                            <label class="label cursor-pointer justify-start gap-2">
                                <input type="checkbox" name="is_published" value="1" class="checkbox checkbox-sm">
                                <span class="label-text">Опубликовать сразу</span>
                            </label>
                            <input name="publish_at" type="datetime-local" class="input input-bordered" placeholder="Дата публикации">
                            <input name="deadline_at" type="datetime-local" class="input input-bordered" placeholder="Дедлайн">
                            <textarea name="description" rows="3" class="textarea textarea-bordered md:col-span-2"
                                placeholder="Описание кейса"></textarea>
                            <button class="btn btn-primary btn-sm md:col-span-2">Добавить кейс</button>
                        </form>
                    </x-organizer-action-modal>
                </div>
            </div>
        </div>
    @endif

    @if($isOrganizer)
        <div class="card bg-base-100 border border-base-200 shadow-sm">
            <div class="card-body space-y-4">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <h2 class="card-title text-xl">Сертификаты участников</h2>
                    @if($participantUsers->isNotEmpty())
                        <x-organizer-action-modal
                            :modal-id="$modals['certificate_upload']"
                            button-label="Загрузить сертификат"
                            title="Загрузка сертификата"
                            description="Выберите одного или нескольких участников и загрузите файл сертификата.">
                            <form method="POST" enctype="multipart/form-data" action="{{ route('hackatons.certificates.store', $hackaton) }}"
                                class="grid grid-cols-1 gap-3 md:grid-cols-2">
                                @csrf
                                <input type="hidden" name="_open_modal" value="{{ $modals['certificate_upload'] }}">
                                <select name="user_ids[]" class="select select-bordered md:col-span-2" multiple required autofocus>
                                    @foreach($participantUsers as $participant)
                                        <option value="{{ $participant->id }}">
                                            {{ $participant->fio ?? $participant->nickname ?? $participant->email }}
                                            @if($issuedCertificatesByUser->has($participant->id))
                                                (уже выдано: {{ $issuedCertificatesByUser->get($participant->id)->count() }})
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                <input name="title" class="input input-bordered" placeholder="Название сертификата" required>
                                <input name="issued_at" type="date" class="input input-bordered">
                                <input name="file" type="file" class="file-input file-input-bordered md:col-span-2" required>
                                <button class="btn btn-primary btn-sm md:col-span-2">Загрузить сертификат</button>
                            </form>
                        </x-organizer-action-modal>
                    @endif
                </div>
                @if($participantUsers->isEmpty())
                    <p class="text-base-content/60">Пока нет участников для выдачи сертификатов.</p>
                @endif

                @if($hackaton->certificates->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Участник</th>
                                    <th>Название</th>
                                    <th>Дата выдачи</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($hackaton->certificates as $certificate)
                                    <tr>
                                        <td>{{ $certificate->user->fio ?? $certificate->user->nickname ?? $certificate->user->email }}</td>
                                        <td>{{ $certificate->title }}</td>
                                        <td>{{ $certificate->issued_at?->format('d.m.Y') ?? '—' }}</td>
                                        <td class="text-right">
                                            <a href="{{ route('certificates.download', $certificate) }}" class="btn btn-xs btn-outline">Скачать</a>
                                            <form method="POST" action="{{ route('hackatons.certificates.destroy', [$hackaton, $certificate]) }}" class="inline-block"
                                                onsubmit="return confirm('Удалить сертификат?');">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-xs btn-ghost text-error">Удалить</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
@endif
</div>