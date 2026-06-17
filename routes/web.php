<?php

use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\HackatonAnnouncementController;
use App\Http\Controllers\HackatonApplicationController;
use App\Http\Controllers\HackatonCaseController;
use App\Http\Controllers\HackatonCaseFieldController;
use App\Http\Controllers\HackatonCaseScoreController;
use App\Http\Controllers\HackatonCaseSubmissionController;
use App\Http\Controllers\HackatonCertificateController;
use App\Http\Controllers\HackatonExportController;
use App\Http\Controllers\JudgeManagementController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PhoneVerificationController;
use App\Http\Controllers\TeamApplicationController;
use App\Http\Controllers\TeamController;
use App\Livewire\Organizer\Dashboard as OrganizerDashboard;
use App\Livewire\Pages\About\Index as AboutIndex;
use App\Livewire\Pages\Admin\AvatarPresets as AdminAvatarPresets;
use App\Livewire\Pages\Admin\Index as AdminIndex;
use App\Livewire\Pages\Auth\Login as AuthLogin;
use App\Livewire\Pages\Auth\Register as AuthRegister;
use App\Livewire\Pages\Contacts\Index as ContactsIndex;
use App\Livewire\Pages\CookiePolicy\Index as CookiePolicyIndex;
use App\Livewire\Pages\Hackatons\Create as HackatonsCreate;
use App\Livewire\Pages\Hackatons\Edit as HackatonsEdit;
use App\Livewire\Pages\Hackatons\Index as HackatonsIndex;
use App\Livewire\Pages\Hackatons\Show as HackatonsShow;
use App\Livewire\Pages\Home\Index as HomeIndex;
use App\Livewire\Pages\Judge\Dashboard as JudgeDashboard;
use App\Livewire\Pages\Judge\EvaluateSubmission as JudgeEvaluateSubmission;
use App\Livewire\Pages\Judge\HackatonShow as JudgeHackatonShow;
use App\Livewire\Pages\Judge\SubmissionList as JudgeSubmissionList;
use App\Livewire\Pages\News\Index as NewsIndex;
use App\Livewire\Pages\News\Show as NewsShow;
use App\Livewire\Pages\PrivacyPolicy\Index as PrivacyPolicyIndex;
use App\Livewire\Pages\Profile\Certificates\Index as ProfileCertificatesIndex;
use App\Livewire\Pages\Profile\Hackatons\Applications as ProfileHackatonsApplications;
use App\Livewire\Pages\Profile\Hackatons\Finished as ProfileHackatonsFinished;
use App\Livewire\Pages\Profile\Hackatons\Hub as ProfileHackatonsHub;
use App\Livewire\Pages\Profile\Hackatons\Participants as ProfileHackatonsParticipants;
use App\Livewire\Pages\Profile\Hackatons\Scoring as ProfileHackatonsScoring;
use App\Livewire\Pages\Profile\Index as ProfileIndex;
use App\Livewire\Pages\Profile\PublicProfileShow;
use App\Livewire\Pages\Profile\Teams\Index as ProfileTeamsIndex;
use App\Livewire\Pages\Teams\Create as TeamsCreate;
use App\Livewire\Pages\Teams\Edit as TeamsEdit;
use App\Livewire\Pages\Teams\Index as TeamsIndex;
use App\Livewire\Pages\Teams\Show as TeamsShow;
use App\Models\NewsPost;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeIndex::class)->name('home');

Route::get('/about', AboutIndex::class);
Route::get('/news', NewsIndex::class);
Route::get('/news/rss', function (): Response {
    $posts = NewsPost::query()
        ->published()
        ->latest('published_at')
        ->limit(50)
        ->get(['title', 'slug', 'excerpt', 'published_at']);

    $items = $posts->map(function (NewsPost $post): string {
        $title = e($post->title);
        $link = route('news.show', ['post' => $post->slug]);
        $description = e((string) ($post->excerpt ?? ''));
        $pubDate = Carbon::parse((string) $post->published_at)->toRfc2822String();

        return <<<XML
<item>
  <title>{$title}</title>
  <link>{$link}</link>
  <guid>{$link}</guid>
  <description>{$description}</description>
  <pubDate>{$pubDate}</pubDate>
</item>
XML;
    })->implode("\n");

    $xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0">
<channel>
  <title>Herd News</title>
  <link>{route('home')}</link>
  <description>Новости платформы Herd</description>
  {$items}
</channel>
</rss>
XML;

    return response($xml, 200, ['Content-Type' => 'application/rss+xml; charset=UTF-8']);
})->name('news.rss');
Route::get('/news/{post:slug}', NewsShow::class)->name('news.show');
Route::get('/contacts', ContactsIndex::class);
Route::get('/privacy-policy', PrivacyPolicyIndex::class);
Route::get('/cookie-policy', CookiePolicyIndex::class);

Route::get('/login', AuthLogin::class)->name('login');
Route::get('/register', AuthRegister::class)->name('register');
Route::get('/profile', ProfileIndex::class)->middleware(['auth', 'verified'])->name('profile');
Route::get('/admin', AdminIndex::class)->middleware(['auth', 'verified', 'can:access-admin']);
Route::get('/admin/avatar-presets', AdminAvatarPresets::class)->middleware(['auth', 'verified', 'can:access-admin']);
Route::get('/u/{user:nickname}', PublicProfileShow::class)->name('profile.public.show');

Route::get('/teams', TeamsIndex::class)->name('teams.index');
Route::get('/teams/create', TeamsCreate::class)->middleware(['auth', 'verified'])->name('teams.create');
Route::get('/profile/teams', ProfileTeamsIndex::class)->middleware(['auth', 'verified'])->name('profile.teams');

Route::get('/hackatons', HackatonsIndex::class)->name('hackatons.index');
Route::get('/hackatons/create', HackatonsCreate::class)->middleware(['auth', 'verified', 'organizer'])->name('hackatons.create');

$organizerMiddleware = ['auth', 'verified', 'organizer'];

Route::redirect('/profile/hackatons', '/organizer')->middleware($organizerMiddleware)->name('profile.hackatons');
Route::redirect('/profile/organizer', '/organizer')->middleware($organizerMiddleware)->name('profile.organizer');
Route::get('/organizer', OrganizerDashboard::class)->middleware($organizerMiddleware)->name('organizer.dashboard');
Route::get('/profile/hackatons/applications', ProfileHackatonsApplications::class)->middleware($organizerMiddleware)->name('profile.hackatons.applications');
Route::get('/profile/hackatons/scoring', ProfileHackatonsScoring::class)->middleware($organizerMiddleware)->name('profile.hackatons.scoring');
Route::get('/profile/hackatons/finished', ProfileHackatonsFinished::class)->middleware($organizerMiddleware)->name('profile.hackatons.finished');
Route::get('/profile/hackatons/{hackaton}/participants', ProfileHackatonsParticipants::class)->middleware($organizerMiddleware)->name('profile.hackatons.participants');
Route::get('/profile/certificates', ProfileCertificatesIndex::class)->middleware(['auth', 'verified'])->name('profile.certificates');
Route::get('/profile/hackatons/{hackaton}/hub', ProfileHackatonsHub::class)
    ->middleware(['auth', 'verified'])
    ->name('profile.hackatons.hub');

Route::get('/teams/{team}', TeamsShow::class)->name('teams.show');
Route::get('/teams/{team}/edit', TeamsEdit::class)
    ->middleware(['auth', 'verified'])
    ->name('teams.edit');

Route::get('/hackatons/{hackaton}', HackatonsShow::class)->name('hackatons.show');
Route::get('/hackatons/{hackaton}/edit', HackatonsEdit::class)
    ->middleware(['auth', 'verified'])
    ->name('hackatons.edit');

Route::get('/auth/yandex/redirect', [SocialAuthController::class, 'redirect'])->defaults('provider', 'yandex');
Route::get('/auth/yandex/callback', [SocialAuthController::class, 'callback'])->defaults('provider', 'yandex');
Route::get('/auth/vk/redirect', [SocialAuthController::class, 'redirect'])->defaults('provider', 'vk');
Route::get('/auth/vk/callback', [SocialAuthController::class, 'callback'])->defaults('provider', 'vk');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/judge', JudgeDashboard::class)->name('judge.dashboard');
    Route::get('/judge/hackatons/{hackaton}', JudgeHackatonShow::class)->name('judge.hackatons.show');
    Route::get('/judge/hackatons/{hackaton}/cases/{case}', JudgeSubmissionList::class)->name('judge.cases.submissions');
    Route::get('/judge/submissions/{submission}', JudgeEvaluateSubmission::class)->name('judge.submissions.evaluate');

    Route::get('/phone/verify', [PhoneVerificationController::class, 'notice'])->name('phone.verify.notice');
    Route::post('/phone/verify/send', [PhoneVerificationController::class, 'sendCode'])->name('phone.verify.send');
    Route::post('/phone/verify', [PhoneVerificationController::class, 'verify'])->name('phone.verify');

    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');

    Route::delete('/teams/{team}/roles/{teamRole}/participant', [TeamController::class, 'destroyParticipant'])
        ->name('teams.participants.destroy');

    Route::post('/team-applications', [TeamApplicationController::class, 'store'])
        ->middleware('throttle:applications')
        ->name('team.applications.store');
    Route::patch('/team-applications/{application}', [TeamApplicationController::class, 'update'])->name('team.applications.update');
    Route::delete('/team-applications/{application}', [TeamApplicationController::class, 'destroy'])->name('team.applications.destroy');

    Route::post('/hackaton-applications', [HackatonApplicationController::class, 'store'])
        ->middleware('throttle:applications')
        ->name('hackaton.applications.store');
    Route::patch('/hackaton-applications/{application}', [HackatonApplicationController::class, 'update'])->name('hackaton.applications.update');
    Route::patch('/hackatons/{hackaton}/applications/bulk', [HackatonApplicationController::class, 'bulkUpdate'])
        ->middleware('throttle:bulk-actions')
        ->name('hackaton.applications.bulk-update');
    Route::delete('/hackaton-applications/{application}', [HackatonApplicationController::class, 'destroy'])->name('hackaton.applications.destroy');

    Route::post('/hackatons/{hackaton}/cases/{case}/join', [HackatonCaseController::class, 'join'])
        ->middleware('throttle:creations')
        ->name('hackatons.cases.join');
    Route::post('/hackatons/{hackaton}/cases', [HackatonCaseController::class, 'store'])
        ->middleware('throttle:creations')
        ->name('hackatons.cases.store');
    Route::delete('/hackatons/{hackaton}/cases/{case}', [HackatonCaseController::class, 'destroy'])->name('hackatons.cases.destroy');
    Route::post('/hackatons/{hackaton}/cases/{case}/fields', [HackatonCaseFieldController::class, 'store'])
        ->middleware('throttle:creations')
        ->name('hackatons.cases.fields.store');
    Route::delete('/hackatons/{hackaton}/cases/{case}/fields/{field}', [HackatonCaseFieldController::class, 'destroy'])->name('hackatons.cases.fields.destroy');
    Route::patch('/hackatons/{hackaton}/cases/{case}/fields/reorder', [HackatonCaseFieldController::class, 'reorder'])
        ->middleware('throttle:creations')
        ->name('hackatons.cases.fields.reorder');
    Route::post('/hackatons/{hackaton}/cases/{case}/submissions', [HackatonCaseSubmissionController::class, 'store'])
        ->middleware('throttle:creations')
        ->name('hackatons.cases.submissions.store');
    Route::post('/hackatons/{hackaton}/scores', [HackatonCaseScoreController::class, 'store'])
        ->middleware('throttle:creations')
        ->name('hackatons.scores.store');

    Route::post('/hackatons/{hackaton}/announcements', [HackatonAnnouncementController::class, 'store'])
        ->middleware('throttle:creations')
        ->name('hackatons.announcements.store');
    Route::delete('/hackatons/{hackaton}/announcements/{announcement}', [HackatonAnnouncementController::class, 'destroy'])->name('hackatons.announcements.destroy');

    Route::post('/hackatons/{hackaton}/certificates', [HackatonCertificateController::class, 'store'])
        ->middleware('throttle:creations')
        ->name('hackatons.certificates.store');
    Route::delete('/hackatons/{hackaton}/certificates/{certificate}', [HackatonCertificateController::class, 'destroy'])->name('hackatons.certificates.destroy');
    Route::get('/certificates/{certificate}/download', [HackatonCertificateController::class, 'download'])->name('certificates.download');
    Route::get('/hackatons/{hackaton}/export/teams', [HackatonExportController::class, 'teams'])
        ->middleware('throttle:exports')
        ->name('hackatons.export.teams');
    Route::get('/hackatons/{hackaton}/export/participants', [HackatonExportController::class, 'participants'])
        ->middleware('throttle:exports')
        ->name('hackatons.export.participants');
    Route::get('/hackatons/{hackaton}/export/documents-zip', [HackatonExportController::class, 'documentsZip'])
        ->middleware('throttle:exports')
        ->name('hackatons.export.documents-zip');

    Route::post('/hackatons/{hackaton}/judges/invite', [JudgeManagementController::class, 'invite'])
        ->middleware('throttle:judge-management')
        ->name('hackatons.judges.invite');
    Route::post('/hackatons/{hackaton}/judges/assign', [JudgeManagementController::class, 'assign'])
        ->middleware('throttle:judge-management')
        ->name('hackatons.judges.assign');
    Route::delete('/hackatons/{hackaton}/judges/{judge}', [JudgeManagementController::class, 'unassign'])->name('hackatons.judges.unassign');
    Route::get('/judge-invitations/{token}', [JudgeManagementController::class, 'showAccept'])->name('judges.invitations.accept');
    Route::post('/judge-invitations/{token}/accept', [JudgeManagementController::class, 'accept'])->name('judges.invitations.accept.store');
});
