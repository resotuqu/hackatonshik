<?php

namespace Database\Factories;

use App\Models\Hackaton;
use App\Models\Role;
use App\Models\Team;
use App\Models\TeamRole;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Team>
 */
class TeamFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->company(),
            'description' => fake()->sentence(10),
            'image_url' => 'team_photos/default.png',
            'cover_image' => null,
            'hackaton_id' => Hackaton::factory(),
            'is_public' => true,
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Team $team): void {
            if ($team->captainRole() !== null) {
                return;
            }

            $roleId = Role::query()->value('id') ?? Role::factory()->create()->id;

            TeamRole::factory()->for($team)->create([
                'user_id' => $team->user_id,
                'role_id' => $roleId,
                'title' => 'Капитан',
                'description' => filled($team->description)
                    ? (string) $team->description
                    : 'Руководитель команды',
            ]);
        });
    }

    public function withoutCaptainRole(): static
    {
        return $this->afterCreating(function (Team $team): void {
            TeamRole::query()
                ->where('team_id', $team->id)
                ->where('user_id', $team->user_id)
                ->delete();
        });
    }
}
