<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Livewire\Livewire;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Livewire::withoutLazyLoading();

        // Symfony's Request::create() defaults to 'en-us,en;q=0.5' for Accept-Language.
        // Override to empty so SetLocale middleware falls back to config default (ru).
        $this->withServerVariables(['HTTP_ACCEPT_LANGUAGE' => '']);
    }
}
