<?php

declare(strict_types=1);

use App\Actions\Hackaton\BuildOrganizerHackatonReadinessChecklist;
use App\Enums\ApplicationStatus;
use App\Models\Hackaton;
use App\Models\HackatonApplication;
use App\Models\HackatonCase;
use App\Models\HackatonDocument;
use App\Models\User;

test('readiness checklist uses configured minimum accepted applications', function () {
    config(['hackaton.organizer_readiness_min_accepted_applications' => 2]);

    $organizer = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create();
    HackatonDocument::factory()->create(['hackaton_id' => $hackaton->id]);
    HackatonCase::factory()->create([
        'hackaton_id' => $hackaton->id,
        'is_published' => true,
        'publish_at' => now()->subHour(),
    ]);
    HackatonApplication::factory()->count(2)->create([
        'hackaton_id' => $hackaton->id,
        'status' => ApplicationStatus::ACCEPTED,
    ]);

    $items = (new BuildOrganizerHackatonReadinessChecklist)->handle($hackaton->fresh());

    $applicationsItem = collect($items)->first(fn (array $row) => str_contains($row['label'], 'принятых команд'));
    expect($applicationsItem)->not->toBeNull()
        ->and($applicationsItem['done'])->toBeTrue()
        ->and($applicationsItem['label'])->toContain('2');
});
