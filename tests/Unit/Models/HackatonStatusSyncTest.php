<?php

declare(strict_types=1);

use App\Enums\HackatonStatus;
use App\Models\Hackaton;
use App\Models\HackatonCase;
use App\Models\User;

test('archived hackaton stays archived even after end_at', function () {
    $organizer = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create([
        'is_public' => true,
        'status' => HackatonStatus::ARCHIVED,
        'start_at' => now()->subDays(2),
        'end_at' => now()->subDay(),
    ]);

    expect($hackaton->syncStatusByTimeline())->toBeFalse();
    expect($hackaton->fresh()->status)->toBe(HackatonStatus::ARCHIVED);
});

test('non public hackaton becomes draft', function () {
    $organizer = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create([
        'is_public' => false,
        'status' => HackatonStatus::REGISTRATION_OPEN,
    ]);

    expect($hackaton->syncStatusByTimeline())->toBeTrue();
    expect($hackaton->fresh()->status)->toBe(HackatonStatus::DRAFT);
});

test('public hackaton before start becomes registration_open while registration is active', function () {
    $organizer = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create([
        'is_public' => true,
        'status' => HackatonStatus::PUBLISHED,
        'registration_deadline_at' => now()->addDays(2),
        'start_at' => now()->addDays(4),
        'end_at' => now()->addDays(6),
    ]);

    expect($hackaton->syncStatusByTimeline())->toBeTrue();
    expect($hackaton->fresh()->status)->toBe(HackatonStatus::REGISTRATION_OPEN);
});

test('public hackaton before start becomes registration_closed after deadline', function () {
    $organizer = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create([
        'is_public' => true,
        'status' => HackatonStatus::DRAFT,
        'registration_deadline_at' => now()->subDay(),
        'start_at' => now()->addDays(6),
        'end_at' => now()->addDays(8),
    ]);

    expect($hackaton->syncStatusByTimeline())->toBeTrue();
    expect($hackaton->fresh()->status)->toBe(HackatonStatus::REGISTRATION_CLOSED);
});

test('public hackaton before start becomes waiting_start near launch', function () {
    $organizer = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create([
        'is_public' => true,
        'status' => HackatonStatus::REGISTRATION_CLOSED,
        'registration_deadline_at' => now()->subDays(3),
        'start_at' => now()->addDay(),
        'end_at' => now()->addDays(3),
    ]);

    expect($hackaton->syncStatusByTimeline())->toBeTrue();
    expect($hackaton->fresh()->status)->toBe(HackatonStatus::WAITING_START);
});

test('public hackaton before start becomes cases_announced when published cases exist', function () {
    $organizer = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create([
        'is_public' => true,
        'status' => HackatonStatus::REGISTRATION_CLOSED,
        'registration_deadline_at' => now()->subDays(2),
        'start_at' => now()->addDays(5),
        'end_at' => now()->addDays(7),
    ]);

    HackatonCase::factory()->create([
        'hackaton_id' => $hackaton->id,
        'is_published' => true,
    ]);

    expect($hackaton->syncStatusByTimeline())->toBeTrue();
    expect($hackaton->fresh()->status)->toBe(HackatonStatus::CASES_ANNOUNCED);
});

test('public hackaton in window becomes in_progress', function () {
    $organizer = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create([
        'is_public' => true,
        'status' => HackatonStatus::REGISTRATION_OPEN,
        'start_at' => now()->subHour(),
        'end_at' => now()->addHour(),
    ]);

    expect($hackaton->syncStatusByTimeline())->toBeTrue();
    expect($hackaton->fresh()->status)->toBe(HackatonStatus::IN_PROGRESS);
});

test('public hackaton after end becomes finished', function () {
    $organizer = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create([
        'is_public' => true,
        'status' => HackatonStatus::IN_PROGRESS,
        'start_at' => now()->subDays(2),
        'end_at' => now()->subDay(),
    ]);

    expect($hackaton->syncStatusByTimeline())->toBeTrue();
    expect($hackaton->fresh()->status)->toBe(HackatonStatus::FINISHED);
});

test('public hackaton after end keeps judging when explicitly judging', function () {
    $organizer = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create([
        'is_public' => true,
        'status' => HackatonStatus::JUDGING,
        'start_at' => now()->subDays(2),
        'end_at' => now()->subDay(),
    ]);

    expect($hackaton->syncStatusByTimeline())->toBeFalse();
    expect($hackaton->fresh()->status)->toBe(HackatonStatus::JUDGING);
});

test('sync returns false when status is unchanged', function () {
    $organizer = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create([
        'is_public' => true,
        'status' => HackatonStatus::REGISTRATION_OPEN,
        'registration_deadline_at' => now()->addHours(6),
        'start_at' => now()->addDay(),
        'end_at' => now()->addDays(3),
    ]);

    expect($hackaton->syncStatusByTimeline())->toBeFalse();
});
