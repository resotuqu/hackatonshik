<?php

use App\Models\Hackaton;
use Illuminate\Support\Collection;
use Livewire\Attributes\Lazy;
use Livewire\Component;

new #[Lazy] class extends Component
{
    public Hackaton $hackaton;

    public bool $isOrganizer = false;

    /**
     * @var Collection<int, mixed>
     */
    public Collection $applications;

    public string $applicationStatusFilter = '';

    /**
     * @param  Collection<int, mixed>  $applications
     */
    public function mount(
        Hackaton $hackaton,
        bool $isOrganizer,
        Collection $applications,
        string $applicationStatusFilter,
    ): void {
        $this->authorize('view', $hackaton);
        $this->hackaton = $hackaton;
        $this->isOrganizer = $isOrganizer;
        $this->applications = $applications;
        $this->applicationStatusFilter = $applicationStatusFilter;
    }
};
?>

@if($isOrganizer)
        <div class="card bg-base-100 border border-base-200 shadow-sm">
            <div class="card-body">
                <h2 class="card-title text-xl">Заявки команд</h2>
                <form method="GET" class="my-3 flex items-center gap-2">
                    <select name="applications_status" class="select select-bordered select-sm">
                        <option value="">Все статусы</option>
                        <option value="pending" @selected($applicationStatusFilter === 'pending')>На рассмотрении</option>
                        <option value="accepted" @selected($applicationStatusFilter === 'accepted')>Принята</option>
                        <option value="rejected" @selected($applicationStatusFilter === 'rejected')>Отклонена</option>
                    </select>
                    <button class="btn btn-sm btn-outline">Фильтровать</button>
                </form>

                @if($applications->isEmpty())
                    <p class="text-base-content/60">Пока нет заявок. Когда команды подадут заявки, они появятся в этом списке.</p>
                @else
                    <div class="mb-3 flex items-center gap-2">
                        <select form="bulk-status-update" name="status" class="select select-bordered select-sm">
                            <option value="accepted">Принять</option>
                            <option value="rejected">Отклонить</option>
                        </select>
                        <button form="bulk-status-update" class="btn btn-sm btn-primary">Применить к выбранным</button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Команда</th>
                                    <th>Сообщение</th>
                                    <th>Отправлена</th>
                                    <th>Статус</th>
                                    <th>Рассмотрел</th>
                                    <th>Рассмотрена</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($applications as $app)
                                    <tr>
                                        <td>
                                            @if($app->status->isPending())
                                                <input form="bulk-status-update" type="checkbox" name="application_ids[]" value="{{ $app->id }}" class="checkbox checkbox-sm">
                                            @endif
                                        </td>
                                        <td>{{ $app->team->title }}</td>
                                        <td class="max-w-xs truncate">{{ $app->message }}</td>
                                        <td>{{ $app->created_at?->format('d.m.Y H:i') ?? '—' }}</td>
                                        <td>
                                            <span class="badge badge-{{ $app->status->isAccepted() ? 'success' : ($app->status->isRejected() ? 'error' : 'warning') }}">
                                                {{ $app->status->label() }}
                                            </span>
                                        </td>
                                        <td>{{ $app->reviewer?->nickname ?? $app->reviewer?->name ?? '—' }}</td>
                                        <td>{{ $app->reviewed_at?->format('d.m.Y H:i') ?? '—' }}</td>
                                        <td>
                                            @if($app->status->isPending())
                                                <form method="POST" action="{{ route('hackaton.applications.update', $app) }}" class="inline-flex">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="accepted">
                                                    <button class="btn btn-success btn-xs">Принять</button>
                                                </form>

                                                <form method="POST" action="{{ route('hackaton.applications.update', $app) }}" class="inline-flex ml-2"
                                                    onsubmit="return confirm('Отклонить заявку команды?');">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="rejected">
                                                    <button class="btn btn-error btn-xs">Отклонить</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <form id="bulk-status-update" method="POST" action="{{ route('hackaton.applications.bulk-update', $hackaton) }}" class="hidden">
                        @csrf
                        @method('PATCH')
                    </form>
                @endif
            </div>
        </div>
@else
        <div class="card bg-base-100 border border-base-200 shadow-sm">
            <div class="card-body">
                <h2 class="card-title text-xl">Участники хакатона</h2>
                <p class="text-base-content/70">Статистика участников доступна в разделе «Описание». Для заявок вашей команды используйте карточку «Информация о хакатоне».</p>
            </div>
        </div>
@endif