<?php

declare(strict_types=1);

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class TeamCreateAndApplyTest extends DuskTestCase
{
    public function test_guest_can_browse_public_team_catalog(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->visit('/teams')
                ->waitForText('Открытые команды', 30)
                ->assertSee('Открытые команды');
        });
    }

    public function test_lets_a_verified_user_open_the_create_team_page_in_real_browser(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user): void {
            $browser->loginAs($user)
                ->visit('/teams/create')
                ->assertSee('Создание команды');
        });
    }
}
