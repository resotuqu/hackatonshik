<?php

declare(strict_types=1);

namespace App\ViewModels;

use App\Enums\ApplicationStatus;
use App\Models\Hackaton;
use App\Models\HackatonApplication;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

final class HomeDashboardData
{
    public int $teamsCount = 0;

    public int $certificatesCount = 0;

    public int $pendingTeamApplicationsCount = 0;

    public int $pendingHackatonApplicationsCount = 0;

    /** @var list<array{id: int, hackaton_id: int, title: string, team_title: string, status_label: string}> */
    public array $hackatonApplicationsPreview = [];

    /** @var list<array{id: int, title: string, start_at: string|null}> */
    public array $participantHackatonsPreview = [];

    public string $participantNextStepTitle = '';

    public string $participantNextStepHint = '';

    public ?string $participantNextStepHref = null;

    public ?string $participantNextStepLabel = null;

    public int $hackatonsCount = 0;

    public ?int $organizerFirstPendingHackatonId = null;

    public int $judgeHackatonsCount = 0;

    /** @var list<array{id: int, title: string, start_at: string|null}> */
    public array $judgeHackatonsPreview = [];

    public int $usersCount = 0;

    public int $adminHackatonsCount = 0;

    public int $adminPartnersCount = 0;

    public int $adminPendingApplicationsCount = 0;

    public int $unreadNotificationsCount = 0;

    public bool $showPhoneVerificationBanner = false;

    public static function fromUser(User $user): self
    {
        $cacheKey = "home-dashboard:user:{$user->id}:v1";
        $cache = Cache::supportsTags()
            ? Cache::tags(['dashboard', "dashboard:user:{$user->id}"])
            : Cache::store();
        $payload = $cache->remember($cacheKey, now()->addSeconds(60), function () use ($user): array {
            return self::buildForUser($user)->toLivewireArray();
        });

        return self::fromLivewireArray($payload);
    }

    /** @param array<string, mixed> $data */
    private static function fromLivewireArray(array $data): self
    {
        $self = new self;

        foreach ($data as $key => $value) {
            if (property_exists($self, (string) $key)) {
                $self->{$key} = $value;
            }
        }

        return $self;
    }

    private static function buildForUser(User $user): self
    {
        $self = new self;

        $userWithCounts = User::query()
            ->where('id', $user->id)
            ->withCount([
                'unreadNotifications',
                'teams',
                'certificates',
                'teamApplications' => fn (Builder $q) => $q->where('status', ApplicationStatus::PENDING),
            ])
            ->first();

        if (! $userWithCounts) {
            return $self;
        }

        $self->unreadNotificationsCount = (int) $userWithCounts->unread_notifications_count;
        $self->showPhoneVerificationBanner = $user->phone !== null
            && $user->phone !== ''
            && $user->phone_verified_at === null;

        if ($user->isParticipant()) {
            $self->teamsCount = (int) $userWithCounts->teams_count;
            $self->certificatesCount = (int) $userWithCounts->certificates_count;
            $self->pendingTeamApplicationsCount = (int) $userWithCounts->team_applications_count;
            $self->fillParticipant($user);

            return $self;
        }

        if ($user->isOrganizer()) {
            $self->fillOrganizer($user);

            return $self;
        }

        if ($user->isJudge()) {
            $self->fillJudge($user);

            return $self;
        }

        if ($user->isAdmin()) {
            $self->fillAdmin();

            return $self;
        }

        return $self;
    }

    /** @return array<string, mixed> */
    public function toLivewireArray(): array
    {
        return get_object_vars($this);
    }

    private function fillParticipant(User $user): void
    {
        $pendingQuery = $this->participantPendingHackatonApplicationsQuery($user);

        $pendingHackatonApps = (clone $pendingQuery)
            ->with(['hackaton:id,title', 'team:id,title'])
            ->latest()
            ->limit(5)
            ->get();

        $this->pendingHackatonApplicationsCount = (clone $pendingQuery)->count();

        $this->hackatonApplicationsPreview = $pendingHackatonApps->map(function (HackatonApplication $app): array {
            $hackaton = $app->hackaton;
            $team = $app->team;

            return [
                'id' => $app->id,
                'hackaton_id' => $app->hackaton_id,
                'title' => $hackaton instanceof Hackaton ? $hackaton->title : 'Хакатон',
                'team_title' => $team instanceof Team ? $team->title : 'Команда',
                'status_label' => $this->applicationStatusLabel($app),
            ];
        })->all();

        $this->participantHackatonsPreview = $this->buildParticipantHackatonsPreview($user);

        $this->setParticipantNextStep($user, $pendingQuery);
    }

    /**
     * @return Builder<HackatonApplication>
     */
    private function participantPendingHackatonApplicationsQuery(User $user): Builder
    {
        return HackatonApplication::query()
            ->where('status', ApplicationStatus::PENDING)
            ->whereHas('team', function (Builder $teamQuery) use ($user): void {
                $this->scopeParticipantTeams($teamQuery, $user);
            });
    }

    /**
     * Teams the participant captains or belongs to (same rule as hub / submissions).
     */
    private function scopeParticipantTeams(Builder $teamQuery, User $user): void
    {
        $teamQuery->where(function (Builder $inner) use ($user): void {
            $inner
                ->where('teams.user_id', $user->id)
                ->orWhereHas('roles', fn (Builder $rolesQuery) => $rolesQuery->where('user_id', $user->id));
        });
    }

    /** @return list<array{id: int, title: string, start_at: string|null}> */
    private function buildParticipantHackatonsPreview(User $user): array
    {
        $fromTeams = Team::query()
            ->where(function (Builder $inner) use ($user): void {
                $this->scopeParticipantTeams($inner, $user);
            })
            ->with(['hackaton' => fn ($q) => $q->select('id', 'title', 'start_at')])
            ->get()
            ->pluck('hackaton')
            ->filter()
            ->unique('id')
            ->sortBy(fn ($h) => $h->start_at->timestamp)
            ->values();

        $ids = $fromTeams->pluck('id')->all();
        $need = max(0, 5 - $fromTeams->count());

        if ($need > 0) {
            $extra = Hackaton::query()
                ->where('is_public', true)
                ->where('start_at', '>', now())
                ->when($ids !== [], fn (Builder $q) => $q->whereNotIn('id', $ids))
                ->orderBy('start_at')
                ->limit($need)
                ->get(['id', 'title', 'start_at']);
            $fromTeams = $fromTeams->concat($extra);
        }

        return $fromTeams
            ->take(5)
            ->map(fn (Hackaton $hackaton): array => [
                'id' => $hackaton->id,
                'title' => $hackaton->title,
                'start_at' => $hackaton->start_at->translatedFormat('d.m.Y H:i'),
            ])
            ->values()
            ->all();
    }

    private function setParticipantNextStep(User $user, Builder $pendingHackatonAppsQuery): void
    {
        if ($this->teamsCount === 0) {
            $this->participantNextStepTitle = 'Создайте команду';
            $this->participantNextStepHint = 'Без команды нельзя подать заявку на участие в хакатоне.';
            $this->participantNextStepHref = '/teams/create';
            $this->participantNextStepLabel = 'Создать команду';

            return;
        }

        if ($this->pendingTeamApplicationsCount > 0) {
            $this->participantNextStepTitle = 'Заявки на роли в командах';
            $this->participantNextStepHint = 'Вы подали заявки на участие в ролях — следите за статусом в списке ниже на странице «Мои команды».';
            $this->participantNextStepHref = '/profile/teams#pending-team-role-applications';
            $this->participantNextStepLabel = 'Посмотреть заявки';

            return;
        }

        if ($this->pendingHackatonApplicationsCount > 0) {
            $hid = (clone $pendingHackatonAppsQuery)
                ->orderByDesc('created_at')
                ->value('hackaton_id');
            $this->participantNextStepTitle = 'Заявки команд на хакатоны';
            $this->participantNextStepHint = 'Организатор рассматривает заявки ваших команд. Статус можно посмотреть на странице хакатона.';
            $this->participantNextStepHref = $hid !== null
                ? route('hackatons.show', $hid).'#participant-hackaton-applications'
                : '/hackatons';
            $this->participantNextStepLabel = $hid !== null ? 'К заявке на хакатон' : 'Каталог хакатонов';

            return;
        }

        $this->participantNextStepTitle = 'Найдите хакатон';
        $this->participantNextStepHint = 'Выберите событие в каталоге и подайте заявку от команды, когда будете готовы.';
        $this->participantNextStepHref = '/hackatons';
        $this->participantNextStepLabel = 'Каталог хакатонов';
    }

    private function fillOrganizer(User $user): void
    {
        $this->hackatonsCount = $user->hackatons()->count();
        $this->pendingHackatonApplicationsCount = HackatonApplication::query()
            ->where('status', ApplicationStatus::PENDING)
            ->whereHas('hackaton', fn (Builder $query) => $query->where('user_id', $user->id))
            ->count();

        $this->organizerFirstPendingHackatonId = Hackaton::query()
            ->where('user_id', $user->id)
            ->whereHas(
                'applications',
                fn (Builder $q) => $q->where('status', ApplicationStatus::PENDING)
            )
            ->orderBy('start_at')
            ->value('id');
    }

    private function fillJudge(User $user): void
    {
        $this->judgeHackatonsCount = $user->judgedHackatons()->count();
        $this->judgeHackatonsPreview = $user->judgedHackatons()
            ->orderBy('start_at')
            ->limit(5)
            ->get()
            ->map(fn (Hackaton $hackaton): array => [
                'id' => $hackaton->id,
                'title' => $hackaton->title,
                'start_at' => $hackaton->start_at->translatedFormat('d.m.Y H:i'),
            ])
            ->all();
    }

    private function fillAdmin(): void
    {
        $this->usersCount = User::query()->count();
        $this->adminHackatonsCount = Hackaton::query()->count();
        $this->adminPartnersCount = User::query()->where('role', 'partner')->count();
        $this->adminPendingApplicationsCount = HackatonApplication::query()
            ->where('status', ApplicationStatus::PENDING)
            ->count();
    }

    private function applicationStatusLabel(HackatonApplication $application): string
    {
        return ApplicationStatus::from((string) $application->getRawOriginal('status'))->label();
    }
}
