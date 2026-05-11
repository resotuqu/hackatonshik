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
            $hackaton->images()->delete();

            if (! filled($hackaton->image_url)) {
                continue;
            }

            HackatonImage::query()->create([
                'hackaton_id' => $hackaton->id,
                'path' => $hackaton->image_url,
                'sort_order' => 0,
                'alt' => $hackaton->title,
            ]);
        }
    }
}
