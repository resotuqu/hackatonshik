<?php

declare(strict_types=1);

use App\Actions\Hackaton\BuildOrganizerHackatonsHubData;
use App\Enums\ApplicationStatus;
use App\Models\Hackaton;
use App\Models\HackatonApplication;
use App\Models\JudgeInvitation;
use App\Models\User;

test('build returns null for guest', function () {
    expect((new BuildOrganizerHackatonsHubData)->build(null))->toBeNull();
});

test('build deduplicates pending application counts in summary and global pending', function () {
    $organizer = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create();
    HackatonApplication::factory()->create([
        'hackaton_id' => $hackaton->id,
        'status' => ApplicationStatus::PENDING,
    ]);

    $data = (new BuildOrganizerHackatonsHubData)->build($organizer);

    expect($data)->not->toBeNull()
        ->and($data['summary']['pendingApplications'])->toBe(1)
        ->and($data['globalPending']['applications'])->toBe(1);
});

test('build picks hackaton with most pending judge invitations for focus link', function () {
    $organizer = User::factory()->partner()->create();
    $hackatonA = Hackaton::factory()->for($organizer)->create();
    $hackatonB = Hackaton::factory()->for($organizer)->create();

    JudgeInvitation::factory()->create([
        'hackaton_id' => $hackatonA->id,
        'status' => JudgeInvitation::STATUS_PENDING,
        'invited_by' => $organizer->id,
    ]);
    JudgeInvitation::factory()->count(2)->create([
        'hackaton_id' => $hackatonB->id,
        'status' => JudgeInvitation::STATUS_PENDING,
        'invited_by' => $organizer->id,
    ]);

    $data = (new BuildOrganizerHackatonsHubData)->build($organizer);

    expect($data['globalPending']['judgeInvitations'])->toBe(3)
        ->and($data['judgeInvitationsFocusHackatonId'])->toBe($hackatonB->id);
});

test('build featured hackaton prefers one with pending applications', function () {
    $organizer = User::factory()->partner()->create();
    $quiet = Hackaton::factory()->for($organizer)->create(['title' => 'Quiet']);
    $busy = Hackaton::factory()->for($organizer)->create(['title' => 'Busy']);
    HackatonApplication::factory()->create([
        'hackaton_id' => $busy->id,
        'status' => ApplicationStatus::PENDING,
    ]);

    $data = (new BuildOrganizerHackatonsHubData)->build($organizer);

    expect($data['featuredHackaton']?->id)->toBe($busy->id)
        ->and($data['featuredHackaton']?->id)->not->toBe($quiet->id);
});
