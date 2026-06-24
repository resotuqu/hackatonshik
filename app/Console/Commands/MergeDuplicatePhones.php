<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class MergeDuplicatePhones extends Command
{
    protected $signature = 'users:merge-duplicate-phones {--dry-run : Report duplicates without changing data}';

    protected $description = 'Report or clear duplicate phone numbers before applying the unique index migration';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        $duplicatePhones = User::query()
            ->whereNotNull('phone')
            ->select('phone')
            ->groupBy('phone')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('phone');

        if ($duplicatePhones->isEmpty()) {
            $this->info('No duplicate phone numbers found.');

            return self::SUCCESS;
        }

        $this->warn("Found {$duplicatePhones->count()} duplicate phone value(s).");

        foreach ($duplicatePhones as $phone) {
            $users = User::query()
                ->where('phone', $phone)
                ->orderBy('id')
                ->get(['id', 'email', 'nickname', 'phone', 'phone_verified_at']);

            $keeper = $users->first();
            $duplicates = $users->slice(1);

            $this->line("Phone {$phone}: keeping user #{$keeper->id} ({$keeper->email}), clearing {$duplicates->count()} duplicate(s).");

            foreach ($duplicates as $user) {
                $this->line("  - would clear user #{$user->id} ({$user->email})");

                if (! $dryRun) {
                    $user->forceFill([
                        'phone' => null,
                        'phone_verified_at' => null,
                    ])->saveQuietly();
                }
            }
        }

        if ($dryRun) {
            $this->comment('Dry run complete. Re-run without --dry-run to apply changes.');
        } else {
            $this->info('Duplicate phones cleared. You can now run migrations safely.');
        }

        return self::SUCCESS;
    }
}
