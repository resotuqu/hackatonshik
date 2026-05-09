<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class RegistrationFlowTest extends DuskTestCase
{
    /**
     * A basic browser test example.
     */
    public function test_renders_registration_page(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                ->assertSee('Регистрация');
        });
    }

    public function test_renders_login_page(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->assertSee('Войти');
        });
    }
}
