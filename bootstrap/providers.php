<?php

use App\Providers\AppServiceProvider;
use App\Providers\FortifyServiceProvider;
use App\Providers\HorizonServiceProvider;
use App\Providers\TelescopeServiceProvider;
use SocialiteProviders\Manager\ServiceProvider;

$providers = [
    AppServiceProvider::class,
    FortifyServiceProvider::class,
    HorizonServiceProvider::class,
    ServiceProvider::class,
];

if (class_exists(TelescopeServiceProvider::class)) {
    $providers[] = TelescopeServiceProvider::class;
}

return $providers;
