<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Hackaton;
use App\Models\HackatonJudge;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<HackatonJudge>
 */
class HackatonJudgeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'hackaton_id' => Hackaton::factory(),
            'user_id' => User::factory()->judge(),
            'assigned_by' => User::factory()->partner(),
            'assigned_at' => now(),
        ];
    }
}
