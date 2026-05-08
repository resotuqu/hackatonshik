<?php

declare(strict_types=1);

use App\Models\Hackaton;
use App\Models\HackatonCertificate;
use App\Models\User;
use App\Policies\HackatonCertificatePolicy;

it('allows only the organizer to create a certificate', function () {
    $organizer = User::factory()->partner()->create();
    $outsider = User::factory()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create();

    $policy = new HackatonCertificatePolicy;
    expect($policy->create($organizer, $hackaton))->toBeTrue();
    expect($policy->create($outsider, $hackaton))->toBeFalse();
});

it('allows recipient or organizer to view a certificate', function () {
    $organizer = User::factory()->partner()->create();
    $recipient = User::factory()->create();
    $outsider = User::factory()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create();
    $certificate = HackatonCertificate::factory()->create([
        'hackaton_id' => $hackaton->id,
        'user_id' => $recipient->id,
    ]);

    $policy = new HackatonCertificatePolicy;
    expect($policy->view($recipient, $certificate))->toBeTrue();
    expect($policy->view($organizer, $certificate))->toBeTrue();
    expect($policy->view($outsider, $certificate))->toBeFalse();
});

it('allows recipient or organizer to download a certificate', function () {
    $organizer = User::factory()->partner()->create();
    $recipient = User::factory()->create();
    $outsider = User::factory()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create();
    $certificate = HackatonCertificate::factory()->create([
        'hackaton_id' => $hackaton->id,
        'user_id' => $recipient->id,
    ]);

    $policy = new HackatonCertificatePolicy;
    expect($policy->download($recipient, $certificate))->toBeTrue();
    expect($policy->download($organizer, $certificate))->toBeTrue();
    expect($policy->download($outsider, $certificate))->toBeFalse();
});

it('allows only the organizer to delete a certificate', function () {
    $organizer = User::factory()->partner()->create();
    $recipient = User::factory()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create();
    $certificate = HackatonCertificate::factory()->create([
        'hackaton_id' => $hackaton->id,
        'user_id' => $recipient->id,
    ]);

    $policy = new HackatonCertificatePolicy;
    expect($policy->delete($organizer, $certificate))->toBeTrue();
    expect($policy->delete($recipient, $certificate))->toBeFalse();
});
