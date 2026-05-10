<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Hackaton;
use App\Models\Team;
use App\Models\User;
use Faker\Factory as FakerFactory;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{
    public function run(): void
    {
        $faker = FakerFactory::create('ru_RU');
        $captains = User::query()->where('role', 'user')->orderBy('id')->get();
        if ($captains->isEmpty()) {
            return;
        }

        $i = 0;
        foreach (Hackaton::query()->with('cases')->orderBy('id')->get() as $hackaton) {
            $teamsToCreate = (int) ceil($this->targetParticipantsCount($hackaton->id) / 4);
            $cases = $hackaton->cases->values();

            foreach (range(1, $teamsToCreate) as $teamIndex) {
                $captain = $captains[$i % $captains->count()];
                $i++;

                Team::query()->create([
                    'user_id' => $captain->id,
                    'title' => $faker->catchPhrase().' — команда '.$faker->lastName(),
                    'description' => $faker->realText(120),
                    'image_url' => 'team_photos/default.png',
                    'cover_image' => null,
                    'hackaton_id' => $hackaton->id,
                    'hackaton_case_id' => $cases->isNotEmpty() ? $cases[($teamIndex - 1) % $cases->count()]->id : null,
                    'is_public' => true,
                ]);
            }
        }
    }

    private function targetParticipantsCount(int $hackatonId): int
    {
        return 20 + ($hackatonId % 21);
    }
}
