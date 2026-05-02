<?php

use App\Models\User;
use Illuminate\Support\Facades\URL;

test('signed email verification URL is accepted by relative signature middleware', function () {
    $user = User::factory()->unverified()->create();

    $signedRelative = URL::temporarySignedRoute(
        'verification.verify',
        now()->addHour(),
        [
            'id' => $user->getKey(),
            'hash' => sha1($user->getEmailForVerification()),
        ],
        false,
    );

    $response = $this->actingAs($user)->get($signedRelative);

    expect($response->status())->not->toBe(403);
});
