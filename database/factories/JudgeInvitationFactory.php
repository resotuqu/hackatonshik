<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Hackaton;
use App\Models\JudgeInvitation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<JudgeInvitation>
 */
class JudgeInvitationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'hackaton_id' => Hackaton::factory(),
            'invited_email' => fake()->unique()->safeEmail(),
            'invited_by' => User::factory()->partner(),
            'invited_user_id' => null,
            'token' => Str::random(64),
            'status' => JudgeInvitation::STATUS_PENDING,
            'accepted_at' => null,
        ];
    }
}
