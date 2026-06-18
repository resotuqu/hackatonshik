<?php

declare(strict_types=1);

namespace Tests\Browser;

use App\Models\Hackaton;
use App\Models\Team;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ParticipantHubTest extends DuskTestCase
{
    public function test_participant_can_open_hackaton_hub_in_real_browser(): void
    {
        $user = User::factory()->create();
        $hackaton = Hackaton::factory()->create(['is_public' => true]);
        Team::factory()->for($user)->for($hackaton)->create();

        $this->browse(function (Browser $browser) use ($user, $hackaton): void {
            $browser->loginAs($user)
                ->visit(route('participant.hackatons.hub', $hackaton))
                ->waitForText('Личный кабинет участника', 30)
                ->assertSee($hackaton->title)
                ->assertSee('Страница хакатона');
        });
    }
}
