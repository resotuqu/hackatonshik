<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\HackatonStatus;
use App\Models\HackatonWatch;
use App\Notifications\HackatonDeadlineReminder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SendHackatonDeadlineReminders extends Command
{
    protected $signature = 'hackatons:send-deadline-reminders
                            {--days=3 : Send reminder this many days before the registration deadline}';

    protected $description = 'Email watchlist users who have not applied yet, N days before the registration deadline closes.';

    public function handle(): int
    {
        $days = max(1, (int) $this->option('days'));
        $sent = 0;
        $windowEnd = now()->addDays($days)->endOfDay();

        /*
         * Find hackaton_watches where:
         *  - reminder not yet sent
         *  - hackaton registration closes within the window
         *  - hackaton is public and registration is open
         *  - the watcher's user has NO team application for this hackaton
         *    (neither as team owner nor as team member via team_roles)
         */
        HackatonWatch::query()
            ->whereNull('reminder_sent_at')
            ->whereHas('hackaton', fn ($q) => $q
                ->where('is_public', true)
                ->whereIn('status', [
                    HackatonStatus::PUBLISHED->value,
                    HackatonStatus::REGISTRATION_OPEN->value,
                ])
                ->whereNotNull('registration_deadline_at')
                ->whereBetween('registration_deadline_at', [now(), $windowEnd])
            )
            ->whereNotExists(fn ($q) => $q
                ->select(DB::raw(1))
                ->from('hackaton_applications')
                ->whereColumn('hackaton_applications.hackaton_id', 'hackaton_watches.hackaton_id')
                ->where(fn ($sub) => $sub
                    ->whereIn('team_id', fn ($t) => $t
                        ->select('id')
                        ->from('teams')
                        ->whereColumn('user_id', 'hackaton_watches.user_id')
                    )
                    ->orWhereIn('team_id', fn ($t) => $t
                        ->select('team_id')
                        ->from('team_roles')
                        ->whereColumn('user_id', 'hackaton_watches.user_id')
                        ->whereNotNull('user_id')
                    )
                )
            )
            ->with(['user:id,email,fio', 'hackaton:id,title,registration_deadline_at,status'])
            ->chunkById(100, function ($watches) use ($days, &$sent): void {
                $ids = [];

                foreach ($watches as $watch) {
                    if ($watch->user === null || $watch->hackaton === null) {
                        continue;
                    }

                    $watch->user->notify(new HackatonDeadlineReminder($watch->hackaton, $days));

                    $ids[] = $watch->id;
                    $sent++;
                }

                if ($ids !== []) {
                    HackatonWatch::query()
                        ->whereIn('id', $ids)
                        ->update(['reminder_sent_at' => now()]);
                }
            });

        $this->info("Напоминаний о дедлайне отправлено: {$sent}.");

        return self::SUCCESS;
    }
}
