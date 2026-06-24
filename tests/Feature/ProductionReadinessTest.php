<?php

declare(strict_types=1);

use App\Http\Middleware\SecurityHeaders;
use App\Models\Hackaton;
use App\Models\HackatonDocument;
use App\Models\Team;
use App\Models\User;
use App\Support\SafeMarkdown;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

uses(RefreshDatabase::class);

test('guest cannot view non-public hackaton', function () {
    $organizer = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create(['is_public' => false]);

    $this->get(route('hackatons.show', $hackaton))->assertForbidden();
});

test('guest cannot view non-public team', function () {
    $owner = User::factory()->create();
    $team = Team::factory()->for($owner)->create(['is_public' => false]);

    $this->get(route('teams.show', $team))->assertForbidden();
});

test('public catalog caps per_page at 60', function () {
    $response = $this->getJson('/api/v1/hackatons?per_page=500');

    $response->assertOk();
    expect($response->json('meta.per_page'))->toBe(60);
});

test('public catalog profiles omit internal user id', function () {
    User::factory()->create([
        'is_profile_public' => true,
        'nickname' => 'public_catalog_nick',
        'fio' => 'Иван Иванов',
    ]);

    $response = $this->getJson('/api/v1/profiles');

    $response->assertOk();
    $first = $response->json('data.0');
    expect($first)->toHaveKeys(['nickname', 'display_name', 'description']);
    expect($first)->not->toHaveKey('id');
    expect($first)->not->toHaveKey('role');
});

test('safe markdown strips raw html', function () {
    $html = SafeMarkdown::toHtml('Hello <script>alert(1)</script> **world**');

    expect($html)->not->toContain('<script>');
    expect($html)->toContain('world');
});

test('hackaton public page does not echo raw script from description markdown', function () {
    $organizer = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create([
        'is_public' => true,
        'description' => 'Intro **bold** <script>hackatonDescXssProbeZ9q</script> tail',
    ]);

    $response = $this->get(route('hackatons.show', $hackaton));

    $response->assertSuccessful();
    $response->assertDontSee('<script>hackatonDescXssProbeZ9q', false);
    $response->assertSee('<strong>bold</strong>', false);
});

test('hackaton documents tab renders description markdown as html', function () {
    $organizer = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create(['is_public' => true]);
    HackatonDocument::factory()->create([
        'hackaton_id' => $hackaton->id,
        'name' => 'DocMarkdownProbeNameK7m',
        'description' => 'Описание **жирный** фрагмент',
    ]);

    $response = $this->get(route('hackatons.show', $hackaton));

    $response->assertSuccessful();
    $response->assertSee('DocMarkdownProbeNameK7m');
    $response->assertSee('<strong>жирный</strong>', false);
    $response->assertDontSee('**жирный**', false);
});

test('application creation routes are explicitly rate limited', function () {
    $routes = app('router')->getRoutes();

    $teamApplicationRoute = $routes->getByName('team.applications.store');
    $hackatonApplicationRoute = $routes->getByName('hackaton.applications.store');
    $caseCreationRoute = $routes->getByName('hackatons.cases.store');

    expect($teamApplicationRoute?->gatherMiddleware())->toContain('throttle:applications');
    expect($hackatonApplicationRoute?->gatherMiddleware())->toContain('throttle:applications');
    expect($caseCreationRoute?->gatherMiddleware())->toContain('throttle:creations');
});

test('security headers are attached to public pages', function () {
    $middleware = new SecurityHeaders;
    $request = Request::create('/fake', 'GET');
    $response = $middleware->handle($request, fn (): Response => response('ok'));

    expect($response->headers->get('X-Frame-Options'))->toBe('SAMEORIGIN')
        ->and($response->headers->get('X-Content-Type-Options'))->toBe('nosniff')
        ->and($response->headers->get('Referrer-Policy'))->toBe('strict-origin-when-cross-origin')
        ->and($response->headers->has('Cross-Origin-Opener-Policy'))->toBeFalse()
        ->and($response->headers->get('Cross-Origin-Resource-Policy'))->toBe('same-site')
        ->and($response->headers->has('Cross-Origin-Embedder-Policy'))->toBeFalse();
});

test('cross origin opener policy is set on https requests', function () {
    $middleware = new SecurityHeaders;
    $request = Request::create('https://example.com/fake', 'GET');
    $response = $middleware->handle($request, fn (): Response => response('ok'));

    expect($response->headers->get('Cross-Origin-Opener-Policy'))->toBe('same-origin-allow-popups');
});

test('content security policy blocks object embedding and frames', function () {
    $middleware = new SecurityHeaders;
    $request = Request::create('/fake', 'GET');
    $response = $middleware->handle($request, fn (): Response => response('ok'));
    $csp = (string) $response->headers->get('Content-Security-Policy');

    expect($csp)->toContain("object-src 'none'")
        ->and($csp)->toContain("frame-ancestors 'none'")
        ->and($csp)->toContain("img-src 'self' data: blob: https:")
        ->and($csp)->toContain('https://autofill.yandex.ru')
        ->and($csp)->toContain('https://yastatic.net')
        ->and($csp)->toContain("font-src 'self' data: https://yastatic.net");
});

test('export routes are rate limited', function () {
    $routes = app('router')->getRoutes();

    expect($routes->getByName('hackatons.export.teams')?->gatherMiddleware())->toContain('throttle:exports')
        ->and($routes->getByName('profile.export')?->gatherMiddleware())->toContain('throttle:exports');
});

test('notification mark routes are rate limited', function () {
    $routes = app('router')->getRoutes();

    expect($routes->getByName('notifications.read')?->gatherMiddleware())->toContain('throttle:notifications')
        ->and($routes->getByName('notifications.read-all')?->gatherMiddleware())->toContain('throttle:notifications');
});

test('oauth routes use login throttle', function () {
    $routes = app('router')->getRoutes();

    expect($routes->getByName('auth.yandex.redirect')?->gatherMiddleware())->toContain('throttle:login')
        ->and($routes->getByName('auth.vk.callback')?->gatherMiddleware())->toContain('throttle:login');
});

test('external links in team chat include noopener noreferrer', function () {
    $owner = User::factory()->create();
    $team = Team::factory()->for($owner)->create();

    $this->actingAs($owner)
        ->get(route('teams.show', $team))
        ->assertOk()
        ->assertSee('rel="noopener noreferrer"', false);
});
