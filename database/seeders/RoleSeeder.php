<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create([
            'name' => 'Фуллстек-программист',
        ]);

        Role::create([
            'name' => 'Дизайнер',
        ]);

        Role::create([
            'name' => 'Менеджер',
        ]);

        Role::create([
            'name' => 'Тестировщик',
        ]);

        Role::create([
            'name' => 'Разработчик баз данных',
        ]);

        Role::create([
            'name' => 'Фронтенд-разработчик',
        ]);

        Role::create([
            'name' => 'Бекенд-разработчик',
        ]);
    }
}
