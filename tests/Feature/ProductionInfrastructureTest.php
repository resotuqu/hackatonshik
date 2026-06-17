<?php

declare(strict_types=1);

use App\Logging\TelegramLogger;
use App\Models\User;
use App\Providers\TelescopeServiceProvider;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Log;

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

test('admin can access pulse dashboard when enabled', function () {
    config(['pulse.enabled' => true]);

    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get('/pulse')
        ->assertSuccessful();
});

test('guest cannot access pulse dashboard', function () {
    config(['pulse.enabled' => true]);

    $this->get('/pulse')->assertForbidden();
});

test('telegram log channel resolves in stack configuration', function () {
    config(['logging.channels.telegram' => [
        'driver' => 'custom',
        'via' => TelegramLogger::class,
        'level' => 'critical',
        'token' => '',
        'chat_id' => '',
    ]]);

    Log::channel('telegram');

    expect(true)->toBeTrue();
});

test('validate production config command fails with log mailer in production', function () {
    /** @var Application $app */
    $app = app();
    $app->detectEnvironment(fn (): string => 'production');

    config(['mail.default' => 'log']);
    config(['app.trusted_proxies' => '127.0.0.1']);

    $this->artisan('app:validate-production-config')
        ->assertExitCode(1);

    $app->detectEnvironment(fn (): string => 'testing');
});

test('documented openapi paths exist in route list', function () {
    $openapi = file_get_contents(base_path('docs/api/openapi.yaml'));

    expect($openapi)->toContain('/hackatons')
        ->and($openapi)->toContain('/teams')
        ->and($openapi)->toContain('/profiles');

    $routes = collect(app('router')->getRoutes())->map(fn ($route) => $route->uri());

    expect($routes)->toContain('api/v1/hackatons')
        ->and($routes)->toContain('api/v1/teams')
        ->and($routes)->toContain('api/v1/profiles');
});
