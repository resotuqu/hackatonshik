<?php

declare(strict_types=1);

namespace App\Livewire\Concerns;

use Illuminate\Support\Facades\Gate;

trait AuthorizesAdminAccess
{
    protected function authorizeAdminAccess(): void
    {
        Gate::authorize('access-admin');
    }
}
