<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Livewire\Pages\Home\Index as HomeIndex;
use App\Models\OrganizerApplication;
use App\Models\User;
use Livewire\Livewire;

test('organizer dashboard does not render role tabs for single-role users', function () {
    $user = User::factory()->create();
    $user->forceFill(['role' => UserRole::PARTNER])->save();

    OrganizerApplication::factory()->for($user)->approved()->create();

    Livewire::actingAs($user->fresh())
        ->test(HomeIndex::class)
        ->assertDontSee('data-test="home-dashboard-role-tabs"', false)
        ->assertSet('activeDashboardRole', 'organizer');
});

test('home index exposes multiple dashboard role tabs when user matches several roles', function () {
    $user = Mockery::mock(User::class)->makePartial();
    $user->shouldReceive('isParticipant')->andReturn(true);
    $user->shouldReceive('isOrganizer')->andReturn(true);
    $user->shouldReceive('isJudge')->andReturn(false);
    $user->shouldReceive('isModerator')->andReturn(false);
    $user->shouldReceive('isAdmin')->andReturn(false);

    $component = new HomeIndex;
    $roles = $component->availableDashboardRoles($user);

    expect($roles)->toHaveKeys(['participant', 'organizer'])
        ->and(count($roles))->toBe(2);
});

test('organizer application policy allows only admin to approve', function () {
    $admin = User::factory()->admin()->create();
    $participant = User::factory()->create();
    $application = OrganizerApplication::factory()->pending()->create();

    expect($admin->can('approve', $application))->toBeTrue()
        ->and($participant->can('approve', $application))->toBeFalse();
});
