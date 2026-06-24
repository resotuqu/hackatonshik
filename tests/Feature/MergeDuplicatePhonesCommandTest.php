<?php

declare(strict_types=1);

use App\Models\Team;
use App\Models\User;

test('merge duplicate phones command reports duplicates in dry run mode', function () {
    User::factory()->create(['phone' => '+79990001122']);
    User::factory()->create(['phone' => '+79990001122']);

    $this->artisan('users:merge-duplicate-phones', ['--dry-run' => true])
        ->assertSuccessful()
        ->expectsOutputToContain('duplicate phone');

    expect(User::query()->where('phone', '+79990001122')->count())->toBe(2);
});

test('merge duplicate phones command clears duplicate records', function () {
    $keeper = User::factory()->create(['phone' => '+79990003344']);
    User::factory()->create(['phone' => '+79990003344']);

    $this->artisan('users:merge-duplicate-phones')
        ->assertSuccessful();

    expect(User::query()->where('phone', '+79990003344')->count())->toBe(1)
        ->and(User::query()->whereKey($keeper->id)->value('phone'))->toBe('+79990003344');
});
