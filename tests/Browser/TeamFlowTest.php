<?php

namespace Tests\Browser;

use App\Enums\HackatonStatus;
use App\Models\Hackaton;
use App\Models\Team;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\Browser\Concerns\InteractsWithHackatonShow;
use Tests\DuskTestCase;

class TeamFlowTest extends DuskTestCase
{
    use InteractsWithHackatonShow;

    public function test_team_owner_can_apply_to_hackaton_from_show_page(): void
    {
        $user = User::factory()->create();
        $hackaton = Hackaton::factory()->create([
            'title' => 'Dusk Hackathon',
            'is_public' => true,
            'status' => HackatonStatus::REGISTRATION_OPEN,
        ]);
        $team = Team::factory()->for($user)->create([
            'title' => 'Dusk Team',
            'hackaton_id' => $hackaton->id,
        ]);

        $this->browse(function (Browser $browser) use ($user, $hackaton, $team): void {
            $browser->loginAs($user);
            $this->visitHackatonShowAndSubmitApplication($browser, $hackaton, $team->id);
            $browser->waitForText('Ваши заявки', 30)
                ->assertSee('Dusk Team')
                ->assertSee('На рассмотрении');
        });
    }
}
