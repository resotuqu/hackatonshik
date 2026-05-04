<?php

declare(strict_types=1);

use App\Models\User;

test('guest can view hackatons index', function () {
    $this->get(route('hackatons.index'))->assertOk();
});

test('authenticated user without verified email is redirected from hackatons', function () {
    $user = User::factory()->unverified()->create([
        'phone_verified_at' => null,
    ]);

    $this->actingAs($user)
        ->get(route('hackatons.index'))
        ->assertRedirect(route('verification.notice'));
});

test('authenticated user with verified email but unverified phone is redirected from hackatons', function () {
    $user = User::factory()->withoutPhoneVerification()->create();

    $this->actingAs($user)
        ->get(route('hackatons.index'))
        ->assertRedirect(route('phone.verify.notice'));
});

test('authenticated user without verified email can access email verification notice', function () {
    $user = User::factory()->unverified()->create();

    $this->actingAs($user)
        ->get(route('verification.notice'))
        ->assertOk();
});
