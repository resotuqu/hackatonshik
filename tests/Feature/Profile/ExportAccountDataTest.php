<?php

use App\Models\User;

test('authenticated user can download account data pdf', function () {
    $user = User::factory()->create([
        'pd_consent_accepted_at' => now(),
    ]);

    $response = $this->actingAs($user)->get(route('profile.export'));

    $response->assertOk()
        ->assertHeader('Content-Type', 'application/pdf');

    expect($response->headers->get('Content-Disposition'))
        ->toContain('.pdf');
});

test('guest cannot access account data export', function () {
    $this->get(route('profile.export'))
        ->assertRedirect(route('login'));
});
