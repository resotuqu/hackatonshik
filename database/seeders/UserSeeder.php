<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Faker\Factory as FakerFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    private const DEMO_PASSWORD = 'password';

    public function run(): void
    {
        $faker = FakerFactory::create('ru_RU');
        $hashed = Hash::make(self::DEMO_PASSWORD);
        $hackatonSlugs = $this->discoverHackatonSlugs();

        $definitions = [
            ['role' => UserRole::ADMIN->value, 'email' => 'admin@demo.hackaton.local', 'nickname' => 'admin_demo', 'phone' => '+79001000001', 'fio' => 'Администратор Системы'],
            ['role' => UserRole::JUDGE->value, 'email' => 'judge1@demo.hackaton.local', 'nickname' => 'judge_alex', 'phone' => '+79001000004', 'fio' => 'Сидоров Алексей Павлович'],
            ['role' => UserRole::JUDGE->value, 'email' => 'judge2@demo.hackaton.local', 'nickname' => 'judge_elena', 'phone' => '+79001000005', 'fio' => 'Козлова Елена Викторовна'],
            ['role' => UserRole::JUDGE->value, 'email' => 'judge3@demo.hackaton.local', 'nickname' => 'judge_roman', 'phone' => '+79001000006', 'fio' => 'Григорьев Роман Андреевич'],
        ];

        $organizerCount = max(1, count($hackatonSlugs));
        for ($i = 1; $i <= $organizerCount; $i++) {
            $slug = $hackatonSlugs[$i - 1] ?? "hackaton-{$i}";
            $definitions[] = [
                'role' => UserRole::PARTNER->value,
                'email' => "organizer{$i}@demo.hackaton.local",
                'nickname' => sprintf('org_%02d_%s', $i, Str::substr(str_replace('-', '_', $slug), 0, 12)),
                'phone' => sprintf('+7%d', 9_001_100_000 + $i),
                'fio' => $faker->name(),
            ];
        }

        foreach ($definitions as $def) {
            $user = User::query()->firstOrNew(['email' => $def['email']]);
            $user->fill([
                'fio' => $def['fio'],
                'date_of_birth' => '1990-05-15',
                'email_verified_at' => now(),
                'nickname' => $def['nickname'],
                'password' => $hashed,
                'phone' => $def['phone'],
                'description' => $faker->realText(120),
                'is_profile_public' => true,
                'show_email_on_profile' => false,
                'show_phone_on_profile' => false,
            ]);
            $user->forceFill([
                'phone_verified_at' => now(),
                'role' => $def['role'],
            ])->save();
        }

        $participantCount = max(80, $organizerCount * 40);
        for ($i = 0; $i < $participantCount; $i++) {
            $n = $i + 1;
            $user = User::query()->firstOrNew(['email' => "participant{$n}@demo.hackaton.local"]);
            $user->fill([
                'fio' => $faker->name(),
                'date_of_birth' => $faker->date('Y-m-d', '-18 years'),
                'email_verified_at' => now(),
                'nickname' => sprintf('participant_%03d', $n),
                'password' => $hashed,
                'phone' => sprintf('+7%d', 9_002_001_000 + $i),
                'description' => $faker->optional(0.7)->realText(100),
                'is_profile_public' => true,
                'show_email_on_profile' => false,
                'show_phone_on_profile' => false,
            ]);
            $user->forceFill([
                'phone_verified_at' => now(),
                'role' => UserRole::USER->value,
            ])->save();
        }

        $this->command?->newLine();
        $this->command?->info('Демо-пользователи созданы. Пароль для всех: '.self::DEMO_PASSWORD);
        $this->command?->line("Организаторы: {$organizerCount}");
        $this->command?->line("Участники: {$participantCount}");
    }

    /**
     * @return list<string>
     */
    private function discoverHackatonSlugs(): array
    {
        $placeholdersPath = base_path('placeholders');
        if (! File::isDirectory($placeholdersPath)) {
            return [];
        }

        $slugs = [];
        foreach (File::files($placeholdersPath) as $file) {
            if ($file->getExtension() !== 'md') {
                continue;
            }

            $slug = $file->getFilenameWithoutExtension();
            if (! File::exists($placeholdersPath.DIRECTORY_SEPARATOR.$slug.'.jpg')) {
                continue;
            }

            $slugs[] = $slug;
        }

        sort($slugs);

        return $slugs;
    }
}
