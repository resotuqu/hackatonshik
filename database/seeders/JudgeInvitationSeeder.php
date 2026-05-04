<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Hackaton;
use App\Models\JudgeInvitation;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class JudgeInvitationSeeder extends Seeder
{
    public function run(): void
    {
        $organizer = User::query()->where('role', 'partner')->orderBy('id')->first();
        if (! $organizer) {
            return;
        }

        foreach (Hackaton::query()->where('is_public', true)->orderBy('id')->get() as $hackaton) {
            JudgeInvitation::query()->firstOrCreate(
                [
                    'hackaton_id' => $hackaton->id,
                    'invited_email' => 'guest_judge_'.$hackaton->id.'@invite.demo.local',
                    'status' => JudgeInvitation::STATUS_PENDING,
                ],
                [
                    'invited_by' => $organizer->id,
                    'invited_user_id' => null,
                    'token' => Str::random(64),
                    'accepted_at' => null,
                ],
            );
        }
    }
}
