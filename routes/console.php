<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('hackatons:sync-statuses')
    ->everyTenMinutes()
    ->withoutOverlapping();

Schedule::command('hackatons:send-deadline-reminders --days=3')
    ->dailyAt('08:00')
    ->withoutOverlapping()
    ->runInBackground();

Schedule::command('horizon:snapshot')->everyFiveMinutes();

Schedule::command('queue:prune-failed --hours=168')->daily();

Schedule::command('pulse:check')->everyMinute();
