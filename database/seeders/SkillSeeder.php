<?php

namespace Database\Seeders;

use App\Models\Skill;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SkillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Skill::create([
            'name' => 'MySql'
        ]);

        Skill::create([
            'name' => 'PostgreSql'
        ]);

        Skill::create([
            'name' => 'NoSql'
        ]);

        Skill::create([
            'name' => 'PHP'
        ]);

        Skill::create([
            'name' => 'Python'
        ]);

        Skill::create([
            'name' => 'Java'
        ]);

        Skill::create([
            'name' => 'Basic'
        ]);

        Skill::create([
            'name' => 'Pascal'
        ]);

        Skill::create([
            'name' => 'Базы данных'
        ]);

        Skill::create([
            'name' => 'Бекэнд'
        ]);

        Skill::create([
            'name' => 'Фронтэнд'
        ]);

        Skill::create([
            'name' => 'Figma'
        ]);

        Skill::create([
            'name' => 'Microsoft Visio'
        ]);

        Skill::create([
            'name' => 'Microsoft Access'
        ]);

        Skill::create([
            'name' => 'Microsoft PowerPoint'
        ]);

        Skill::create([
            'name' => 'Linux'
        ]);

        Skill::create([
            'name' => 'Laravel'
        ]);

        Skill::create([
            'name' => 'Symphony'
        ]);

        Skill::create([
            'name' => 'Django'
        ]);
    }
}
