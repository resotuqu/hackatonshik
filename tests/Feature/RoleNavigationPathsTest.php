<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\Hackaton;
use App\Models\JudgeInvitation;
use App\Models\User;
use App\Support\ProfileNavigation;

use function Pest\Laravel\actingAs;

test('staff roles do not show hackatons profile tab', function () {
    $moderator = User::factory()->moderator()->create();
    $admin = User::factory()->admin()->create();

    expect(ProfileNavigation::showsHackatonsTab($moderator))->toBeFalse()
        ->and(ProfileNavigation::showsHackatonsTab($admin))->toBeFalse()
        ->and(ProfileNavigation::showsHackatonsTab(User::factory()->create()))->toBeTrue();
});

test('participant profile hackatons tab points to participant list not organizer route', function () {
    $participant = User::factory()->create(['role' => 'user']);

    expect(ProfileNavigation::hackatonsTabHref($participant))
        ->toBe(route('participant.hackatons'))
        ->and(ProfileNavigation::hackatonsTabLabel($participant))
        ->toBe('Мои заявки и хакатоны');
});

test('participant can open hackatons list page', function () {
    $participant = User::factory()->create(['role' => 'user']);

    actingAs($participant)
        ->get(route('participant.hackatons'))
        ->assertOk()
        ->assertSee('Мои заявки и хакатоны');
});

test('profile hackatons legacy route redirects participants to participant list', function () {
    $participant = User::factory()->create(['role' => 'user']);

    actingAs($participant)
        ->get(route('profile.hackatons'))
        ->assertRedirect(route('participant.hackatons'));
});

test('profile hackatons legacy route redirects organizers to organizer dashboard', function () {
    $organizer = User::factory()->partner()->create();

    actingAs($organizer)
        ->get(route('profile.hackatons'))
        ->assertRedirect(route('organizer.dashboard'));
});

test('organizer subpages are available under organizer namespace with legacy redirects', function () {
    $organizer = User::factory()->partner()->create();

    actingAs($organizer)
        ->get(route('profile.hackatons.applications'))
        ->assertRedirect(route('organizer.applications'));

    actingAs($organizer)
        ->get(route('organizer.applications'))
        ->assertOk();

    actingAs($organizer)
        ->get(route('profile.hackatons.scoring'))
        ->assertRedirect(route('organizer.scoring'));

    actingAs($organizer)
        ->get(route('organizer.scoring'))
        ->assertOk();

    actingAs($organizer)
        ->get(route('profile.hackatons.finished'))
        ->assertRedirect(route('organizer.finished'));

    actingAs($organizer)
        ->get(route('organizer.finished'))
        ->assertOk();
});

test('non judge cannot access judge dashboard', function () {
    $participant = User::factory()->create(['role' => 'user']);

    actingAs($participant)
        ->get(route('judge.dashboard'))
        ->assertForbidden();
});

test('judge can access judge dashboard', function () {
    $judge = User::factory()->judge()->create();

    actingAs($judge)
        ->get(route('judge.dashboard'))
        ->assertOk();
});

test('moderator profile hackatons legacy route redirects to admin dashboard', function () {
    $moderator = User::factory()->moderator()->create();

    actingAs($moderator)
        ->get(route('profile.hackatons'))
        ->assertRedirect(route('admin.dashboard'));
});

test('admin profile hackatons legacy route redirects to admin dashboard', function () {
    $admin = User::factory()->admin()->create();

    actingAs($admin)
        ->get(route('profile.hackatons'))
        ->assertRedirect(route('admin.dashboard'));
});

test('moderator profile page hides hackatons tab', function () {
    $moderator = User::factory()->moderator()->create();

    actingAs($moderator)
        ->get(route('profile'))
        ->assertOk()
        ->assertDontSee('Мои заявки и хакатоны', false)
        ->assertSee('Модератор', false);
});

test('moderator cannot accept judge invitation', function () {
    $organizer = User::factory()->partner()->create();
    $moderator = User::factory()->moderator()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create();

    $invitation = JudgeInvitation::query()->create([
        'hackaton_id' => $hackaton->id,
        'invited_email' => mb_strtolower($moderator->email),
        'invited_by' => $organizer->id,
        'token' => 'moderator-judge-token',
        'status' => JudgeInvitation::STATUS_PENDING,
    ]);

    actingAs($moderator)
        ->post(route('judges.invitations.accept.store', $invitation->token))
        ->assertForbidden();

    expect($moderator->fresh()->role)->toBe(UserRole::MODERATOR);
});
