<?php

namespace App\Providers;

use App\Events\HackatonApplicationChanged;
use App\Events\TeamApplicationChanged;
use App\Listeners\InvalidateHomeCaches;
use App\Mail\VerifyEmailAddressMail;
use App\Models\Hackaton;
use App\Models\HackatonAnnouncement;
use App\Models\HackatonApplication;
use App\Models\HackatonCase;
use App\Models\HackatonCaseSubmission;
use App\Models\HackatonCertificate;
use App\Models\Team;
use App\Models\TeamApplication;
use App\Models\User;
use App\Policies\HackatonAnnouncementPolicy;
use App\Policies\HackatonApplicationPolicy;
use App\Policies\HackatonCasePolicy;
use App\Policies\HackatonCaseSubmissionPolicy;
use App\Policies\HackatonCertificatePolicy;
use App\Policies\HackatonPolicy;
use App\Policies\TeamApplicationPolicy;
use App\Policies\TeamPolicy;
use Carbon\CarbonImmutable;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use SocialiteProviders\Manager\SocialiteWasCalled;
use SocialiteProviders\VKontakte\Provider as VkontakteProvider;
use SocialiteProviders\Yandex\Provider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $mailViews = resource_path('views/vendor/mail');
        View::addNamespace('mail', is_dir($mailViews)
            ? $mailViews
            : base_path('vendor/laravel/framework/src/Illuminate/Mail/resources/views'));

        // Must match `signed:relative` middleware: hash is built from the relative path. Prepend APP_URL so HTML mail
        // clients do not resolve `/email/verify/...` against the mail catcher host (e.g. localhost:8025).
        VerifyEmail::createUrlUsing(function ($notifiable): string {
            $signedRelative = URL::temporarySignedRoute(
                'verification.verify',
                now()->addMinutes((int) Config::get('auth.verification.expire', 60)),
                [
                    'id' => $notifiable->getKey(),
                    'hash' => sha1($notifiable->getEmailForVerification()),
                ],
                false,
            );

            return rtrim((string) Config::get('app.url'), '/').$signedRelative;
        });

        VerifyEmail::toMailUsing(fn (User $notifiable, string $url): VerifyEmailAddressMail => (new VerifyEmailAddressMail(
            $notifiable,
            $url,
        ))->locale('ru'));

        $this->configureDefaults();
        $this->configureRateLimiting();
        $this->configureCatalogCacheInvalidation();
        Event::listen(function (SocialiteWasCalled $event) {
            $event->extendSocialite('yandex', Provider::class);
            $event->extendSocialite('vkontakte', VkontakteProvider::class);
        });
        Event::listen(HackatonApplicationChanged::class, InvalidateHomeCaches::class);
        Event::listen(TeamApplicationChanged::class, InvalidateHomeCaches::class);

        Gate::policy(Hackaton::class, HackatonPolicy::class);
        Gate::policy(Team::class, TeamPolicy::class);
        Gate::policy(TeamApplication::class, TeamApplicationPolicy::class);
        Gate::policy(HackatonApplication::class, HackatonApplicationPolicy::class);
        Gate::policy(HackatonCase::class, HackatonCasePolicy::class);
        Gate::policy(HackatonCaseSubmission::class, HackatonCaseSubmissionPolicy::class);
        Gate::policy(HackatonAnnouncement::class, HackatonAnnouncementPolicy::class);
        Gate::policy(HackatonCertificate::class, HackatonCertificatePolicy::class);
        Gate::define('access-admin', fn ($user): bool => $user->isAdmin());
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(
            fn (): ?Password => app()->isProduction()
                ? Password::min(12)
                    ->mixedCase()
                    ->letters()
                    ->numbers()
                    ->symbols()
                    ->uncompromised()
                : null,
        );
    }

    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', fn (Request $request) => Limit::perMinute(120)->by($request->ip()));

        RateLimiter::for('bulk-actions', fn (Request $request) => Limit::perMinute(15)->by($request->user()?->id ?: $request->ip()));

        RateLimiter::for('exports', fn (Request $request) => Limit::perMinute(8)->by($request->user()?->id ?: $request->ip()));

        RateLimiter::for('creations', fn (Request $request) => Limit::perMinute(5)->by($request->user()?->id ?: $request->ip()));

        RateLimiter::for('applications', fn (Request $request) => Limit::perMinute(10)->by($request->user()?->id ?: $request->ip()));

        RateLimiter::for('password-reset', function (Request $request) {
            return Limit::perMinute(3)->by($request->input('email') ?: $request->ip());
        });
    }

    protected function configureCatalogCacheInvalidation(): void
    {
        $cache = Cache::supportsTags() ? Cache::tags(['catalog']) : Cache::store();
        $bumpCatalogVersion = static function (): void {
            $cacheStore = Cache::supportsTags() ? Cache::tags(['catalog', 'catalog:hackatons']) : Cache::store();
            $cacheStore->add('api:v1:catalog:hackatons:version', 1);
            $cacheStore->increment('api:v1:catalog:hackatons:version');
            $cacheStore = Cache::supportsTags() ? Cache::tags(['catalog', 'catalog:teams']) : Cache::store();
            $cacheStore->add('api:v1:catalog:teams:version', 1);
            $cacheStore->increment('api:v1:catalog:teams:version');
            $cacheStore = Cache::supportsTags() ? Cache::tags(['catalog', 'catalog:profiles']) : Cache::store();
            $cacheStore->add('api:v1:catalog:profiles:version', 1);
            $cacheStore->increment('api:v1:catalog:profiles:version');
        };

        $refreshCatalogVersion = static function (Hackaton $hackaton) use ($bumpCatalogVersion): void {
            if (
                ! $hackaton->wasRecentlyCreated
                && ! $hackaton->wasChanged(['title', 'is_public', 'start_at', 'end_at', 'image_url'])
            ) {
                return;
            }

            $bumpCatalogVersion();
        };

        Hackaton::saved($refreshCatalogVersion);
        Hackaton::deleted(static function () use ($bumpCatalogVersion): void {
            $bumpCatalogVersion();
        });
        Team::saved(static function () use ($bumpCatalogVersion): void {
            $bumpCatalogVersion();
        });
        Team::deleted(static function () use ($bumpCatalogVersion): void {
            $bumpCatalogVersion();
        });
        User::saved(static function (User $user) use ($cache): void {
            if ($user->wasChanged(['nickname', 'role', 'description', 'is_profile_public'])) {
                $cache->forget("profile:public-show:{$user->id}:v2");
            }
        });
        User::deleted(static function (User $user) use ($cache): void {
            $cache->forget("profile:public-show:{$user->id}:v2");
        });
    }
}
