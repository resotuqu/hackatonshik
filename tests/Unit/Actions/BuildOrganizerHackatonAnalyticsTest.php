<?php

declare(strict_types=1);

use App\Actions\Hackaton\BuildOrganizerHackatonAnalytics;
use App\Enums\ApplicationStatus;
use App\Models\Hackaton;
use App\Models\HackatonApplication;
use App\Models\User;
use Illuminate\Support\Carbon;

test('analytics returns conversion rate and daily application counts', function () {
    Carbon::setTestNow('2026-06-15 12:00:00');

    $organizer = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create();

    HackatonApplication::factory()->create([
        'hackaton_id' => $hackaton->id,
        'status' => ApplicationStatus::ACCEPTED,
        'created_at' => now()->subDays(2),
    ]);
    HackatonApplication::factory()->create([
        'hackaton_id' => $hackaton->id,
        'status' => ApplicationStatus::PENDING,
        'created_at' => now()->subDays(1),
    ]);

    $analytics = app(BuildOrganizerHackatonAnalytics::class)->handle($organizer);

    expect($analytics['totalApplications'])->toBe(2)
        ->and($analytics['acceptedApplications'])->toBe(1)
        ->and($analytics['conversionRate'])->toBe(50.0)
        ->and($analytics['applicationsByDay'])->toHaveCount(14)
        ->and(collect($analytics['applicationsByDay'])->sum('count'))->toBe(2);

    Carbon::setTestNow();
});
