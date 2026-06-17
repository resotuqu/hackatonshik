<?php

declare(strict_types=1);

use App\Models\Hackaton;
use App\Models\HackatonCase;
use App\Models\User;
use App\Policies\HackatonCasePolicy;

it('allows guest to view a published hackaton case', function () {
    $hackatonCase = HackatonCase::factory()->create(['is_published' => true]);

    expect((new HackatonCasePolicy)->view(null, $hackatonCase))->toBeTrue();
});

it('denies guest from viewing an unpublished hackaton case', function () {
    $hackatonCase = HackatonCase::factory()->create(['is_published' => false]);

    expect((new HackatonCasePolicy)->view(null, $hackatonCase))->toBeFalse();
});

it('allows organizer to view their unpublished hackaton case', function () {
    $organizer = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create();
    $hackatonCase = HackatonCase::factory()->for($hackaton)->create(['is_published' => false]);

    expect((new HackatonCasePolicy)->view($organizer, $hackatonCase))->toBeTrue();
});

it('allows only the hackaton organizer to create update and delete cases', function () {
    $organizer = User::factory()->partner()->create();
    $other = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create();
    $hackatonCase = HackatonCase::factory()->for($hackaton)->create();

    $policy = new HackatonCasePolicy;

    expect($policy->create($organizer, $hackaton))->toBeTrue();
    expect($policy->create($other, $hackaton))->toBeFalse();
    expect($policy->update($organizer, $hackatonCase))->toBeTrue();
    expect($policy->update($other, $hackatonCase))->toBeFalse();
    expect($policy->delete($organizer, $hackatonCase))->toBeTrue();
    expect($policy->delete($other, $hackatonCase))->toBeFalse();
});
