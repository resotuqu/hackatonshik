<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment('local', 'testing')) {
            $this->command?->warn('DatabaseSeeder skipped: demo seeders are only allowed in local/testing environments.');

            return;
        }

        $this->call([
            UserSeeder::class,
            RoleSeeder::class,
            SkillSeeder::class,
            HackatonTemplateSeeder::class,
            HackatonSeeder::class,
            HackatonDocumentSeeder::class,
            HackatonCaseSeeder::class,
            TeamSeeder::class,
            TeamRoleSeeder::class,
            TeamRoleSkillSeeder::class,
            TeamSocialLinkSeeder::class,
            HackatonApplicationSeeder::class,
            HackatonCaseJoinSeeder::class,
            TeamApplicationSeeder::class,
            HackatonCaseSubmissionSeeder::class,
            HackatonCaseAnswerSeeder::class,
            HackatonCaseScoreSeeder::class,
            HackatonAnnouncementSeeder::class,
            HackatonAnnouncementImageSeeder::class,
            HackatonImageSeeder::class,
            HackatonCertificateSeeder::class,
            UserHackatonDocumentSeeder::class,
            HackatonJudgeSeeder::class,
            JudgeInvitationSeeder::class,
            NewsPostSeeder::class,
            ContactMessageSeeder::class,
            SavedListFilterSeeder::class,
            ListAnalyticsEventSeeder::class,
            AvatarPresetPackSeeder::class,
            DemoShowcaseSeeder::class,
        ]);
    }
}
