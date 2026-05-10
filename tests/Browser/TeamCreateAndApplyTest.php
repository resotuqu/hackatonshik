<?php

declare(strict_types=1);

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class TeamCreateAndApplyTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_lets_a_verified_user_open_the_create_team_page_in_real_browser(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user): void {
            $browser->loginAs($user)
                ->visit('/teams/create')
                ->assertSee('Создание команды');
        });
    }

    public function test_shows_public_team_catalog_for_guests(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->visit('/teams')
                ->assertSee('Каталог команд');
        });
    }
}
