<?php

declare(strict_types=1);

namespace App\Events;

final class TeamApplicationChanged
{
    public function __construct(
        public readonly int $teamId,
        public readonly ?int $applicantId = null,
        public readonly ?int $captainId = null,
    ) {
    }
}

