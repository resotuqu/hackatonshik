<?php

use App\Http\Controllers\HackatonAnnouncementController;
use App\Http\Controllers\HackatonApplicationController;
use App\Http\Controllers\HackatonCaseController;
use App\Http\Controllers\HackatonCaseFieldController;
use App\Http\Controllers\HackatonCaseScoreController;
use App\Http\Controllers\HackatonCaseSubmissionController;
use App\Http\Controllers\HackatonCertificateController;
use App\Http\Controllers\HackatonController;
use App\Http\Controllers\JudgeManagementController;
use App\Http\Controllers\PublicProfileController;
use App\Http\Controllers\TeamApplicationController;
use App\Http\Controllers\TeamController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

Route::livewire('/', 'pages::index')->name('home');

Route::livewire('/about', 'pages::about.index');
Route::livewire('/news', 'pages::news.index');
Route::livewire('/contacts', 'pages::contacts.index');
Route::livewire('/privacy-policy', 'pages::privacy-policy.index');
Route::livewire('/cookie-policy', 'pages::cookie-policy.index');

Route::livewire('/login', 'pages::auth.login');
Route::livewire('/register', 'pages::auth.register');
Route::livewire('/profile', 'pages::profile.index')->middleware('auth');
Route::livewire('/admin', 'pages::admin.index')->middleware(['auth', 'can:access-admin']);
Route::get('/u/{user:nickname}', [PublicProfileController::class, 'show'])->name('profile.public.show');

Route::livewire('/teams', 'pages::teams.index');
Route::livewire('/teams/create', 'pages::teams.create')->middleware('auth');
// Route::livewire('/teams/{team}', 'pages::teams.show');
// Route::livewire('/teams/{team}/edit', 'pages::teams.edit');
Route::livewire('/profile/teams', 'pages::profile.teams.index')->middleware('auth');

Route::livewire('/hackatons', 'pages::hackatons.index');
Route::livewire('/hackatons/create', 'pages::hackatons.create')->middleware('auth');
// Route::livewire('/hackatons/{hackaton}', 'pages::hackatons.show');
// Route::livewire('/hackatons/{hackaton}/edit', 'pages::hackatons.edit');
Route::livewire('/profile/hackatons', 'pages::profile.hackatons.index')->middleware('auth');
Route::livewire('/profile/hackatons/{hackaton}/participants', 'pages::profile.hackatons.participants')->middleware('auth');
Route::livewire('/profile/certificates', 'pages::profile.certificates.index')->middleware('auth');

Route::get('/teams/{team}', [TeamController::class, 'show'])
    ->name('teams.show');
Route::livewire('/teams/{team}/edit', 'pages::teams.edit')
    ->middleware('auth')
    ->name('teams.edit');

Route::get('/hackatons/{hackaton}', [HackatonController::class, 'show'])
    ->name('hackatons.show');
Route::livewire('/hackatons/{hackaton}/edit', 'pages::hackatons.edit')
    ->middleware('auth')
    ->name('hackatons.edit');

Route::get('/auth/yandex/redirect', function () {
    return Socialite::driver('yandex')->redirect();
});

Route::get('/auth/yandex/callback', function () {
    $yandexUser = Socialite::driver('yandex')->user();

    $email = $yandexUser->getEmail() ?: "yandex_{$yandexUser->getId()}@oauth.local";
    $name = $yandexUser->getName() ?: $yandexUser->getNickname() ?: 'Пользователь Яндекс';

    $user = User::firstOrCreate(
        ['email' => $email],
        [
            'fio' => $name,
            'nickname' => 'yandex_'.Str::lower(Str::random(10)),
            'date_of_birth' => now()->subYears(18)->toDateString(),
            'phone' => '+70000000000',
            'password' => Hash::make(Str::random(40)),
        ],
    );

    Auth::login($user);

    return redirect()->route('home');
});

Route::middleware('auth')->group(function () {
    Route::delete('/teams/{team}/roles/{teamRole}/participant', [TeamController::class, 'destroyParticipant'])
        ->name('teams.participants.destroy');

    Route::post('/team-applications', [TeamApplicationController::class, 'store'])->name('team.applications.store');
    Route::patch('/team-applications/{application}', [TeamApplicationController::class, 'update'])->name('team.applications.update');
    Route::delete('/team-applications/{application}', [TeamApplicationController::class, 'destroy'])->name('team.applications.destroy');

    Route::post('/hackaton-applications', [HackatonApplicationController::class, 'store'])->name('hackaton.applications.store');
    Route::patch('/hackaton-applications/{application}', [HackatonApplicationController::class, 'update'])->name('hackaton.applications.update');
    Route::patch('/hackatons/{hackaton}/applications/bulk', [HackatonApplicationController::class, 'bulkUpdate'])->name('hackaton.applications.bulk-update');
    Route::delete('/hackaton-applications/{application}', [HackatonApplicationController::class, 'destroy'])->name('hackaton.applications.destroy');

    Route::post('/hackatons/{hackaton}/cases', [HackatonCaseController::class, 'store'])->name('hackatons.cases.store');
    Route::delete('/hackatons/{hackaton}/cases/{case}', [HackatonCaseController::class, 'destroy'])->name('hackatons.cases.destroy');
    Route::post('/hackatons/{hackaton}/cases/{case}/fields', [HackatonCaseFieldController::class, 'store'])->name('hackatons.cases.fields.store');
    Route::delete('/hackatons/{hackaton}/cases/{case}/fields/{field}', [HackatonCaseFieldController::class, 'destroy'])->name('hackatons.cases.fields.destroy');
    Route::post('/hackatons/{hackaton}/cases/{case}/submissions', [HackatonCaseSubmissionController::class, 'store'])->name('hackatons.cases.submissions.store');
    Route::post('/hackatons/{hackaton}/scores', [HackatonCaseScoreController::class, 'store'])->name('hackatons.scores.store');

    Route::post('/hackatons/{hackaton}/announcements', [HackatonAnnouncementController::class, 'store'])->name('hackatons.announcements.store');
    Route::delete('/hackatons/{hackaton}/announcements/{announcement}', [HackatonAnnouncementController::class, 'destroy'])->name('hackatons.announcements.destroy');

    Route::post('/hackatons/{hackaton}/certificates', [HackatonCertificateController::class, 'store'])->name('hackatons.certificates.store');
    Route::delete('/hackatons/{hackaton}/certificates/{certificate}', [HackatonCertificateController::class, 'destroy'])->name('hackatons.certificates.destroy');
    Route::get('/certificates/{certificate}/download', [HackatonCertificateController::class, 'download'])->name('certificates.download');

    Route::post('/hackatons/{hackaton}/judges/invite', [JudgeManagementController::class, 'invite'])->name('hackatons.judges.invite');
    Route::post('/hackatons/{hackaton}/judges/assign', [JudgeManagementController::class, 'assign'])->name('hackatons.judges.assign');
    Route::delete('/hackatons/{hackaton}/judges/{judge}', [JudgeManagementController::class, 'unassign'])->name('hackatons.judges.unassign');
    Route::get('/judge-invitations/{token}/accept', [JudgeManagementController::class, 'accept'])->name('judges.invitations.accept');
});
