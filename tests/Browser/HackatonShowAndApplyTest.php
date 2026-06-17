<?php

declare(strict_types=1);

namespace Tests\Browser;

use App\Enums\HackatonStatus;
use App\Models\Hackaton;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class HackatonShowAndApplyTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_shows_public_hackaton_catalog_with_active_hackatons_in_real_browser(): void
    {
        $organizer = User::factory()->partner()->create();
        Hackaton::factory()->for($organizer)->create([
            'is_public' => true,
            'status' => HackatonStatus::REGISTRATION_OPEN,
            'title' => 'BrowserCatalogHackUnique',
        ]);

        $this->browse(function (Browser $browser): void {
            $browser->visit('/hackatons')
                ->waitForText('Хакатоны', 30)
                ->assertSee('Хакатоны')
                ->assertSee('BrowserCatalogHackUnique');
        });
    }

    public function test_renders_public_hackaton_show_page_in_real_browser(): void
    {
        $organizer = User::factory()->partner()->create();
        $hackaton = Hackaton::factory()->for($organizer)->create([
            'is_public' => true,
            'status' => HackatonStatus::REGISTRATION_OPEN,
            'title' => 'BrowserShowHackUnique',
        ]);

        $this->browse(function (Browser $browser) use ($hackaton): void {
            $browser->visit('/hackatons/'.$hackaton->id)
                ->waitForText('BrowserShowHackUnique', 30)
                ->assertSee('BrowserShowHackUnique');
        });
    }
}
