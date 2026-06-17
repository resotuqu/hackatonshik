<?php

use App\Enums\HackatonStatus;
use App\Models\Hackaton;
use App\Models\User;
use Spatie\Activitylog\Models\Activity;

use function Pest\Laravel\actingAs;

test('hackaton status change is logged', function () {
    $organizer = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->create([
        'user_id' => $organizer->id,
        'status' => HackatonStatus::DRAFT,
    ]);

    actingAs($organizer);

    $hackaton->update(['status' => HackatonStatus::PUBLISHED]);

    $activity = Activity::query()
        ->forSubject($hackaton)
        ->where('event', 'updated')
        ->latest()
        ->first();

    expect($activity)->not->toBeNull()
        ->and($activity?->log_name)->toBe('hackaton')
        ->and($activity?->attribute_changes?->get('attributes')['status'] ?? null)->toBe(HackatonStatus::PUBLISHED->value);
});
