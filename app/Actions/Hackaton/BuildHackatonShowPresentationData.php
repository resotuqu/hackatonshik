<?php

declare(strict_types=1);

namespace App\Actions\Hackaton;

use App\Enums\ApplicationStatus;
use App\Models\Hackaton;
use App\Models\Team;
use App\Support\SafeMarkdown;
use Illuminate\Support\Collection;

class BuildHackatonShowPresentationData
{
    /**
     * @param  Collection<int, Team>  $availableTeams
     * @return array{
     *     hackatonGalleryImages: Collection<int, mixed>,
     *     fieldTypeLabels: array<string, string>,
     *     teamsCount: int,
     *     participantsCount: int,
     *     myApplicationsByTeam: Collection<int|string, mixed>,
     *     teamsWithoutApplication: Collection<int, Team>,
     *     announcementTemplates: array<string, string>,
     *     modals: array<string, string>,
     *     issuedCertificatesByUser: Collection<int|string, mixed>,
     *     nextStepTitle: string,
     *     nextStepHint: string,
     *     seoDescription: string,
     *     heroImage: string|null
     * }
     */
    public function build(Hackaton $hackaton, Collection $availableTeams, bool $isOrganizer, bool $isAssignedJudge): array
    {
        $hackatonGalleryImages = $this->resolveGalleryImages($hackaton);
        $teamsCount = $hackaton->teams->count();
        $participantsCount = $hackaton->teams->sum(fn ($team) => $team->roles->whereNotNull('user_id')->count());

        $myTeamIds = auth()->check() ? $availableTeams->pluck('id') : collect();
        $myApplicationsByTeam = auth()->check()
            ? $hackaton->applications->whereIn('team_id', $myTeamIds)->keyBy('team_id')
            : collect();
        $teamsWithoutApplication = auth()->check()
            ? $availableTeams->reject(fn (Team $team) => $myApplicationsByTeam->has($team->id))->values()
            : collect();

        return [
            'hackatonGalleryImages' => $hackatonGalleryImages,
            'fieldTypeLabels' => [
                'text' => 'Короткий текст',
                'url' => 'Ссылка',
                'textarea' => 'Большой текст',
                'file' => 'Файл',
            ],
            'teamsCount' => $teamsCount,
            'participantsCount' => $participantsCount,
            'myApplicationsByTeam' => $myApplicationsByTeam,
            'teamsWithoutApplication' => $teamsWithoutApplication,
            'announcementTemplates' => [
                'start' => 'Старт хакатона',
                'deadline' => 'Напоминание о дедлайне',
                'results' => 'Публикация результатов',
            ],
            'modals' => [
                'announcement_create' => 'organizer-announcement-create-modal',
                'case_create' => 'organizer-case-create-modal',
                'judge_invite' => 'organizer-judge-invite-modal',
                'judge_assign' => 'organizer-judge-assign-modal',
                'certificate_upload' => 'organizer-certificate-upload-modal',
            ],
            'issuedCertificatesByUser' => $hackaton->certificates->groupBy('user_id'),
            'nextStepTitle' => $this->resolveNextStepTitle(
                $availableTeams,
                $teamsWithoutApplication,
                $myApplicationsByTeam,
                $isOrganizer,
                $isAssignedJudge,
            ),
            'nextStepHint' => $this->resolveNextStepHint(
                $availableTeams,
                $teamsWithoutApplication,
                $myApplicationsByTeam,
                $isOrganizer,
                $isAssignedJudge,
            ),
            'seoDescription' => $this->buildSeoDescription($hackaton),
            'heroImage' => $this->resolveHeroImage($hackaton, $hackatonGalleryImages),
        ];
    }

    /**
     * @return Collection<int, mixed>
     */
    private function resolveGalleryImages(Hackaton $hackaton): Collection
    {
        $images = $hackaton->images;
        if ($images->isNotEmpty() || ! filled($hackaton->image_url)) {
            return $images;
        }

        return collect([(object) [
            'path' => $hackaton->image_url,
            'alt' => $hackaton->title,
        ]]);
    }

    private function buildSeoDescription(Hackaton $hackaton): string
    {
        $plainDescription = strip_tags(SafeMarkdown::toHtml($hackaton->description ?? ''));
        $plainDescription = preg_replace('/\s+/u', ' ', $plainDescription) ?? '';

        return trim(mb_substr(
            $plainDescription !== '' ? $plainDescription : 'Онлайн и офлайн хакатон на платформе «Хакатонщик».',
            0,
            180,
            'UTF-8'
        ));
    }

    /**
     * @param  Collection<int, mixed>  $hackatonGalleryImages
     */
    private function resolveHeroImage(Hackaton $hackaton, Collection $hackatonGalleryImages): ?string
    {
        if ($hackatonGalleryImages->isNotEmpty()) {
            $first = $hackatonGalleryImages->first();

            return isset($first->path)
                ? (str_starts_with((string) $first->path, 'http') ? $first->path : asset('storage/'.$first->path))
                : null;
        }

        if (! filled($hackaton->image_url)) {
            return null;
        }

        return str_starts_with((string) $hackaton->image_url, 'http')
            ? $hackaton->image_url
            : asset('storage/'.$hackaton->image_url);
    }

    /**
     * @param  Collection<int, Team>  $availableTeams
     * @param  Collection<int, Team>  $teamsWithoutApplication
     * @param  Collection<int|string, mixed>  $myApplicationsByTeam
     */
    private function resolveNextStepTitle(
        Collection $availableTeams,
        Collection $teamsWithoutApplication,
        Collection $myApplicationsByTeam,
        bool $isOrganizer,
        bool $isAssignedJudge,
    ): string {
        if (! auth()->check()) {
            return 'Авторизуйтесь';
        }

        if ($isOrganizer) {
            return 'Управляйте хакатоном';
        }

        if ($isAssignedJudge) {
            return 'Оценивайте решения';
        }

        if ($availableTeams->isEmpty()) {
            return 'Создайте команду';
        }

        if ($teamsWithoutApplication->isNotEmpty()) {
            return 'Подайте заявку команды';
        }

        if ($myApplicationsByTeam->where('status', ApplicationStatus::ACCEPTED)->isNotEmpty()) {
            return 'Отправьте решение кейса';
        }

        return 'Ожидайте модерацию';
    }

    /**
     * @param  Collection<int, Team>  $availableTeams
     * @param  Collection<int, Team>  $teamsWithoutApplication
     * @param  Collection<int|string, mixed>  $myApplicationsByTeam
     */
    private function resolveNextStepHint(
        Collection $availableTeams,
        Collection $teamsWithoutApplication,
        Collection $myApplicationsByTeam,
        bool $isOrganizer,
        bool $isAssignedJudge,
    ): string {
        if (! auth()->check()) {
            return 'Войдите в аккаунт, чтобы подавать заявки и отправлять решения кейсов.';
        }

        if ($isOrganizer) {
            return 'Публикуйте анонсы и кейсы, а затем рассматривайте заявки команд.';
        }

        if ($isAssignedJudge) {
            return 'Вы назначены судьей: используйте блок кейсов для выставления оценок и комментариев.';
        }

        if ($availableTeams->isEmpty()) {
            return 'Без команды нельзя подать заявку на участие в хакатоне.';
        }

        if ($teamsWithoutApplication->isNotEmpty()) {
            return 'Выберите команду и отправьте заявку на участие прямо на этой странице.';
        }

        if ($myApplicationsByTeam->where('status', ApplicationStatus::ACCEPTED)->isNotEmpty()) {
            return 'Команда допущена: перейдите к блоку кейсов и отправьте ответы.';
        }

        return 'Заявка уже отправлена. Следите за обновлением статуса ниже.';
    }
}
