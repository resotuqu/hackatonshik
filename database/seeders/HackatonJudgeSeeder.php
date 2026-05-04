<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Hackaton;
use App\Models\HackatonJudge;
use App\Models\User;
use Illuminate\Database\Seeder;

class HackatonJudgeSeeder extends Seeder
{
    public function run(): void
    {
        $judges = User::query()->where('role', 'judge')->orderBy('id')->get();
        $organizer = User::query()->where('role', 'partner')->orderBy('id')->first();
        if ($judges->isEmpty() || ! $organizer) {
            return;
        }

        foreach (Hackaton::query()->where('is_public', true)->orderBy('id')->get() as $hackaton) {
            foreach ($judges as $judge) {
                HackatonJudge::query()->firstOrCreate(
                    [
                        'hackaton_id' => $hackaton->id,
                        'user_id' => $judge->id,
                    ],
                    [
                        'assigned_by' => $organizer->id,
                        'assigned_at' => now()->subDays(3),
                    ],
                );
            }
        }
    }
}
