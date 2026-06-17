<?php

declare(strict_types=1);

use App\Models\User;
use App\Policies\UserPolicy;

it('allows only admins to view another user activity history', function () {
    $admin = User::factory()->admin()->create();
    $participant = User::factory()->create();
    $target = User::factory()->create();

    $policy = new UserPolicy;

    expect($policy->viewActivityHistory($admin, $target))->toBeTrue();
    expect($policy->viewActivityHistory($participant, $target))->toBeFalse();
});
