<?php

declare(strict_types=1);

use App\Http\Controllers\OrganizerAnalyticsExportController;
use App\Livewire\Organizer\Dashboard as OrganizerDashboard;
use App\Livewire\Pages\Profile\Hackatons\Applications as ProfileHackatonsApplications;
use App\Livewire\Pages\Profile\Hackatons\Finished as ProfileHackatonsFinished;
use App\Livewire\Pages\Profile\Hackatons\Participants as ProfileHackatonsParticipants;
use App\Livewire\Pages\Profile\Hackatons\Scoring as ProfileHackatonsScoring;
use App\Models\Hackaton;
use Illuminate\Support\Facades\Route;

$organizerMiddleware = ['auth', 'verified', 'organizer'];

Route::redirect('/profile/organizer', '/organizer')->middleware($organizerMiddleware)->name('profile.organizer');
Route::get('/organizer', OrganizerDashboard::class)->middleware($organizerMiddleware)->name('organizer.dashboard');
Route::get('/organizer/analytics/export', OrganizerAnalyticsExportController::class)
    ->middleware(['throttle:exports', ...$organizerMiddleware])
    ->name('organizer.analytics.export');
Route::get('/organizer/applications', ProfileHackatonsApplications::class)->middleware($organizerMiddleware)->name('organizer.applications');
Route::get('/organizer/scoring', ProfileHackatonsScoring::class)->middleware($organizerMiddleware)->name('organizer.scoring');
Route::get('/organizer/finished', ProfileHackatonsFinished::class)->middleware($organizerMiddleware)->name('organizer.finished');
Route::get('/organizer/hackatons/{hackaton}/participants', ProfileHackatonsParticipants::class)->middleware($organizerMiddleware)->name('organizer.participants');

Route::redirect('/profile/hackatons/applications', '/organizer/applications')->middleware($organizerMiddleware)->name('profile.hackatons.applications');
Route::redirect('/profile/hackatons/scoring', '/organizer/scoring')->middleware($organizerMiddleware)->name('profile.hackatons.scoring');
Route::redirect('/profile/hackatons/finished', '/organizer/finished')->middleware($organizerMiddleware)->name('profile.hackatons.finished');
Route::get('/profile/hackatons/{hackaton}/participants', function (Hackaton $hackaton) {
    return redirect()->route('organizer.participants', $hackaton);
})->middleware($organizerMiddleware)->name('profile.hackatons.participants');
