<?php

declare(strict_types=1);

namespace App\Actions\Hackaton;

use Illuminate\Support\Facades\Cache;

final class BumpHackatonCatalogCacheVersion
{
    public function handle(): void
    {
        $key = 'api:v1:catalog:hackatons:version';

        if (app()->isProduction() && Cache::supportsTags()) {
            $store = Cache::tags(['catalog', 'catalog:hackatons']);
            $store->put($key, ((int) $store->get($key, 0)) + 1);

            return;
        }

        Cache::put($key, ((int) Cache::get($key, 0)) + 1);
    }
}
