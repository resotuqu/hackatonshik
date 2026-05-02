<?php

use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\HackatonAnnouncementController;
use App\Http\Controllers\HackatonApplicationController;
use App\Http\Controllers\HackatonCaseController;
use App\Http\Controllers\HackatonCaseFieldController;
use App\Http\Controllers\HackatonCaseScoreController;
use App\Http\Controllers\HackatonCaseSubmissionController;
use App\Http\Controllers\HackatonCertificateController;
use App\Http\Controllers\HackatonController;
use App\Http\Controllers\HackatonExportController;
use App\Http\Controllers\JudgeManagementController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ParticipantHackatonHubController;
use App\Http\Controllers\PhoneVerificationController;
use App\Http\Controllers\PublicProfileController;
use App\Http\Controllers\TeamApplicationController;
use App\Http\Controllers\TeamController;
use Illuminate\Support\Facades\Route;

Route::livewire('/', 'pages::index')->name('home');

Route::livewire('/about', 'pages::about.index');
Route::livewire('/news', 'pages::news.index');
Route::livewire('/contacts', 'pages::contacts.index');
Route::livewire('/privacy-policy', 'pages::privacy-policy.index');
Route::livewire('/cookie-policy', 'pages::cookie-policy.index');

Route::livewire('/login', 'pages::auth.login');
Route::livewire('/register', 'pages::auth.register');
Route::livewire('/profile', 'pages::profile.index')->middleware(['auth', 'verified']);
Route::livewire('/admin', 'pages::admin.index')->middleware(['auth', 'verified', 'can:access-admin']);
Route::get('/u/{user:nickname}', [PublicProfileController::class, 'show'])->name('profile.public.show');

Route::livewire('/teams', 'pages::teams.index');
Route::livewire('/teams/create', 'pages::teams.create')->middleware(['auth', 'verified']);
// Route::livewire('/teams/{team}', 'pages::teams.show');
// Route::livewire('/teams/{team}/edit', 'pages::teams.edit');
Route::livewire('/profile/teams', 'pages::profile.teams.index')->middleware(['auth', 'verified']);

Route::livewire('/hackatons', 'pages::hackatons.index');
Route::livewire('/hackatons/create', 'pages::hackatons.create')->middleware(['auth', 'verified']);
// Route::livewire('/hackatons/{hackaton}', 'pages::hackatons.show');
// Route::livewire('/hackatons/{hackaton}/edit', 'pages::hackatons.edit');
Route::livewire('/profile/hackatons', 'pages::profile.hackatons.index')->middleware(['auth', 'verified']);
Route::livewire('/profile/hackatons/{hackaton}/participants', 'pages::profile.hackatons.participants')->middleware(['auth', 'verified']);
Route::livewire('/profile/certificates', 'pages::profile.certificates.index')->middleware(['auth', 'verified']);
Route::get('/profile/hackatons/{hackaton}/hub', [ParticipantHackatonHubController::class, 'show'])
    ->middleware(['auth', 'verified'])
    ->name('profile.hackatons.hub');

Route::get('/teams/{team}', [TeamController::class, 'show'])
    ->name('teams.show');
Route::livewire('/teams/{team}/edit', 'pages::teams.edit')
    ->middleware(['auth', 'verified'])
    ->name('teams.edit');

Route::get('/hackatons/{hackaton}', [HackatonController::class, 'show'])
    ->name('hackatons.show');
Route::livewire('/hackatons/{hackaton}/edit', 'pages::hackatons.edit')
    ->middleware(['auth', 'verified'])
    ->name('hackatons.edit');

Route::get('/auth/yandex/redirect', [SocialAuthController::class, 'redirect'])->defaults('provider', 'yandex');
Route::get('/auth/yandex/callback', [SocialAuthController::class, 'callback'])->defaults('provider', 'yandex');
Route::get('/auth/vk/redirect', [SocialAuthController::class, 'redirect'])->defaults('provider', 'vk');
Route::get('/auth/vk/callback', [SocialAuthController::class, 'callback'])->defaults('provider', 'vk');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/phone/verify', [PhoneVerificationController::class, 'notice'])->name('phone.verify.notice');
    Route::post('/phone/verify/send', [PhoneVerificationController::class, 'sendCode'])->name('phone.verify.send');
    Route::post('/phone/verify', [PhoneVerificationController::class, 'verify'])->name('phone.verify');

    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');

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
    Route::get('/hackatons/{hackaton}/export/teams', [HackatonExportController::class, 'teams'])->name('hackatons.export.teams');
    Route::get('/hackatons/{hackaton}/export/participants', [HackatonExportController::class, 'participants'])->name('hackatons.export.participants');
    Route::get('/hackatons/{hackaton}/export/documents-zip', [HackatonExportController::class, 'documentsZip'])->name('hackatons.export.documents-zip');

    Route::post('/hackatons/{hackaton}/judges/invite', [JudgeManagementController::class, 'invite'])->name('hackatons.judges.invite');
    Route::post('/hackatons/{hackaton}/judges/assign', [JudgeManagementController::class, 'assign'])->name('hackatons.judges.assign');
    Route::delete('/hackatons/{hackaton}/judges/{judge}', [JudgeManagementController::class, 'unassign'])->name('hackatons.judges.unassign');
    Route::get('/judge-invitations/{token}/accept', [JudgeManagementController::class, 'accept'])->name('judges.invitations.accept');
});
