<?php

declare(strict_types=1);

use App\Actions\Hackaton\BuildOrganizerShowHeaderMetrics;
use App\Models\Hackaton;
use App\Models\HackatonCase;
use App\Models\User;
use Illuminate\Support\Carbon;

test('header metrics resolve nearest future deadline', function () {
    $organizer = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create([
        'registration_deadline_at' => now()->addDays(3),
        'start_at' => now()->addDay(),
        'end_at' => now()->addWeek(),
    ]);
    HackatonCase::factory()->create([
        'hackaton_id' => $hackaton->id,
        'title' => 'Later case',
        'deadline_at' => now()->addDays(2),
        'publish_at' => now()->subHour(),
        'is_published' => true,
    ]);

    $hackaton->load(['cases', 'judges']);

    $metrics = (new BuildOrganizerShowHeaderMetrics)->handle(
        $hackaton,
        ['applications_pending' => 0],
        ['judgeCandidates' => collect(), 'pendingJudgeInvitations' => collect()],
    );

    expect($metrics['next_deadline_label'])->toBe('Старт хакатона')
        ->and(Carbon::parse($metrics['next_deadline_at'])->equalTo($hackaton->start_at))->toBeTrue();
});
