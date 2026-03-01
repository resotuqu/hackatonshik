<?php

namespace Database\Seeders;

use App\Models\Hackaton;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HackatonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
//        Hackaton::factory()->create([
//            'user_id' => User::factory()->create(),
//            'title' => "Моя профессия ИТ 2026",
//            'description' => "САМЫЕ САМЫЕ ПРЕСАМЫЕ",
//            'image_url' => 'http://avatars.mds.yandex.net/get-vthumb/3323915/5095dd11582f3750967b23690b35df80/800x450',
//            'start_at' => '2026-03-18',
//            'end_at' => '2026-03-20',
//            'is_public' => true
//        ]);

        Hackaton::factory()->create([
            'user_id' => User::factory()->create(),
            'title' => "Сбер ХакAI 2026",
            'description' => "Мы берём деньги, чтобы делать деньги",
            'image_url' => 'http://avatars.mds.yandex.net/get-vthumb/3323915/5095dd11582f3750967b23690b35df80/800x450',
            'start_at' => '2026-04-01',
            'end_at' => '2026-04-10',
            'is_public' => true
        ]);
    }
}
