<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\HackatonStatus;
use App\Models\Hackaton;
use Illuminate\Database\Seeder;

/**
 * Pins the SmartOmega demo hackathon to FINISHED for live presentations.
 */
class DemoShowcaseSeeder extends Seeder
{
    public const SHOWCASE_TITLE = 'SmartOmega GameJab 2026';

    public function run(): void
    {
        $hackaton = Hackaton::query()->where('title', self::SHOWCASE_TITLE)->first();

        if ($hackaton === null) {
            $this->command?->warn('Demo showcase hackathon not found — skipping DemoShowcaseSeeder.');

            return;
        }

        $hackaton->update([
            'status' => HackatonStatus::FINISHED,
            'is_public' => true,
            'auto_issue_certificates' => true,
            'auto_publish_results_announcement' => true,
        ]);
    }
}
