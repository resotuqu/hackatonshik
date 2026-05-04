<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\SavedListFilter;
use App\Models\User;
use Illuminate\Database\Seeder;

class SavedListFilterSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::query()->whereIn('role', ['user', 'partner'])->orderBy('id')->take(4)->get();
        if ($users->isEmpty()) {
            return;
        }

        foreach ($users as $idx => $user) {
            SavedListFilter::query()->create([
                'user_id' => $user->id,
                'list_key' => 'hackatons',
                'name' => 'Мой набор '.($idx + 1),
                'filters' => [
                    'status' => ['registration_open', 'in_progress'],
                    'level' => ['intermediate'],
                    'sort' => 'start_at',
                ],
            ]);
        }
    }
}
