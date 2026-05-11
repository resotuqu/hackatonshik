<?php

declare(strict_types=1);

namespace App\Actions\Hackaton;

use App\Models\Hackaton;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

final class ResolveHackatonShowAudience
{
    /**
     * @return array{isOrganizer: bool, isAssignedJudge: bool, needsOrganizationInsights: bool}
     */
    public function handle(Hackaton $hackaton, ?User $user): array
    {
        $isOrganizer = $user !== null && Gate::forUser($user)->allows('update', $hackaton);
        $isAssignedJudge = $user !== null && $hackaton->isJudge($user);

        return [
            'isOrganizer' => $isOrganizer,
            'isAssignedJudge' => $isAssignedJudge,
            'needsOrganizationInsights' => $isOrganizer || $isAssignedJudge,
        ];
    }
}
