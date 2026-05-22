<?php

declare(strict_types=1);

use App\Models\User;

it('allows partner to access organizer profile hackaton pages', function (): void {
    $partner = User::factory()->partner()->create();

    $this->actingAs($partner)
        ->get(route('profile.hackatons'))
        ->assertRedirect(route('organizer.dashboard'));

    $this->actingAs($partner)
        ->get(route('profile.hackatons.applications'))
        ->assertOk();

    $this->actingAs($partner)
        ->get(route('profile.hackatons.scoring'))
        ->assertOk();

    $this->actingAs($partner)
        ->get(route('profile.hackatons.finished'))
        ->assertOk();
});

it('forbids non-partner from organizer-only pages', function (): void {
    $user = User::factory()->create(['role' => 'user']);

    $this->actingAs($user)
        ->get(route('profile.hackatons.applications'))
        ->assertForbidden();

    $this->actingAs($user)
        ->get(route('profile.hackatons.scoring'))
        ->assertForbidden();

    $this->actingAs($user)
        ->get(route('profile.hackatons.finished'))
        ->assertForbidden();
});
