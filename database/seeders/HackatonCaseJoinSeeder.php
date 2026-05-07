<?php

namespace Database\Seeders;

use App\Enums\ApplicationStatus;
use App\Models\Hackaton;
use Illuminate\Database\Seeder;

class HackatonCaseJoinSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Join some teams to cases if their application is accepted
        $hackatons = Hackaton::with(['cases', 'teams.hackatonApplications'])->get();

        foreach ($hackatons as $hackaton) {
            $case = $hackaton->cases->first();
            if (! $case) {
                continue;
            }

            foreach ($hackaton->teams as $team) {
                $isAccepted = $team->hackatonApplications
                    ->where('hackaton_id', $hackaton->id)
                    ->where('status', ApplicationStatus::ACCEPTED)
                    ->isNotEmpty();

                if ($isAccepted && rand(0, 100) > 30) {
                    $team->update(['hackaton_case_id' => $case->id]);
                }
            }
        }
    }
}
