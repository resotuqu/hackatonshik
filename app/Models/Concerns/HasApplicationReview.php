<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use App\Enums\ApplicationStatus;
use App\Models\User;

trait HasApplicationReview
{
    public function markAsAccepted(User $reviewer): void
    {
        $this->markAs(ApplicationStatus::ACCEPTED, $reviewer);
    }

    public function markAsRejected(User $reviewer): void
    {
        $this->markAs(ApplicationStatus::REJECTED, $reviewer);
    }

    private function markAs(ApplicationStatus $status, User $reviewer): void
    {
        $this->update([
            'status' => $status,
            'reviewed_at' => now(),
            'reviewed_by' => $reviewer->id,
        ]);
    }
}
