<?php

use App\Enums\HackatonStatus;
use App\Models\Hackaton;
use App\Models\HackatonApplication;
use App\Models\HackatonWatch;
use App\Models\Team;
use App\Models\TeamRole;
use App\Models\User;
use App\Notifications\HackatonDeadlineReminder;
use Illuminate\Support\Facades\Notification;

beforeEach(function (): void {
    Notification::fake();
});

test('sends reminder to watcher without application within deadline window', function () {
    $user = User::factory()->create();
    $hackaton = Hackaton::factory()->create([
        'is_public' => true,
        'status' => HackatonStatus::REGISTRATION_OPEN,
        'registration_deadline_at' => now()->addDays(2),
    ]);
    HackatonWatch::factory()->create(['user_id' => $user->id, 'hackaton_id' => $hackaton->id]);

    $this->artisan('hackatons:send-deadline-reminders --days=3')->assertSuccessful();

    Notification::assertSentTo($user, HackatonDeadlineReminder::class);
});

test('does not send reminder to watcher who already applied via owned team', function () {
    $user = User::factory()->create();
    $hackaton = Hackaton::factory()->create([
        'is_public' => true,
        'status' => HackatonStatus::REGISTRATION_OPEN,
        'registration_deadline_at' => now()->addDays(2),
    ]);
    $team = Team::factory()->for($user)->for($hackaton)->create();
    HackatonApplication::factory()->create(['hackaton_id' => $hackaton->id, 'team_id' => $team->id]);
    HackatonWatch::factory()->create(['user_id' => $user->id, 'hackaton_id' => $hackaton->id]);

    $this->artisan('hackatons:send-deadline-reminders --days=3')->assertSuccessful();

    Notification::assertNotSentTo($user, HackatonDeadlineReminder::class);
});

test('does not send reminder to watcher who applied via team membership', function () {
    $owner = User::factory()->create();
    $member = User::factory()->create();
    $hackaton = Hackaton::factory()->create([
        'is_public' => true,
        'status' => HackatonStatus::REGISTRATION_OPEN,
        'registration_deadline_at' => now()->addDays(2),
    ]);
    $team = Team::factory()->for($owner)->for($hackaton)->create();
    TeamRole::factory()->for($team)->create(['user_id' => $member->id]);
    HackatonApplication::factory()->create(['hackaton_id' => $hackaton->id, 'team_id' => $team->id]);
    HackatonWatch::factory()->create(['user_id' => $member->id, 'hackaton_id' => $hackaton->id]);

    $this->artisan('hackatons:send-deadline-reminders --days=3')->assertSuccessful();

    Notification::assertNotSentTo($member, HackatonDeadlineReminder::class);
});

test('does not send reminder when deadline is outside the window', function () {
    $user = User::factory()->create();
    $hackaton = Hackaton::factory()->create([
        'is_public' => true,
        'status' => HackatonStatus::REGISTRATION_OPEN,
        'registration_deadline_at' => now()->addDays(10),
    ]);
    HackatonWatch::factory()->create(['user_id' => $user->id, 'hackaton_id' => $hackaton->id]);

    $this->artisan('hackatons:send-deadline-reminders --days=3')->assertSuccessful();

    Notification::assertNotSentTo($user, HackatonDeadlineReminder::class);
});

test('does not send reminder when hackaton is not public', function () {
    $user = User::factory()->create();
    $hackaton = Hackaton::factory()->create([
        'is_public' => false,
        'status' => HackatonStatus::REGISTRATION_OPEN,
        'registration_deadline_at' => now()->addDays(2),
    ]);
    HackatonWatch::factory()->create(['user_id' => $user->id, 'hackaton_id' => $hackaton->id]);

    $this->artisan('hackatons:send-deadline-reminders --days=3')->assertSuccessful();

    Notification::assertNotSentTo($user, HackatonDeadlineReminder::class);
});

test('does not send reminder again when reminder_sent_at is already set', function () {
    $user = User::factory()->create();
    $hackaton = Hackaton::factory()->create([
        'is_public' => true,
        'status' => HackatonStatus::REGISTRATION_OPEN,
        'registration_deadline_at' => now()->addDays(2),
    ]);
    HackatonWatch::factory()->create([
        'user_id' => $user->id,
        'hackaton_id' => $hackaton->id,
        'reminder_sent_at' => now()->subDay(),
    ]);

    $this->artisan('hackatons:send-deadline-reminders --days=3')->assertSuccessful();

    Notification::assertNotSentTo($user, HackatonDeadlineReminder::class);
});

test('sets reminder_sent_at after sending', function () {
    $user = User::factory()->create();
    $hackaton = Hackaton::factory()->create([
        'is_public' => true,
        'status' => HackatonStatus::REGISTRATION_OPEN,
        'registration_deadline_at' => now()->addDays(2),
    ]);
    $watch = HackatonWatch::factory()->create(['user_id' => $user->id, 'hackaton_id' => $hackaton->id]);

    $this->artisan('hackatons:send-deadline-reminders --days=3')->assertSuccessful();

    expect($watch->fresh()->reminder_sent_at)->not->toBeNull();
});

test('command respects custom --days option', function () {
    $user = User::factory()->create();
    $hackaton = Hackaton::factory()->create([
        'is_public' => true,
        'status' => HackatonStatus::REGISTRATION_OPEN,
        'registration_deadline_at' => now()->addDays(6),
    ]);
    HackatonWatch::factory()->create(['user_id' => $user->id, 'hackaton_id' => $hackaton->id]);

    // With default --days=3: outside window → no notification
    $this->artisan('hackatons:send-deadline-reminders --days=3')->assertSuccessful();
    Notification::assertNotSentTo($user, HackatonDeadlineReminder::class);

    // With --days=7: inside window → notification sent
    $this->artisan('hackatons:send-deadline-reminders --days=7')->assertSuccessful();
    Notification::assertSentTo($user, HackatonDeadlineReminder::class);
});
