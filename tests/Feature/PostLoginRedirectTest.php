<?php

declare(strict_types=1);

use App\Livewire\Pages\Auth\Login;
use App\Models\User;
use App\Support\PostLoginRedirect;
use Livewire\Livewire;

test('post login redirect sends participants to home', function () {
    $participant = User::factory()->create(['role' => 'user']);

    expect(PostLoginRedirect::intendedUrl($participant))->toBe(route('home'));
});

test('post login redirect sends organizers to organizer dashboard', function () {
    $organizer = User::factory()->partner()->create();

    expect(PostLoginRedirect::intendedUrl($organizer))->toBe(route('organizer.dashboard'));
});

test('post login redirect sends judges to judge dashboard', function () {
    $judge = User::factory()->judge()->create();

    expect(PostLoginRedirect::intendedUrl($judge))->toBe(route('judge.dashboard'));
});

test('post login redirect sends moderators to admin dashboard', function () {
    $moderator = User::factory()->moderator()->create();

    expect(PostLoginRedirect::intendedUrl($moderator))->toBe(route('admin.dashboard'));
});

test('post login redirect sends admins to admin dashboard', function () {
    $admin = User::factory()->admin()->create();

    expect(PostLoginRedirect::intendedUrl($admin))->toBe(route('admin.dashboard'));
});

test('livewire login redirects organizer to organizer dashboard', function () {
    $organizer = User::factory()->partner()->create([
        'password' => bcrypt('password'),
    ]);

    Livewire::test(Login::class)
        ->set('email', $organizer->email)
        ->set('password', 'password')
        ->call('save')
        ->assertRedirect(route('organizer.dashboard'));
});
