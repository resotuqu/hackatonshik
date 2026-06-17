<?php

declare(strict_types=1);

use App\Actions\Hackaton\BuildOrganizerFunnelMetrics;
use App\Enums\ApplicationStatus;
use App\Models\Hackaton;
use App\Models\HackatonAnalyticsEvent;
use App\Models\HackatonApplication;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('public hackaton show records page view once per session', function () {
    $organizer = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create(['is_public' => true]);

    $this->get(route('hackatons.show', $hackaton))->assertSuccessful();
    $this->get(route('hackatons.show', $hackaton))->assertSuccessful();

    expect(HackatonAnalyticsEvent::query()->where('event_name', 'page_view')->count())->toBe(1);
});

test('organizer funnel metrics calculate view to application conversion', function () {
    $organizer = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create(['is_public' => true]);
    $team = Team::factory()->for(User::factory()->create())->for($hackaton)->create();

    HackatonAnalyticsEvent::factory()->count(10)->create([
        'hackaton_id' => $hackaton->id,
        'event_name' => 'page_view',
    ]);

    HackatonApplication::factory()->create([
        'hackaton_id' => $hackaton->id,
        'team_id' => $team->id,
        'status' => ApplicationStatus::ACCEPTED,
    ]);

    $metrics = app(BuildOrganizerFunnelMetrics::class)->handle($organizer);

    expect($metrics['summary']['views'])->toBe(10)
        ->and($metrics['summary']['applications'])->toBe(1)
        ->and($metrics['summary']['accepted'])->toBe(1)
        ->and($metrics['summary']['viewToApplicationRate'])->toBe(10.0)
        ->and($metrics['hackatons'][0]['completionRate'])->toBe(100.0)
        ->and($metrics['slices']['weekly'])->toBeArray()
        ->and($metrics['slices']['monthly'])->toBeArray()
        ->and($metrics['statusSegments'])->toHaveKey(ApplicationStatus::ACCEPTED->value);
});

test('organizer can export funnel csv', function () {
    $organizer = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create();

    HackatonAnalyticsEvent::factory()->create([
        'hackaton_id' => $hackaton->id,
        'event_name' => 'page_view',
    ]);

    $this->actingAs($organizer)
        ->get(route('organizer.analytics.export'))
        ->assertOk()
        ->assertHeader('content-type', 'text/csv; charset=UTF-8');
});

test('participant cannot export organizer funnel', function () {
    $participant = User::factory()->create();

    $this->actingAs($participant)
        ->get(route('organizer.analytics.export'))
        ->assertForbidden();
});

test('application store records analytics event', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
        'phone_verified_at' => now(),
    ]);
    $hackaton = Hackaton::factory()->create(['is_public' => true]);
    $team = Team::factory()->for($user)->for($hackaton)->create();

    $this->actingAs($user)
        ->post(route('hackaton.applications.store'), [
            'hackaton_id' => $hackaton->id,
            'team_id' => $team->id,
            'message' => 'Hello',
        ])
        ->assertRedirect();

    expect(HackatonAnalyticsEvent::query()->where('event_name', 'application_submitted')->exists())->toBeTrue();
});
