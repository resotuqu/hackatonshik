<?php

use App\Enums\HackatonStatus;
use App\Models\Hackaton;
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

test('sync command keeps published status before start', function () {
    $organizer = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create([
        'is_public' => true,
        'status' => HackatonStatus::PUBLISHED,
        'start_at' => now()->addDay(),
        'end_at' => now()->addDays(2),
    ]);

    Artisan::call('hackatons:sync-statuses');

    expect($hackaton->fresh()->status)->toBe(HackatonStatus::PUBLISHED);
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
