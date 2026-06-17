<?php

declare(strict_types=1);

use App\Models\Hackaton;
use App\Models\HackatonAnnouncement;
use App\Models\User;
use App\Policies\HackatonAnnouncementPolicy;

it('allows anyone to view hackaton announcements', function () {
    $announcement = HackatonAnnouncement::factory()->create();

    expect((new HackatonAnnouncementPolicy)->view(null, $announcement))->toBeTrue();
});

it('allows only the hackaton organizer to manage announcements', function () {
    $organizer = User::factory()->partner()->create();
    $other = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create();
    $announcement = HackatonAnnouncement::factory()->for($hackaton)->create([
        'created_by' => $organizer->id,
    ]);

    $policy = new HackatonAnnouncementPolicy;

    expect($policy->create($organizer, $hackaton))->toBeTrue();
    expect($policy->create($other, $hackaton))->toBeFalse();
    expect($policy->update($organizer, $announcement))->toBeTrue();
    expect($policy->update($other, $announcement))->toBeFalse();
    expect($policy->delete($organizer, $announcement))->toBeTrue();
    expect($policy->delete($other, $announcement))->toBeFalse();
});
