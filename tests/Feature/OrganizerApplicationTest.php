<?php

declare(strict_types=1);

use App\Enums\OrganizerApplicationStatus;
use App\Enums\OrganizerEntityType;
use App\Enums\UserRole;
use App\Models\OrganizerApplication;
use App\Models\User;

test('approve organizer application assigns partner role', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create();
    $application = OrganizerApplication::factory()->for($user)->pending()->create();

    $application->approve($admin);

    expect($application->fresh()->status)->toBe(OrganizerApplicationStatus::Approved)
        ->and($application->reviewed_by)->toBe($admin->id)
        ->and($user->fresh()->role)->toBe(UserRole::PARTNER)
        ->and($user->fresh()->isOrganizer())->toBeTrue();
});

test('approve organizer application updates status and role atomically', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create();
    $application = OrganizerApplication::factory()->for($user)->pending()->create();

    $application->approve($admin);

    $application->refresh();
    $user->refresh();

    expect($application->status)->toBe(OrganizerApplicationStatus::Approved)
        ->and($user->role)->toBe(UserRole::PARTNER);
});

test('reject organizer application keeps user role', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create();
    $application = OrganizerApplication::factory()->for($user)->pending()->create();

    $application->reject($admin, 'Недостаточно информации о компании');

    expect($application->fresh()->status)->toBe(OrganizerApplicationStatus::Rejected)
        ->and($application->admin_note)->toBe('Недостаточно информации о компании')
        ->and($user->fresh()->role)->toBe(UserRole::USER);
});

test('resubmit rejected application resets status to pending', function () {
    $user = User::factory()->create();
    $application = OrganizerApplication::factory()->for($user)->rejected()->create([
        'entity_type' => OrganizerEntityType::Individual,
        'note' => 'Старое примечание достаточной длины для теста.',
    ]);

    $application->resubmit(
        OrganizerEntityType::Company,
        'ООО Новая Компания',
        'Обновлённое примечание с подробным описанием деятельности.',
    );

    $application->refresh();

    expect($application->status)->toBe(OrganizerApplicationStatus::Pending)
        ->and($application->entity_type)->toBe(OrganizerEntityType::Company)
        ->and($application->company_name)->toBe('ООО Новая Компания')
        ->and($application->admin_note)->toBeNull()
        ->and($application->reviewed_by)->toBeNull();
});

test('admin can access organizer applications filament resource', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('filament.admin.resources.organizer-applications.index'))
        ->assertOk();
});

test('moderator can access organizer applications filament resource', function () {
    $moderator = User::factory()->moderator()->create();

    $this->actingAs($moderator)
        ->get(route('filament.admin.resources.organizer-applications.index'))
        ->assertOk();
});

test('participant cannot access organizer applications filament resource', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('filament.admin.resources.organizer-applications.index'))
        ->assertForbidden();
});
