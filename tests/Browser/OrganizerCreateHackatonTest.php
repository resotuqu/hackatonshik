<?php

declare(strict_types=1);

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class OrganizerCreateHackatonTest extends DuskTestCase
{
    public function test_organizer_can_open_create_hackaton_wizard_in_real_browser(): void
    {
        $organizer = User::factory()->partner()->create();

        $this->browse(function (Browser $browser) use ($organizer): void {
            $browser->loginAs($organizer)
                ->visit('/hackatons/create')
                ->waitForText('Создание хакатона', 30)
                ->assertSee('Создание хакатона')
                ->assertSee('Прогресс мастера');
        });
    }
}
