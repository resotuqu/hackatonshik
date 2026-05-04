<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\HackatonAnnouncement;
use App\Models\HackatonAnnouncementImage;
use Illuminate\Database\Seeder;

class HackatonAnnouncementImageSeeder extends Seeder
{
    public function run(): void
    {
        foreach (HackatonAnnouncement::query()->orderBy('id')->get() as $announcement) {
            HackatonAnnouncementImage::factory()
                ->count(random_int(0, 2))
                ->for($announcement, 'announcement')
                ->create();
        }
    }
}
