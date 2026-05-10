<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| Feature and Unit tests use the default Laravel TestCase and refresh the
| database. Browser E2E tests live under tests/Browser as PHPUnit + Dusk
| classes; they are run via `php artisan dusk`, not Pest.
|
*/

pest()->extend(TestCase::class)->in('Feature', 'Unit');

pest()->use(RefreshDatabase::class)->in('Feature', 'Unit');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});
