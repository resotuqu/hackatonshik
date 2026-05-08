<?php

declare(strict_types=1);

use App\Models\User;

use function Pest\Laravel\get;

test('public profile is accessible to guest by nickname', function () {
    $user = User::factory()->create([
        'is_profile_public' => true,
        'nickname' => 'public-profile-guest-test',
        'fio' => 'Public Profile Guest Test',
    ]);

    get(route('profile.public.show', ['user' => $user->nickname]))
        ->assertOk()
        ->assertSee('Public Profile Guest Test', false);
});

test('private profile returns 404', function () {
    $user = User::factory()->create([
        'is_profile_public' => false,
        'nickname' => 'hidden-profile-guest-test',
    ]);

    get(route('profile.public.show', ['user' => $user->nickname]))
        ->assertNotFound();
});

test('public profile renders without errors when relations are empty', function () {
    $user = User::factory()->create([
        'is_profile_public' => true,
        'nickname' => 'empty-relations-user',
        'fio' => 'Empty Relations User',
    ]);

    $response = get(route('profile.public.show', ['user' => $user->nickname]));

    $response->assertOk();
    $response->assertSee('Empty Relations User', false);
});
