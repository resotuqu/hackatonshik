<?php

use App\Enums\HackatonStatus;
use App\Models\Hackaton;
use App\Models\HackatonCase;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;

test('sync command updates hackaton status by timeline', function () {
    $organizer = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create([
        'is_public' => true,
        'status' => HackatonStatus::DRAFT,
        'start_at' => now()->subHour(),
        'end_at' => now()->addHour(),
    ]);

    Artisan::call('hackatons:sync-statuses');

    expect($hackaton->fresh()->status)->toBe(HackatonStatus::IN_PROGRESS);
});

test('sync command sets registration_closed after registration deadline', function () {
    $organizer = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create([
        'is_public' => true,
        'status' => HackatonStatus::PUBLISHED,
        'registration_deadline_at' => now()->subDay(),
        'start_at' => now()->addDays(6),
        'end_at' => now()->addDays(8),
    ]);

    Artisan::call('hackatons:sync-statuses');

    expect($hackaton->fresh()->status)->toBe(HackatonStatus::REGISTRATION_CLOSED);
});

test('sync command sets waiting_start close to start', function () {
    $organizer = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create([
        'is_public' => true,
        'status' => HackatonStatus::REGISTRATION_CLOSED,
        'registration_deadline_at' => now()->subDays(2),
        'start_at' => now()->addDay(),
        'end_at' => now()->addDays(3),
    ]);

    Artisan::call('hackatons:sync-statuses');

    expect($hackaton->fresh()->status)->toBe(HackatonStatus::WAITING_START);
});

test('sync command sets cases_announced when published cases exist', function () {
    $organizer = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create([
        'is_public' => true,
        'status' => HackatonStatus::REGISTRATION_CLOSED,
        'registration_deadline_at' => now()->subDays(3),
        'start_at' => now()->addDays(5),
        'end_at' => now()->addDays(6),
    ]);
    HackatonCase::factory()->create([
        'hackaton_id' => $hackaton->id,
        'is_published' => true,
    ]);

    Artisan::call('hackatons:sync-statuses');

    expect($hackaton->fresh()->status)->toBe(HackatonStatus::CASES_ANNOUNCED);
});

test('sync command keeps judging status after end', function () {
    $organizer = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create([
        'is_public' => true,
        'status' => HackatonStatus::JUDGING,
        'start_at' => now()->subDays(3),
        'end_at' => now()->subDay(),
    ]);

    Artisan::call('hackatons:sync-statuses');

    expect($hackaton->fresh()->status)->toBe(HackatonStatus::JUDGING);
});
