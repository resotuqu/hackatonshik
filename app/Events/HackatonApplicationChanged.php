<?php

declare(strict_types=1);

namespace App\Events;

final class HackatonApplicationChanged
{
    public function __construct(
        public readonly int $teamId,
        public readonly ?int $hackatonId = null,
        public readonly ?int $organizerId = null,
        public readonly bool $invalidateHomeFeatured = false,
    ) {
    }
}

