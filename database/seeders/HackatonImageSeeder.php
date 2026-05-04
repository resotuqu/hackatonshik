<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Hackaton;
use App\Models\HackatonImage;
use Illuminate\Database\Seeder;

class HackatonImageSeeder extends Seeder
{
    public function run(): void
    {
        foreach (Hackaton::query()->where('is_public', true)->orderBy('id')->get() as $hackaton) {
            HackatonImage::factory()
                ->count(2)
                ->for($hackaton)
                ->create();
        }
    }
}
