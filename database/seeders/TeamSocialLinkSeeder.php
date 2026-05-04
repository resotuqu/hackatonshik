<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Team;
use App\Models\TeamSocialLink;
use Illuminate\Database\Seeder;

class TeamSocialLinkSeeder extends Seeder
{
    public function run(): void
    {
        foreach (Team::query()->orderBy('id')->get() as $team) {
            TeamSocialLink::factory()
                ->count(random_int(1, 2))
                ->for($team)
                ->create();
        }
    }
}
