<?php

declare(strict_types=1);

use App\Actions\Hackaton\BumpHackatonCatalogCacheVersion;
use Illuminate\Support\Facades\Cache;

test('bump hackaton catalog cache version increments stored value', function () {
    Cache::put('api:v1:catalog:hackatons:version', 1);

    app(BumpHackatonCatalogCacheVersion::class)->handle();

    expect(Cache::get('api:v1:catalog:hackatons:version'))->toBe(2);
});
