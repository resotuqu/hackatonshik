<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Database\QueryException;

test('duplicate phone is rejected at database level', function () {
    User::factory()->create(['phone' => '+79991234567']);

    expect(fn () => User::factory()->create(['phone' => '+79991234567']))
        ->toThrow(QueryException::class);
});

test('multiple users can have null phone', function () {
    User::factory()->create(['phone' => null]);
    User::factory()->create(['phone' => null]);

    expect(User::query()->whereNull('phone')->count())->toBe(2);
});
