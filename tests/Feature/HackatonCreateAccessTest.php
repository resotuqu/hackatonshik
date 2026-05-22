<?php

declare(strict_types=1);

use App\Models\User;

use function Pest\Laravel\actingAs;

test('partner can open hackaton create page', function () {
    $partner = User::factory()->partner()->create();

    actingAs($partner)
        ->get(route('hackatons.create'))
        ->assertOk();
});

test('regular user cannot open hackaton create page', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(route('hackatons.create'))
        ->assertForbidden();
});
