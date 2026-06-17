<?php

declare(strict_types=1);

namespace Tests\Browser;

use App\Models\Hackaton;
use App\Models\JudgeInvitation;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class JudgeAcceptInvitationTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_renders_judge_invitation_acceptance_page_in_real_browser(): void
    {
        $organizer = User::factory()->partner()->create();
        $invitee = User::factory()->create([
            'email' => 'browser-judge@example.com',
        ]);
        $hackaton = Hackaton::factory()->for($organizer)->create([
            'title' => 'JudgeInviteBrowserUnique',
        ]);
        $invitation = JudgeInvitation::factory()->create([
            'hackaton_id' => $hackaton->id,
            'invited_by' => $organizer->id,
            'invited_email' => 'browser-judge@example.com',
            'status' => JudgeInvitation::STATUS_PENDING,
        ]);

        $this->browse(function (Browser $browser) use ($invitation, $invitee): void {
            $browser->loginAs($invitee)
                ->visit('/judge-invitations/'.$invitation->token)
                ->waitForText('JudgeInviteBrowserUnique', 30)
                ->assertSee('JudgeInviteBrowserUnique');
        });
    }
}
