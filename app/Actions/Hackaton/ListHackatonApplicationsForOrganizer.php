<?php

declare(strict_types=1);

namespace App\Actions\Hackaton;

use App\Models\Hackaton;
use App\Models\HackatonApplication;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

final class ListHackatonApplicationsForOrganizer
{
    /**
     * @return Collection<int, HackatonApplication>
     */
    public function handle(Hackaton $hackaton, string $applicationStatusFilter): Collection
    {
        return $hackaton->applications()
            ->with(['team', 'reviewer'])
            ->when(
                $applicationStatusFilter !== '',
                fn (Builder $query) => $query->where('status', $applicationStatusFilter)
            )
            ->latest()
            ->get();
    }
}
