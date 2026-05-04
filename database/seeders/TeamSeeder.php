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
        foreach (Hackaton::query()->orderBy('id')->get() as $hackaton) {
            foreach (range(1, 2) as $_) {
                $captain = $captains[$i % $captains->count()];
                $i++;

                Team::query()->create([
                    'user_id' => $captain->id,
                    'title' => $faker->catchPhrase().' — команда '.$faker->lastName(),
                    'description' => $faker->realText(120),
                    'image_url' => 'team_photos/default.png',
                    'cover_image' => null,
                    'hackaton_id' => $hackaton->id,
                    'is_public' => true,
                ]);
            }
        }
    }
}
