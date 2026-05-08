<?php

declare(strict_types=1);

use App\Enums\HackatonStatus;
use App\Models\Hackaton;
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

test('published hackaton before start stays published', function () {
    $organizer = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create([
        'is_public' => true,
        'status' => HackatonStatus::PUBLISHED,
        'start_at' => now()->addDay(),
        'end_at' => now()->addDays(3),
    ]);

    expect($hackaton->syncStatusByTimeline())->toBeFalse();
    expect($hackaton->fresh()->status)->toBe(HackatonStatus::PUBLISHED);
});

test('public hackaton before start becomes registration_open', function () {
    $organizer = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create([
        'is_public' => true,
        'status' => HackatonStatus::DRAFT,
        'start_at' => now()->addDay(),
        'end_at' => now()->addDays(3),
    ]);

    expect($hackaton->syncStatusByTimeline())->toBeTrue();
    expect($hackaton->fresh()->status)->toBe(HackatonStatus::REGISTRATION_OPEN);
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
        'start_at' => now()->addDay(),
        'end_at' => now()->addDays(3),
    ]);

    expect($hackaton->syncStatusByTimeline())->toBeFalse();
});
