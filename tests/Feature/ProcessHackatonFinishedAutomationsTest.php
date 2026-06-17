<?php

declare(strict_types=1);

use App\Actions\Hackaton\ResolveParticipantUsersForHackatonCertificates;
use App\Enums\HackatonStatus;
use App\Jobs\ProcessHackatonFinishedAutomations;
use App\Models\Hackaton;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('finished automations job is idempotent', function () {
    $organizer = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create([
        'status' => HackatonStatus::FINISHED,
        'auto_publish_results_announcement' => true,
        'auto_issue_certificates' => false,
    ]);

    $job = new ProcessHackatonFinishedAutomations($hackaton->id);
    $job->handle(new ResolveParticipantUsersForHackatonCertificates);
    $job->handle(new ResolveParticipantUsersForHackatonCertificates);

    expect($hackaton->fresh()->finished_automations_ran_at)->not->toBeNull()
        ->and($hackaton->announcements()->count())->toBe(1);
});
