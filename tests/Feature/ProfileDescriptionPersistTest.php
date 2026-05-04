<?php

use App\Models\User;
use Livewire\Livewire;

test('profile normalizes description line endings and persists to database', function () {
    $user = User::factory()->create(['description' => null]);

    Livewire::actingAs($user)
        ->test('pages::profile.index')
        ->set('description', "A\r\nB")
        ->assertSet('description', "A\nB");

    expect($user->fresh()->description)->toBe("A\nB");
});

test('profile stores whitespace-only description as null', function () {
    $user = User::factory()->create(['description' => 'old']);

    Livewire::actingAs($user)
        ->test('pages::profile.index')
        ->set('description', "  \n\t  ")
        ->assertSet('description', null);

    expect($user->fresh()->description)->toBeNull();
});
