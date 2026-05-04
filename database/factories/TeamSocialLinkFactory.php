<?php

namespace Database\Factories;

use App\Models\Team;
use App\Models\TeamSocialLink;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TeamSocialLink>
 */
class TeamSocialLinkFactory extends Factory
{
    public function definition(): array
    {
        return [
            'team_id' => Team::factory(),
            'name' => fake()->randomElement(['Telegram', 'GitHub', 'ВКонтакте', 'Сайт команды']),
            'url' => fake()->url(),
        ];
    }
}
