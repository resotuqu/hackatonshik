<?php

namespace Database\Seeders;

use App\Enums\ApplicationStatus;
use App\Enums\HackatonStatus;
use App\Models\Hackaton;
use Illuminate\Database\Seeder;

class HackatonCaseJoinSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hackatons = Hackaton::with(['cases', 'teams.hackatonApplications'])->get();

        foreach ($hackatons as $hackaton) {
            $cases = $hackaton->cases->values();
            if ($cases->isEmpty()) {
                continue;
            }

            $casePointer = 0;
            foreach ($hackaton->teams as $team) {
                $isAccepted = $team->hackatonApplications
                    ->where('hackaton_id', $hackaton->id)
                    ->where('status', ApplicationStatus::ACCEPTED)
                    ->isNotEmpty();

                if (! $isAccepted) {
                    continue;
                }

                $currentCaseId = $team->hackaton_case_id;
                $isCurrentCaseValid = $currentCaseId !== null && $cases->contains('id', $currentCaseId);
                $nextCaseId = $cases[$casePointer % $cases->count()]->id;
                $casePointer++;

                if (! $isCurrentCaseValid || $team->hackaton->status !== HackatonStatus::REGISTRATION_OPEN) {
                    $team->update(['hackaton_case_id' => $nextCaseId]);
                }
            }
        }
    }
}
