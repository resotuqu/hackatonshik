<?php

use App\Providers\AppServiceProvider;
use App\Providers\Filament\AdminPanelProvider;
use App\Providers\FortifyServiceProvider;
use App\Providers\HorizonServiceProvider;
use App\Providers\TelescopeServiceProvider;
use SocialiteProviders\Manager\ServiceProvider;

$providers = [
    AppServiceProvider::class,
    AdminPanelProvider::class,
    FortifyServiceProvider::class,
    HorizonServiceProvider::class,
    ServiceProvider::class,
];

if (class_exists(TelescopeServiceProvider::class) && in_array(env('APP_ENV'), ['local', 'testing'], true)) {
    $providers[] = TelescopeServiceProvider::class;
}

return $providers;
