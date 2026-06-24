<?php

declare(strict_types=1);

namespace App\Actions\Hackaton;

use App\Models\Hackaton;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

final class LoadHackatonRelationsForShowPage
{
    public function handle(Hackaton $hackaton, ?User $user, bool $isOrganizer, bool $isAssignedJudge): void
    {
        $hackaton->load([
            'user:id,nickname,fio,email',
            'documents',
            'teams:id,hackaton_id,user_id,title',
            'teams.roles:id,team_id,user_id',
            'images:id,hackaton_id,path,alt,sort_order',
            'judges:id,fio,email',
        ]);

        $hackaton->setRelation('announcements', $hackaton->announcements()
            ->with('images')
            ->when(! $isOrganizer, fn (Builder $query) => $query
                ->where('is_draft', false)
                ->whereNotNull('published_at')
                ->where('published_at', '<=', now()))
            ->get());

        $hackaton->setRelation('cases', $hackaton->cases()
            ->with(['fields', 'images', 'teams'])
            ->with([
                'submissions' => function ($query) use ($user, $isOrganizer, $isAssignedJudge): void {
                    if (! $isOrganizer && ! $isAssignedJudge) {
                        if ($user === null) {
                            $query->whereRaw('1 = 0');

                            return;
                        }

                        $myTeamIds = $user->teams()->pluck('id');
                        $query->where(function (Builder $submissionQuery) use ($user, $myTeamIds): void {
                            $submissionQuery
                                ->where('user_id', $user->id)
                                ->orWhereIn('team_id', $myTeamIds);
                        });
                    }
                },
            ])
            ->with([
                'submissions.team:id,title',
                'submissions.user:id,nickname,email,fio',
                'submissions.answers.field',
                'submissions.score',
            ])
            ->when(! $isOrganizer, fn (Builder $query) => $query
                ->where('is_published', true)
                ->where(function (Builder $scheduleQuery): void {
                    $scheduleQuery
                        ->whereNull('publish_at')
                        ->orWhere('publish_at', '<=', now());
                }))
            ->get());

        if ($isOrganizer) {
            $hackaton->setRelation('certificates', $hackaton->certificates()->with('user')->get());

            return;
        }

        $hackaton->setRelation('certificates', collect());
    }
}
