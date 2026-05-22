<?php

declare(strict_types=1);

use App\Livewire\Organizer\Dashboard;
use App\Models\Hackaton;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

test('organizer can delete own hackaton via dashboard', function () {
    $organizer = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create();

    actingAs($organizer);

    Livewire::test(Dashboard::class)
        ->call('showDeleteHackatonModal', $hackaton->id)
        ->call('deleteHackaton')
        ->assertHasNoErrors();

    expect(Hackaton::query()->find($hackaton->id))->toBeNull();
});

test('organizer cannot delete another users hackaton via dashboard', function () {
    $owner = User::factory()->partner()->create();
    $attacker = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->for($owner)->create();

    actingAs($attacker);

    Livewire::test(Dashboard::class)
        ->call('showDeleteHackatonModal', $hackaton->id)
        ->call('deleteHackaton')
        ->assertForbidden();

    expect(Hackaton::query()->find($hackaton->id))->not->toBeNull();
});

test('non partner cannot access organizer dashboard', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(route('organizer.dashboard'))
        ->assertForbidden();
});

test('profile hackatons route redirects to organizer dashboard', function () {
    $organizer = User::factory()->partner()->create();

    actingAs($organizer)
        ->get(route('profile.hackatons'))
        ->assertRedirect(route('organizer.dashboard'));
});
