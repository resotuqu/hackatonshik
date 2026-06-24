<?php

use App\Http\Controllers\HackatonAnnouncementController;
use App\Http\Controllers\HackatonApplicationController;
use App\Http\Controllers\HackatonCaseController;
use App\Http\Controllers\HackatonCaseFieldController;
use App\Http\Controllers\HackatonCaseScoreController;
use App\Http\Controllers\HackatonCaseSubmissionController;
use App\Http\Controllers\HackatonCertificateController;
use App\Http\Controllers\HackatonExportController;
use App\Http\Controllers\HackatonWatchController;
use App\Http\Controllers\JudgeManagementController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PhoneVerificationController;
use App\Http\Controllers\Profile\ExportAccountDataController;
use App\Http\Controllers\TeamApplicationController;
use App\Http\Controllers\TeamController;
use App\Livewire\Pages\About\Index as AboutIndex;
use App\Livewire\Pages\Contacts\Index as ContactsIndex;
use App\Livewire\Pages\CookiePolicy\Index as CookiePolicyIndex;
use App\Livewire\Pages\Hackatons\Create as HackatonsCreate;
use App\Livewire\Pages\Hackatons\Edit as HackatonsEdit;
use App\Livewire\Pages\Hackatons\Index as HackatonsIndex;
use App\Livewire\Pages\Hackatons\Results as HackatonsResults;
use App\Livewire\Pages\Hackatons\Show as HackatonsShow;
use App\Livewire\Pages\Home\Index as HomeIndex;
use App\Livewire\Pages\News\Index as NewsIndex;
use App\Livewire\Pages\News\Show as NewsShow;
use App\Livewire\Pages\Participant\Hackatons\Index as ParticipantHackatonsIndex;
use App\Livewire\Pages\PrivacyPolicy\Index as PrivacyPolicyIndex;
use App\Livewire\Pages\Profile\Certificates\Index as ProfileCertificatesIndex;
use App\Livewire\Pages\Profile\Hackatons\Hub as ProfileHackatonsHub;
use App\Livewire\Pages\Profile\Index as ProfileIndex;
use App\Livewire\Pages\Profile\PublicProfileDeleted;
use App\Livewire\Pages\Profile\PublicProfileShow;
use App\Livewire\Pages\Profile\Teams\Index as ProfileTeamsIndex;
use App\Livewire\Pages\Profile\Watches\Index as ProfileWatchesIndex;
use App\Livewire\Pages\Teams\Create as TeamsCreate;
use App\Livewire\Pages\Teams\Edit as TeamsEdit;
use App\Livewire\Pages\Teams\Index as TeamsIndex;
use App\Livewire\Pages\Teams\Show as TeamsShow;
use App\Livewire\Pages\Templates\Index as TemplatesIndex;
use App\Livewire\Pages\Templates\Show as TemplatesShow;
use App\Models\Hackaton;
use App\Models\NewsPost;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeIndex::class)->name('home');

Route::get('/about', AboutIndex::class);
Route::get('/news', NewsIndex::class)->name('news.index');
Route::get('/news/rss', function (): Response {
    $xml = Cache::remember('news-rss-feed', now()->addMinutes(10), function (): string {
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

        $channelTitle = e((string) config('app.rss_channel_title'));
        $channelDescription = e((string) config('app.rss_channel_description'));
        $channelLink = route('home');

        return <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0">
<channel>
  <title>{$channelTitle}</title>
  <link>{$channelLink}</link>
  <description>{$channelDescription}</description>
  {$items}
</channel>
</rss>
XML;
    });

    return response($xml, 200, [
        'Content-Type' => 'application/rss+xml; charset=UTF-8',
        'Cache-Control' => 'public, max-age=600',
    ]);
})->name('news.rss');
Route::get('/news/{post:slug}', NewsShow::class)->name('news.show');
Route::get('/contacts', ContactsIndex::class);
Route::get('/privacy-policy', PrivacyPolicyIndex::class);
Route::get('/cookie-policy', CookiePolicyIndex::class);

Route::get('/profile', ProfileIndex::class)->middleware(['auth', 'verified'])->name('profile');
Route::get('/profile/export', ExportAccountDataController::class)
    ->middleware(['auth', 'verified', 'throttle:exports'])
    ->name('profile.export');
// Filament admin panel handles /admin/* (see AdminPanelProvider)
Route::redirect('/admin/dashboard', '/admin')->name('admin.dashboard');
Route::get('/u/deleted/{user}', PublicProfileDeleted::class)->name('profile.public.deleted');
Route::get('/u/{user:nickname}', PublicProfileShow::class)->name('profile.public.show');

$organizerMiddleware = ['auth', 'verified', 'organizer'];
$participantMiddleware = ['auth', 'verified', 'participant'];
$authMiddleware = ['auth', 'verified'];

Route::get('/teams', TeamsIndex::class)->name('teams.index');
Route::get('/teams/create', TeamsCreate::class)->middleware($participantMiddleware)->name('teams.create');
Route::get('/profile/teams', ProfileTeamsIndex::class)->middleware($participantMiddleware)->name('profile.teams');

Route::get('/hackatons', HackatonsIndex::class)->name('hackatons.index');
Route::get('/templates', TemplatesIndex::class)->name('templates.index');
Route::get('/templates/{slug}', TemplatesShow::class)->name('templates.show');
Route::get('/hackatons/create', HackatonsCreate::class)->middleware($organizerMiddleware)->name('hackatons.create');

Route::get('/profile/hackatons', function () {
    $user = Auth::user();
    abort_unless($user !== null, 403);

    if ($user->isOrganizer()) {
        return redirect()->route('organizer.dashboard');
    }

    if ($user->isJudge()) {
        return redirect()->route('judge.dashboard');
    }

    if ($user->isModerator() || $user->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }

    return redirect()->route('participant.hackatons');
})->middleware($authMiddleware)->name('profile.hackatons');

Route::get('/participant/hackatons', ParticipantHackatonsIndex::class)->middleware($participantMiddleware)->name('participant.hackatons');
Route::get('/participant/hackatons/{hackaton}/hub', ProfileHackatonsHub::class)
    ->middleware($participantMiddleware)
    ->name('participant.hackatons.hub');
Route::get('/profile/hackatons/{hackaton}/hub', function (Hackaton $hackaton) {
    return redirect()->route('participant.hackatons.hub', $hackaton);
})->middleware($participantMiddleware)->name('profile.hackatons.hub');

Route::get('/profile/certificates', ProfileCertificatesIndex::class)->middleware($participantMiddleware)->name('profile.certificates');

Route::get('/profile/watches', ProfileWatchesIndex::class)->middleware(['auth', 'verified'])->name('profile.watches');

Route::get('/teams/{team}', TeamsShow::class)->name('teams.show');
Route::get('/teams/{team}/edit', TeamsEdit::class)
    ->middleware($participantMiddleware)
    ->name('teams.edit');

Route::get('/hackatons/{hackaton}', HackatonsShow::class)->name('hackatons.show');
Route::get('/hackatons/{hackaton}/results', HackatonsResults::class)->name('hackatons.results');
Route::get('/hackatons/{hackaton}/edit', HackatonsEdit::class)
    ->middleware($organizerMiddleware)
    ->name('hackatons.edit');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/phone/verify', [PhoneVerificationController::class, 'notice'])->name('phone.verify.notice');
    Route::post('/phone/verify/phone', [PhoneVerificationController::class, 'storePhone'])->name('phone.verify.phone');
    Route::post('/phone/verify/send', [PhoneVerificationController::class, 'sendCode'])->name('phone.verify.send');
    Route::post('/phone/verify', [PhoneVerificationController::class, 'verify'])->name('phone.verify');

    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])
        ->middleware('throttle:notifications')
        ->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])
        ->middleware('throttle:notifications')
        ->name('notifications.read-all');

    Route::delete('/teams/{team}/roles/{teamRole}/participant', [TeamController::class, 'destroyParticipant'])
        ->name('teams.participants.destroy');

    Route::post('/team-applications', [TeamApplicationController::class, 'store'])
        ->middleware('throttle:applications')
        ->name('team.applications.store');
    Route::patch('/team-applications/{application}', [TeamApplicationController::class, 'update'])->name('team.applications.update');
    Route::delete('/team-applications/{application}', [TeamApplicationController::class, 'destroy'])->name('team.applications.destroy');

    Route::post('/hackaton-watches/{hackaton}', [HackatonWatchController::class, 'store'])->name('hackaton.watches.store');
    Route::delete('/hackaton-watches/{hackaton}', [HackatonWatchController::class, 'destroy'])->name('hackaton.watches.destroy');

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
    Route::post('/hackatons/{hackaton}/cases/{case}/fields/preview', [HackatonCaseFieldController::class, 'preview'])
        ->middleware('throttle:creations')
        ->name('hackatons.cases.fields.preview');
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
    Route::get('/hackatons/{hackaton}/export/applications', [HackatonExportController::class, 'applications'])
        ->middleware('throttle:exports')
        ->name('hackatons.export.applications');
    Route::get('/hackatons/{hackaton}/export/results', [HackatonExportController::class, 'results'])
        ->middleware('throttle:exports')
        ->name('hackatons.export.results');

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

require __DIR__.'/auth.php';
require __DIR__.'/organizer.php';
require __DIR__.'/judge.php';
