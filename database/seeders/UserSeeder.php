<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Faker\Factory as FakerFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    private const DEMO_PASSWORD = 'password';

    public function run(): void
    {
        $faker = FakerFactory::create('ru_RU');
        $hashed = Hash::make(self::DEMO_PASSWORD);

        $definitions = [
            ['role' => 'admin', 'email' => 'admin@demo.hackaton.local', 'nickname' => 'admin_demo', 'phone' => '+79001000001', 'fio' => 'Администратор Системы'],
            ['role' => 'partner', 'email' => 'organizer1@demo.hackaton.local', 'nickname' => 'org_ivan', 'phone' => '+79001000002', 'fio' => 'Иванов Иван Иванович'],
            ['role' => 'partner', 'email' => 'organizer2@demo.hackaton.local', 'nickname' => 'org_maria', 'phone' => '+79001000003', 'fio' => 'Петрова Мария Сергеевна'],
            ['role' => 'judge', 'email' => 'judge1@demo.hackaton.local', 'nickname' => 'judge_alex', 'phone' => '+79001000004', 'fio' => 'Сидоров Алексей Павлович'],
            ['role' => 'judge', 'email' => 'judge2@demo.hackaton.local', 'nickname' => 'judge_elena', 'phone' => '+79001000005', 'fio' => 'Козлова Елена Викторовна'],
        ];

        $participantNicknames = ['dev_anna', 'coder_dmitry', 'ux_olga', 'pm_sergey', 'qa_nikita', 'data_irina', 'mobile_pavel', 'design_kate'];

        foreach ($definitions as $def) {
            User::query()->updateOrCreate(
                ['email' => $def['email']],
                [
                    'fio' => $def['fio'],
                    'date_of_birth' => '1990-05-15',
                    'email_verified_at' => now(),
                    'nickname' => $def['nickname'],
                    'password' => $hashed,
                    'phone' => $def['phone'],
                    'phone_verified_at' => now(),
                    'role' => $def['role'],
                    'description' => $faker->realText(120),
                    'is_profile_public' => true,
                    'show_email_on_profile' => false,
                    'show_phone_on_profile' => false,
                ],
            );
        }

        foreach ($participantNicknames as $i => $nick) {
            $n = $i + 1;
            User::query()->updateOrCreate(
                ['email' => "participant{$n}@demo.hackaton.local"],
                [
                    'fio' => $faker->name(),
                    'date_of_birth' => $faker->date('Y-m-d', '-18 years'),
                    'email_verified_at' => now(),
                    'nickname' => $nick,
                    'password' => $hashed,
                    'phone' => sprintf('+7%d', 9_002_001_000 + $i),
                    'phone_verified_at' => now(),
                    'role' => 'user',
                    'description' => $faker->optional(0.7)->realText(100),
                    'is_profile_public' => true,
                    'show_email_on_profile' => false,
                    'show_phone_on_profile' => false,
                ],
            );
        }

        $rows = User::query()
            ->where('email', 'like', '%@demo.hackaton.local')
            ->orderByRaw("CASE role WHEN 'admin' THEN 1 WHEN 'partner' THEN 2 WHEN 'judge' THEN 3 ELSE 4 END")
            ->orderBy('id')
            ->get(['role', 'email', 'phone', 'nickname']);

        $table = $rows->map(fn (User $u) => [
            $u->role,
            $u->email,
            $u->phone,
            $u->nickname,
            self::DEMO_PASSWORD,
        ])->all();

        $this->command?->newLine();
        $this->command?->info('Демо-пользователи (почта и телефон верифицированы). Пароль для всех: '.self::DEMO_PASSWORD);
        $this->command?->table(
            ['Роль', 'Email', 'Телефон', 'Никнейм', 'Пароль'],
            $table,
        );
    }
}
