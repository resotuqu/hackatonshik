<?php

use App\Enums\ApplicationStatus;
use App\Models\Team;
use App\Models\TeamApplication;
use App\Models\TeamRole;
use App\Models\User;
use Spatie\Activitylog\Models\Activity;

use function Pest\Laravel\actingAs;

test('application review creates status_changed activity', function () {
    $captain = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $captain->id]);
    $teamRole = TeamRole::factory()->create(['team_id' => $team->id, 'user_id' => null]);
    $applicant = User::factory()->create();
    $application = TeamApplication::factory()->create([
        'team_role_id' => $teamRole->id,
        'user_id' => $applicant->id,
        'status' => ApplicationStatus::PENDING,
    ]);

    actingAs($captain)
        ->patch(route('team.applications.update', $application), [
            'status' => ApplicationStatus::ACCEPTED->value,
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    $activity = Activity::query()
        ->forSubject($application)
        ->where('description', 'status_changed')
        ->latest()
        ->first();

    expect($activity)->not->toBeNull()
        ->and($activity?->causer_id)->toBe($captain->id)
        ->and($activity?->getProperty('status'))->toBe(ApplicationStatus::ACCEPTED->value);
});
