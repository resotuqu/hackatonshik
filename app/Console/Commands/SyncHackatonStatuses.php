<?php

namespace App\Console\Commands;

use App\Models\Hackaton;
use App\Models\HackatonCase;
use App\Models\User;
use App\Notifications\CaseDeadlineReminder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;

class SyncHackatonStatuses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hackatons:sync-statuses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize hackaton status by timeline.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $updated = 0;
        $deadlineRemindersSent = 0;

        Hackaton::query()
            ->select(['id', 'is_public', 'start_at', 'end_at', 'status'])
            ->chunkById(100, function ($hackatons) use (&$updated): void {
                foreach ($hackatons as $hackaton) {
                    if ($hackaton->syncStatusByTimeline()) {
                        $updated++;
                    }
                }
            });

        HackatonCase::query()
            ->with(['hackaton.teams.roles'])
            ->where('is_published', true)
            ->whereBetween('deadline_at', [now(), now()->addDay()])
            ->chunkById(50, function ($cases) use (&$deadlineRemindersSent): void {
                foreach ($cases as $case) {
                    $participantIds = $case->hackaton->teams
                        ->flatMap(fn ($team) => collect([$team->user_id])->merge($team->roles->pluck('user_id')))
                        ->filter()
                        ->unique()
                        ->values();

                    if ($participantIds->isEmpty()) {
                        continue;
                    }

                    $users = User::query()->whereIn('id', $participantIds)->get();

                    foreach ($users as $user) {
                        $cacheKey = "case-deadline-reminder:{$case->id}:{$user->id}:".now()->toDateString();
                        if (! Cache::add($cacheKey, true, now()->endOfDay())) {
                            continue;
                        }

                        $deadlineRemindersSent++;
                        Notification::send($user, new CaseDeadlineReminder($case));
                    }
                }
            });

        $this->info("Синхронизация завершена. Обновлено статусов: {$updated}. Напоминаний отправлено: {$deadlineRemindersSent}.");

        return self::SUCCESS;
    }
}
