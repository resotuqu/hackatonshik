<?php

$root = dirname(__DIR__, 2);

test('compose.yaml contains sail dev services only', function () use ($root) {
    $compose = file_get_contents($root.'/compose.yaml');

    expect($compose)
        ->toContain('laravel.test:')
        ->toContain('pgsql:')
        ->toContain('mailpit:')
        ->toContain('${REVERB_SERVER_PORT:-8080}:8080')
        ->not->toContain("\n  app:\n");
});

test('compose.prod.yaml contains production app service', function () use ($root) {
    $compose = file_get_contents($root.'/compose.prod.yaml');

    expect($compose)
        ->toContain('app:')
        ->toContain('dockerfile: Dockerfile')
        ->toContain('redis:');
});

test('env sail template configures postgres redis and mailpit', function () use ($root) {
    $env = file_get_contents($root.'/.env.sail');

    expect($env)
        ->toContain('DB_CONNECTION=pgsql')
        ->toContain('DB_HOST=pgsql')
        ->toContain('REDIS_HOST=redis')
        ->toContain('MAIL_HOST=mailpit')
        ->toContain('APP_URL=http://localhost')
        ->toContain('FORCE_HTTPS=false')
        ->toContain('REVERB_HOST=localhost')
        ->toContain('REVERB_SCHEME=http');
});
