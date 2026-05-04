<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\HackatonStatus;
use App\Models\Hackaton;
use App\Models\HackatonCertificate;
use App\Models\User;
use Illuminate\Database\Seeder;

class HackatonCertificateSeeder extends Seeder
{
    public function run(): void
    {
        $finished = Hackaton::query()
            ->whereIn('status', [HackatonStatus::FINISHED, HackatonStatus::JUDGING])
            ->orderBy('id')
            ->get();

        $participants = User::query()->where('role', 'user')->orderBy('id')->take(5)->get();
        $organizer = User::query()->where('role', 'partner')->orderBy('id')->first();
        if ($finished->isEmpty() || $participants->isEmpty() || ! $organizer) {
            return;
        }

        foreach ($finished as $hackaton) {
            foreach ($participants as $user) {
                $title = 'Участие в хакатоне «'.$hackaton->title.'» — '.$user->nickname;
                HackatonCertificate::query()->firstOrCreate(
                    [
                        'hackaton_id' => $hackaton->id,
                        'user_id' => $user->id,
                        'title' => $title,
                    ],
                    [
                        'uploaded_by' => $organizer->id,
                        'file_path' => 'hackaton_certificates/demo-'.$hackaton->id.'-'.$user->id.'.pdf',
                        'issued_at' => now()->subDays(5),
                    ],
                );
            }
        }
    }
}
