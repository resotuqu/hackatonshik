<?php

declare(strict_types=1);

use App\Models\Hackaton;
use App\Models\User;

use function Pest\Laravel\actingAs;

test('organizer dashboard is reachable for partner', function () {
    $organizer = User::factory()->partner()->create();
    Hackaton::factory()->for($organizer)->create(['is_public' => true]);

    actingAs($organizer)
        ->get(route('organizer.dashboard'))
        ->assertOk()
        ->assertSeeLivewire('organizer.dashboard');
});

test('profile organizer route serves same dashboard', function () {
    $organizer = User::factory()->partner()->create();

    actingAs($organizer)
        ->get(route('profile.organizer'))
        ->assertOk()
        ->assertSeeLivewire('organizer.dashboard');
});
