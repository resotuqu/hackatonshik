<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Hackaton;
use App\Models\HackatonCase;
use App\Models\Team;
use App\Models\User;
use App\Notifications\CaseDeadlineReminder;
use App\Notifications\HackatonWatchStartReminder;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;

class SyncHackatonStatuses extends Command
{
    /**
     * @var string
     */
    protected $signature = 'hackatons:sync-statuses';

    /**
     * @var string
     */
    protected $description = 'Synchronize hackaton status by timeline.';

    public function handle(): int
    {
        $updated = 0;
        $deadlineRemindersSent = 0;
        $watchStartRemindersSent = 0;

        Hackaton::query()
            ->select(['id', 'is_public', 'start_at', 'end_at', 'status', 'registration_deadline_at'])
            ->chunkById(100, function ($hackatons) use (&$updated): void {
                foreach ($hackatons as $hackaton) {
                    if ($hackaton->syncStatusByTimeline()) {
                        $updated++;
                    }
                }
            });

        HackatonCase::query()
            ->select(['id', 'hackaton_id', 'deadline_at', 'title', 'is_published'])
            ->where('is_published', true)
            ->whereBetween('deadline_at', [now(), now()->addDay()])
            ->chunkById(50, function ($cases) use (&$deadlineRemindersSent): void {
                /** @var Collection<int, int> $hackatonIds */
                $hackatonIds = $cases->pluck('hackaton_id')->unique()->values();
                $participantIdsByHackaton = $this->resolveParticipantIdsByHackatonIds($hackatonIds);

                foreach ($cases as $case) {
                    $participantIds = $participantIdsByHackaton[$case->hackaton_id] ?? collect();

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

        $watchStartRemindersSent += $this->sendHackatonWatchStartReminders();

        $this->info("Синхронизация завершена. Обновлено статусов: {$updated}. Напоминаний о дедлайнах: {$deadlineRemindersSent}. Напоминаний о старте: {$watchStartRemindersSent}.");

        return self::SUCCESS;
    }

    /**
     * @param  Collection<int, int>  $hackatonIds
     * @return array<int, Collection<int, int>>
     */
    private function resolveParticipantIdsByHackatonIds(Collection $hackatonIds): array
    {
        if ($hackatonIds->isEmpty()) {
            return [];
        }

        $teams = Team::query()
            ->whereIn('hackaton_id', $hackatonIds)
            ->with('roles:id,team_id,user_id')
            ->get(['id', 'hackaton_id', 'user_id']);

        $byHackaton = [];

        foreach ($teams as $team) {
            $ids = collect([$team->user_id])
                ->merge($team->roles->pluck('user_id'))
                ->filter()
                ->unique()
                ->values();

            if (! isset($byHackaton[$team->hackaton_id])) {
                $byHackaton[$team->hackaton_id] = collect();
            }

            $byHackaton[$team->hackaton_id] = $byHackaton[$team->hackaton_id]
                ->merge($ids)
                ->unique()
                ->values();
        }

        return $byHackaton;
    }

    private function sendHackatonWatchStartReminders(): int
    {
        $sent = 0;
        $reminderDays = [7, 1];

        foreach ($reminderDays as $days) {
            $windowStart = now()->addDays($days)->startOfDay();
            $windowEnd = now()->addDays($days)->endOfDay();

            Hackaton::query()
                ->where('is_public', true)
                ->whereBetween('start_at', [$windowStart, $windowEnd])
                ->select(['id', 'title', 'start_at'])
                ->chunkById(50, function ($hackatons) use ($days, &$sent): void {
                    foreach ($hackatons as $hackaton) {
                        $watchers = User::query()
                            ->whereHas('watchedHackatons', fn ($query) => $query->where('hackatons.id', $hackaton->id))
                            ->get();

                        foreach ($watchers as $watcher) {
                            $cacheKey = "hackaton-watch-start:{$hackaton->id}:{$watcher->id}:{$days}";
                            if (! Cache::add($cacheKey, true, Carbon::parse((string) $hackaton->start_at)->addDay())) {
                                continue;
                            }

                            $sent++;
                            Notification::send($watcher, new HackatonWatchStartReminder($hackaton, $days));
                        }
                    }
                });
        }

        return $sent;
    }
}
