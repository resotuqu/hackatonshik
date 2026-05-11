<?php

declare(strict_types=1);

namespace App\Actions\Hackaton;

use App\Models\Hackaton;
use App\Models\User;
use Illuminate\Support\Collection;

final class ResolveJudgeManagementDataset
{
    /**
     * @return array{judgeCandidates: Collection<int, User>, pendingJudgeInvitations: Collection<int, mixed>}
     */
    public function handle(Hackaton $hackaton, bool $isOrganizer): array
    {
        if (! $isOrganizer) {
            return [
                'judgeCandidates' => collect(),
                'pendingJudgeInvitations' => collect(),
            ];
        }

        return [
            'judgeCandidates' => User::query()
                ->where('role', 'judge')
                ->orderBy('fio')
                ->get(['id', 'fio', 'email', 'nickname']),
            'pendingJudgeInvitations' => $hackaton->judgeInvitations()
                ->where('status', 'pending')
                ->latest()
                ->get(),
        ];
    }
}
