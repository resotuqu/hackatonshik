<?php

declare(strict_types=1);

use App\Models\Hackaton;
use App\Models\JudgeInvitation;
use App\Models\User;

it('renders judge invitation acceptance page in real browser', function () {
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

    $this->actingAs($invitee);

    visit('/judge-invitations/'.$invitation->token)
        ->assertSee('JudgeInviteBrowserUnique')
        ->assertNoJavaScriptErrors();
});
