<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ExampleTest extends DuskTestCase
{
    /**
     * Smoke: главная страница отдаёт ожидаемый герой-копирайт (не дефолтный Laravel welcome).
     */
    public function test_home_page_renders_product_hero(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->visit('/')
                ->assertSee('Найдите команду');
        });
    }
}
