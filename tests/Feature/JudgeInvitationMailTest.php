<?php

declare(strict_types=1);

use App\Mail\JudgeInvitationMail;
use App\Models\Hackaton;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

test('organizer inviting judge sends mailable to invited email', function () {
    Mail::fake();

    $organizer = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create(['title' => 'Test Hackaton']);

    $this->actingAs($organizer)
        ->from(route('hackatons.show', $hackaton))
        ->post(route('hackatons.judges.invite', $hackaton), [
            'email' => 'judge-invite-test@gmail.com',
        ])
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    Mail::assertSent(JudgeInvitationMail::class, function (JudgeInvitationMail $mail): bool {
        return $mail->invitation->invited_email === 'judge-invite-test@gmail.com'
            && str_contains($mail->acceptUrl, '/judge-invitations/')
            && str_contains($mail->acceptUrl, $mail->invitation->token);
    });
});
