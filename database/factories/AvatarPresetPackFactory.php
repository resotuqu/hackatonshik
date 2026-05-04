<?php

namespace Database\Factories;

use App\Models\AvatarPresetPack;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AvatarPresetPack>
 */
class AvatarPresetPackFactory extends Factory
{
    protected $model = AvatarPresetPack::class;

    public function definition(): array
    {
        $slug = 'pack-'.fake()->unique()->numerify('####');

        return [
            'slug' => $slug,
            'name' => fake()->words(2, true),
            'sort_order' => 0,
            'is_active' => true,
        ];
    }
}
