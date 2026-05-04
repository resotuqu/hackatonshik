<?php

namespace Database\Factories;

use App\Models\AvatarPreset;
use App\Models\AvatarPresetPack;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AvatarPreset>
 */
class AvatarPresetFactory extends Factory
{
    protected $model = AvatarPreset::class;

    public function definition(): array
    {
        return [
            'avatar_preset_pack_id' => AvatarPresetPack::factory(),
            'storage_path' => 'preset_avatars/packs/test/'.fake()->uuid().'.svg',
            'sort_order' => 0,
        ];
    }
}
