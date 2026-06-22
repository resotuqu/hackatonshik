<?php

use App\Http\Middleware\ForceHttps;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

test('force https middleware redirects insecure requests', function () {
    config(['app.force_https' => true, 'app.url' => 'https://hackatonshik.test']);
    URL::forceRootUrl('https://hackatonshik.test');

    $middleware = new ForceHttps;
    $request = Request::create('http://hackatonshik.test/login', 'GET');
    $response = $middleware->handle($request, fn (): Response => response('ok'));

    expect($response->getStatusCode())->toBe(301)
        ->and($response->headers->get('Location'))->toBe('https://hackatonshik.test/login');
});

test('force https middleware allows secure requests', function () {
    config(['app.force_https' => true]);

    $middleware = new ForceHttps;
    $request = Request::create('https://hackatonshik.test/login', 'GET');
    $response = $middleware->handle($request, fn (): Response => response('ok', 200));

    expect($response->getStatusCode())->toBe(200)
        ->and($response->getContent())->toBe('ok');
});

test('force https middleware is disabled when configured off', function () {
    config(['app.force_https' => false]);

    $middleware = new ForceHttps;
    $request = Request::create('http://hackatonshik.test/login', 'GET');
    $response = $middleware->handle($request, fn (): Response => response('ok', 200));

    expect($response->getStatusCode())->toBe(200);
});

test('generated urls use https when force https is enabled', function () {
    config(['app.force_https' => true, 'app.url' => 'https://hackatonshik.test']);

    URL::forceScheme('https');

    expect(route('login'))->toStartWith('https://');
});
