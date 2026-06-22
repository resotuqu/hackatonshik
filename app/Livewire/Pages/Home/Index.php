<?php

namespace App\Livewire\Pages\Home;

use App\Actions\Hackaton\SuggestTeamsForUser;
use App\Enums\HackatonStatus;
use App\Models\Hackaton;
use App\Models\Team;
use App\Models\TeamRole;
use App\Models\User;
use App\ViewModels\HomeDashboardData;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts::app', [
    'title' => 'Хакатонщик — платформа для хакатонов, команд и участников',
    'meta_description' => 'Хакатонщик помогает участникам, командам и организаторам находить друг друга, запускать и проводить хакатоны — от поиска команды до сертификатов.',
    'canonical_url' => '/',
])]
class Index extends Component
{
    /** @var array<int, Hackaton> */
    public array $featuredHackatons = [];

    /** Публичные хакатоны (все статусы кроме черновика), включая завершённые и в архиве */
    public int $publicHackatonsCount = 0;

    public int $publicParticipantsCount = 0;

    public int $publicTeamsCount = 0;

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

    /** @var list<array{team: Team, match_score: int, matched_skills: list<string>}> */
    public array $recommendedTeams = [];

    public string $activeDashboardRole = '';

    public function mount(): void
    {
        $homeCatalogCache = Cache::supportsTags()
            ? Cache::tags(['home', 'catalog'])
            : Cache::store();

        $this->featuredHackatons = $homeCatalogCache->remember('home-featured-hackatons-v2', now()->addMinutes(10), function (): array {
            return Hackaton::query()
                ->select('hackatons.*')
                ->selectSub(function ($query) {
                    $query->from('team_roles')
                        ->join('teams', 'teams.id', '=', 'team_roles.team_id')
                        ->whereColumn('teams.hackaton_id', 'hackatons.id')
                        ->whereNotNull('team_roles.user_id')
                        ->selectRaw('count(*)');
                }, 'participants_count')
                ->where('is_public', true)
                ->whereIn('status', [
                    HackatonStatus::PUBLISHED,
                    HackatonStatus::REGISTRATION_OPEN,
                    HackatonStatus::REGISTRATION_CLOSED,
                    HackatonStatus::WAITING_START,
                    HackatonStatus::CASES_ANNOUNCED,
                    HackatonStatus::IN_PROGRESS,
                    HackatonStatus::JUDGING,
                ])
                ->withCount('teams')
                ->latest('start_at')
                ->limit(4)
                ->get()
                ->all();
        });

        // Все публичные события кроме черновика (в т.ч. завершённые и в архиве) + суммарно команды и участники по ним.
        $totals = $homeCatalogCache->remember('home-public-totals-v4', now()->addMinutes(10), function (): array {

            $hackatonsCount = Hackaton::query()
                ->where('is_public', true)
                ->whereNot('status', HackatonStatus::DRAFT)
                ->count();

            $teamsCount = Team::query()
                ->whereExists(function ($query): void {
                    $query->selectRaw('1')
                        ->from('hackatons')
                        ->whereColumn('hackatons.id', 'teams.hackaton_id')
                        ->where('hackatons.is_public', true)
                        ->whereNot('hackatons.status', HackatonStatus::DRAFT);
                })
                ->count();

            $participantsCount = TeamRole::query()
                ->whereNotNull('user_id')
                ->whereExists(function ($query): void {
                    $query->from('teams')
                        ->join('hackatons', 'hackatons.id', '=', 'teams.hackaton_id')
                        ->whereColumn('teams.id', 'team_roles.team_id')
                        ->where('hackatons.is_public', true)
                        ->whereNot('hackatons.status', HackatonStatus::DRAFT);
                })
                ->count();

            return [
                'hackatons' => $hackatonsCount,
                'participants' => $participantsCount,
                'teams' => $teamsCount,
            ];
        });

        $this->publicHackatonsCount = (int) $totals['hackatons'];
        $this->publicParticipantsCount = (int) $totals['participants'];
        $this->publicTeamsCount = (int) $totals['teams'];

        if (! Auth::check()) {
            return;
        }

        $user = Auth::user();
        if (! $user instanceof User) {
            return;
        }

        foreach (HomeDashboardData::fromUser($user)->toLivewireArray() as $key => $value) {
            $this->{$key} = $value;
        }

        if ($user->isParticipant()) {
            $this->recommendedTeams = app(SuggestTeamsForUser::class)
                ->handle($user, 3)
                ->all();
        }

        $availableRoles = $this->availableDashboardRoles($user);
        $sessionRole = session('home.dashboard_role');

        $this->activeDashboardRole = is_string($sessionRole) && array_key_exists($sessionRole, $availableRoles)
            ? $sessionRole
            : array_key_first($availableRoles);
    }

    /**
     * @return array<string, string>
     */
    public function availableDashboardRoles(User $user): array
    {
        $roles = [];

        if ($user->isParticipant()) {
            $roles['participant'] = __('ui.dashboard.roles.participant');
        }

        if ($user->isOrganizer()) {
            $roles['organizer'] = __('ui.dashboard.roles.organizer');
        }

        if ($user->isJudge()) {
            $roles['judge'] = __('ui.dashboard.roles.judge');
        }

        if ($user->isModerator()) {
            $roles['moderator'] = __('ui.dashboard.roles.moderator');
        }

        if ($user->isAdmin()) {
            $roles['admin'] = __('ui.dashboard.roles.admin');
        }

        return $roles;
    }

    public function switchDashboardRole(string $role): void
    {
        $user = Auth::user();

        if (! $user instanceof User) {
            return;
        }

        $availableRoles = $this->availableDashboardRoles($user);

        abort_unless(array_key_exists($role, $availableRoles), 403);

        $this->activeDashboardRole = $role;
        session(['home.dashboard_role' => $role]);
    }

    public function placeholder()
    {
        return view('pages.home.index-skeleton');
    }

    public function render()
    {
        return view('pages.home.index');
    }
}
