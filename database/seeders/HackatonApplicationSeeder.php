<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\ApplicationStatus;
use App\Models\HackatonApplication;
use App\Models\Team;
use Illuminate\Database\Seeder;

class HackatonApplicationSeeder extends Seeder
{
    public function run(): void
    {
        foreach (Team::query()->orderBy('id')->get() as $team) {
            HackatonApplication::query()->firstOrCreate(
                [
                    'team_id' => $team->id,
                    'hackaton_id' => $team->hackaton_id,
                ],
                [
                    'message' => 'Просим принять нашу команду на хакатон. Готовы к любым форматам проверки.',
                    'status' => ApplicationStatus::ACCEPTED,
                    'reviewed_at' => now()->subDays(2),
                    'reviewed_by' => $team->hackaton->user_id,
                ],
            );
        }
    }
}
