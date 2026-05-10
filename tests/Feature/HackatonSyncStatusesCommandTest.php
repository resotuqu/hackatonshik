<?php

declare(strict_types=1);

use App\Enums\HackatonStatus;
use App\Models\Hackaton;
use App\Models\HackatonCase;
use App\Models\Team;
use App\Models\TeamRole;
use App\Models\User;
use App\Notifications\CaseDeadlineReminder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;

test('sync command transitions registration_open to in_progress when window opens', function () {
    $organizer = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create([
        'is_public' => true,
        'status' => HackatonStatus::REGISTRATION_OPEN,
        'start_at' => now()->subMinutes(5),
        'end_at' => now()->addHour(),
    ]);

    Artisan::call('hackatons:sync-statuses');

    expect($hackaton->fresh()->status)->toBe(HackatonStatus::IN_PROGRESS);
});

test('sync command transitions to registration_closed when deadline has passed', function () {
    $organizer = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create([
        'is_public' => true,
        'status' => HackatonStatus::REGISTRATION_OPEN,
        'registration_deadline_at' => now()->subDay(),
        'start_at' => now()->addDays(6),
        'end_at' => now()->addDays(7),
    ]);

    Artisan::call('hackatons:sync-statuses');

    expect($hackaton->fresh()->status)->toBe(HackatonStatus::REGISTRATION_CLOSED);
});

test('sync command transitions to cases_announced when cases are published before start', function () {
    $organizer = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create([
        'is_public' => true,
        'status' => HackatonStatus::REGISTRATION_CLOSED,
        'registration_deadline_at' => now()->subDays(3),
        'start_at' => now()->addDays(4),
        'end_at' => now()->addDays(6),
    ]);
    HackatonCase::factory()->create([
        'hackaton_id' => $hackaton->id,
        'is_published' => true,
    ]);

    Artisan::call('hackatons:sync-statuses');

    expect($hackaton->fresh()->status)->toBe(HackatonStatus::CASES_ANNOUNCED);
});

test('sync command transitions in_progress to finished after end_at', function () {
    $organizer = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create([
        'is_public' => true,
        'status' => HackatonStatus::IN_PROGRESS,
        'start_at' => now()->subDay(),
        'end_at' => now()->subMinutes(5),
    ]);

    Artisan::call('hackatons:sync-statuses');

    expect($hackaton->fresh()->status)->toBe(HackatonStatus::FINISHED);
});

test('sync command keeps archived status untouched', function () {
    $organizer = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create([
        'is_public' => true,
        'status' => HackatonStatus::ARCHIVED,
        'start_at' => now()->subDay(),
        'end_at' => now()->addHour(),
    ]);

    Artisan::call('hackatons:sync-statuses');

    expect($hackaton->fresh()->status)->toBe(HackatonStatus::ARCHIVED);
});

test('sync command sends case deadline reminders to team members', function () {
    Notification::fake();
    Cache::flush();

    $organizer = User::factory()->partner()->create();
    $captain = User::factory()->create();
    $member = User::factory()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create([
        'is_public' => true,
        'status' => HackatonStatus::IN_PROGRESS,
        'start_at' => now()->subDay(),
        'end_at' => now()->addDays(7),
    ]);
    $team = Team::factory()->for($captain)->for($hackaton)->create();
    TeamRole::factory()->for($team)->create(['user_id' => $member->id]);
    HackatonCase::factory()->create([
        'hackaton_id' => $hackaton->id,
        'is_published' => true,
        'deadline_at' => now()->addHours(12),
    ]);

    Artisan::call('hackatons:sync-statuses');

    Notification::assertSentTo($captain, CaseDeadlineReminder::class);
    Notification::assertSentTo($member, CaseDeadlineReminder::class);
});

test('sync command does not duplicate deadline reminder within the same day', function () {
    Notification::fake();
    Cache::flush();

    $organizer = User::factory()->partner()->create();
    $captain = User::factory()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create([
        'is_public' => true,
        'status' => HackatonStatus::IN_PROGRESS,
        'start_at' => now()->subDay(),
        'end_at' => now()->addDays(7),
    ]);
    Team::factory()->for($captain)->for($hackaton)->create();
    HackatonCase::factory()->create([
        'hackaton_id' => $hackaton->id,
        'is_published' => true,
        'deadline_at' => now()->addHours(12),
    ]);

    Artisan::call('hackatons:sync-statuses');
    Artisan::call('hackatons:sync-statuses');

    Notification::assertSentToTimes($captain, CaseDeadlineReminder::class, 1);
});

test('sync command ignores cases without upcoming deadline', function () {
    Notification::fake();
    Cache::flush();

    $organizer = User::factory()->partner()->create();
    $captain = User::factory()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create([
        'is_public' => true,
        'status' => HackatonStatus::IN_PROGRESS,
        'start_at' => now()->subDay(),
        'end_at' => now()->addDays(7),
    ]);
    Team::factory()->for($captain)->for($hackaton)->create();
    HackatonCase::factory()->create([
        'hackaton_id' => $hackaton->id,
        'is_published' => true,
        'deadline_at' => now()->addDays(5),
    ]);

    Artisan::call('hackatons:sync-statuses');

    Notification::assertNothingSent();
});
