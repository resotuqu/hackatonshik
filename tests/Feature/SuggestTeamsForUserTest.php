<?php

declare(strict_types=1);

use App\Actions\Hackaton\SuggestTeamsForUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('private profile does not get team recommendations', function () {
    $user = User::factory()->create([
        'open_to_teams' => true,
        'is_profile_public' => false,
    ]);

    $suggestions = app(SuggestTeamsForUser::class)->handle($user);

    expect($suggestions)->toHaveCount(0);
});
