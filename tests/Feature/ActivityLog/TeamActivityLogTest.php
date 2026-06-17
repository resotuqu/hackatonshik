<?php

use App\Models\Team;
use App\Models\User;
use Spatie\Activitylog\Models\Activity;

use function Pest\Laravel\actingAs;

test('team title change is logged with causer', function () {
    $captain = User::factory()->create();
    $team = Team::factory()->create([
        'user_id' => $captain->id,
        'title' => 'Старое название',
    ]);

    actingAs($captain);

    $team->update(['title' => 'Новое название']);

    $activity = Activity::query()
        ->forSubject($team)
        ->where('event', 'updated')
        ->latest()
        ->first();

    expect($activity)->not->toBeNull()
        ->and($activity?->causer_id)->toBe($captain->id)
        ->and($activity?->log_name)->toBe('team')
        ->and($activity?->attribute_changes?->get('attributes')['title'] ?? null)->toBe('Новое название');
});
