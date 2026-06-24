<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AnonymizeUserAccount
{
    public static function run(User $user): void
    {
        DB::transaction(function () use ($user) {
            self::invalidateAuthentication($user);
            self::clearPersonalData($user);
            self::markAsDeleted($user);
            self::deleteRelatedData($user);
        });
    }

    private static function invalidateAuthentication(User $user): void
    {
        $user->update([
            'password' => Hash::make(Str::random(64)),
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'suspended_at' => now(),
        ]);

        DB::table('sessions')->where('user_id', $user->id)->delete();
    }

    private static function clearPersonalData(User $user): void
    {
        if ($user->avatar_path) {
            Storage::disk('public')->delete($user->avatar_path);
        }

        $user->update([
            'fio' => '',
            'email' => "deleted_{$user->id}_".time().'@deleted.invalid',
            'phone' => null,
            'phone_verified_at' => null,
            'date_of_birth' => null,
            'description' => '',
            'nickname' => "deleted_{$user->id}",
            'avatar_path' => null,
            'is_profile_public' => false,
            'show_email_on_profile' => false,
            'show_phone_on_profile' => false,
            'show_skills_on_profile' => false,
            'open_to_teams' => false,
            'email_verified_at' => null,
        ]);
    }

    private static function markAsDeleted(User $user): void
    {
        $user->update([
            'account_deleted_at' => now(),
        ]);
    }

    private static function deleteRelatedData(User $user): void
    {
        DB::table('organizer_applications')->where('user_id', $user->id)->delete();
        DB::table('hackaton_watches')->where('user_id', $user->id)->delete();
        DB::table('user_skills')->where('user_id', $user->id)->delete();

        DB::table('judge_invitations')
            ->where('invited_user_id', $user->id)
            ->orWhere('invited_by', $user->id)
            ->delete();

        DB::table('personal_access_tokens')->where('tokenable_id', $user->id)->delete();
        DB::table('password_reset_tokens')->where('email', $user->email)->delete();
    }
}
