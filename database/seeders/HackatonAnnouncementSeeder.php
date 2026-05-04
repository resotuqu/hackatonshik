<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Hackaton;
use App\Models\HackatonAnnouncement;
use Illuminate\Database\Seeder;

class HackatonAnnouncementSeeder extends Seeder
{
    public function run(): void
    {
        foreach (Hackaton::query()->orderBy('id')->get() as $hackaton) {
            HackatonAnnouncement::factory()
                ->count(2)
                ->for($hackaton)
                ->create([
                    'created_by' => $hackaton->user_id,
                    'is_draft' => false,
                    'template_key' => null,
                    'published_at' => now()->subDays(random_int(1, 10)),
                ]);
        }
    }
}
