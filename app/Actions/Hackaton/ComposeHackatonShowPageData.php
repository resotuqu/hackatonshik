<?php

declare(strict_types=1);

namespace App\Actions\Hackaton;

use App\Models\Hackaton;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComposeHackatonShowPageData
{
    public function __construct(
        private readonly ResolveHackatonShowAudience $resolveAudience,
        private readonly LoadHackatonRelationsForShowPage $loadRelations,
        private readonly ListHackatonApplicationsForOrganizer $listOrganizerApplications,
        private readonly HydrateHackatonApplicationsForViewer $hydrateApplications,
        private readonly ResolveAvailableTeamsForUser $resolveAvailableTeams,
        private readonly ResolveSubmitterTeamsForHackaton $resolveSubmitterTeams,
        private readonly ResolveParticipantUsersForHackatonCertificates $resolveParticipantUsers,
        private readonly BuildHackatonOrganizationMetrics $buildMetrics,
        private readonly BuildHackatonTeamLeaderboard $buildLeaderboard,
        private readonly ResolveJudgeManagementDataset $resolveJudgeManagementDataset,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function build(Hackaton $hackaton, Request $request): array
    {
        $user = Auth::user();
        $audience = $this->resolveAudience->handle($hackaton, $user);

        $isOrganizer = $audience['isOrganizer'];
        $isAssignedJudge = $audience['isAssignedJudge'];
        $needsOrganizationInsights = $audience['needsOrganizationInsights'];

        $this->loadRelations->handle($hackaton, $user, $isOrganizer, $isAssignedJudge);

        $applicationStatusFilter = $request->string('applications_status')->toString();
        $organizerApplications = $isOrganizer
            ? $this->listOrganizerApplications->handle($hackaton, $applicationStatusFilter)
            : collect();

        $this->hydrateApplications->handle($hackaton, $user, $isOrganizer, $organizerApplications);

        $availableTeams = $this->resolveAvailableTeams->handle($user);
        $submitterTeams = $this->resolveSubmitterTeams->handle($hackaton);
        $participantUsers = $isOrganizer
            ? $this->resolveParticipantUsers->handle($hackaton)
            : collect();

        $metrics = $needsOrganizationInsights
            ? $this->buildMetrics->handle($hackaton)
            : $this->buildMetrics->empty();
        $leaderboard = $needsOrganizationInsights
            ? $this->buildLeaderboard->handle($hackaton)
            : collect();

        $judgeManagement = $this->resolveJudgeManagementDataset->handle($hackaton, $isOrganizer);

        return [
            'isOrganizer' => $isOrganizer,
            'isAssignedJudge' => $isAssignedJudge,
            'availableTeams' => $availableTeams,
            'submitterTeams' => $submitterTeams,
            'participantUsers' => $participantUsers,
            'applications' => $organizerApplications,
            'applicationStatusFilter' => $applicationStatusFilter,
            'metrics' => $metrics,
            'leaderboard' => $leaderboard,
            'judgeCandidates' => $judgeManagement['judgeCandidates'],
            'pendingJudgeInvitations' => $judgeManagement['pendingJudgeInvitations'],
        ];
    }
}
