<?php

declare(strict_types=1);

use App\Models\User;
use App\Providers\TelescopeServiceProvider;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Application;

test('telescope provider is not registered when package is absent', function () {
    $providers = require base_path('bootstrap/providers.php');

    if (class_exists(TelescopeServiceProvider::class)) {
        expect($providers)->toContain(TelescopeServiceProvider::class);
    } else {
        expect($providers)->not->toContain(TelescopeServiceProvider::class);
    }
});

test('database seeder skips demo data outside local and testing', function () {
    /** @var Application $app */
    $app = app();
    $app->detectEnvironment(fn (): string => 'production');

    $seeder = new DatabaseSeeder;
    $seeder->setContainer($app);

    $seeder->run();

    expect(User::count())->toBe(0);

    $app->detectEnvironment(fn (): string => 'testing');
});

test('health endpoint responds successfully', function () {
    $this->get('/up')->assertOk();
});

test('judge and case join routes are rate limited', function () {
    $routes = app('router')->getRoutes();

    $joinRoute = $routes->getByName('hackatons.cases.join');
    $inviteRoute = $routes->getByName('hackatons.judges.invite');
    $assignRoute = $routes->getByName('hackatons.judges.assign');

    expect($joinRoute?->gatherMiddleware())->toContain('throttle:creations');
    expect($inviteRoute?->gatherMiddleware())->toContain('throttle:judge-management');
    expect($assignRoute?->gatherMiddleware())->toContain('throttle:judge-management');
});
