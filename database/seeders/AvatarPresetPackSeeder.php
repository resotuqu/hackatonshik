<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\AvatarPresetPack;
use Illuminate\Database\Seeder;

class AvatarPresetPackSeeder extends Seeder
{
    public function run(): void
    {
        AvatarPresetPack::query()->firstOrCreate(
            ['slug' => 'year-2026'],
            ['name' => 'Год 2026', 'sort_order' => 10, 'is_active' => true]
        );
        AvatarPresetPack::query()->firstOrCreate(
            ['slug' => 'vesna-2026'],
            ['name' => 'ВЕСНА 2026', 'sort_order' => 20, 'is_active' => true]
        );
    }
}
