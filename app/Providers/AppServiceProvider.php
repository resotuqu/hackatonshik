<?php

namespace App\Providers;

use App\Events\HackatonApplicationChanged;
use App\Events\TeamApplicationChanged;
use App\Listeners\BroadcastNewNotification;
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
use App\Policies\UserPolicy;
use App\Support\OAuthRedirectUris;
use Carbon\CarbonImmutable;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\SQLiteConnection;
use Illuminate\Foundation\Events\DiagnosingHealth;
use Illuminate\Http\Request;
use Illuminate\Notifications\Events\NotificationSent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Laravel\Horizon\Contracts\MasterSupervisorRepository;
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
        $this->registerCaseInsensitiveSearch();

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

        Config::set('livewire.temporary_file_upload.rules', ['required', 'file', 'max:10240']);

        $this->configureDefaults();
        $this->configureOAuthRedirectUris();
        $this->configureRateLimiting();
        $this->configureCatalogCacheInvalidation();
        $this->configureHealthChecks();
        Event::listen(function (SocialiteWasCalled $event) {
            $event->extendSocialite('yandex', Provider::class);
            $event->extendSocialite('vkontakte', VkontakteProvider::class);
        });
        Event::listen(HackatonApplicationChanged::class, InvalidateHomeCaches::class);
        Event::listen(TeamApplicationChanged::class, InvalidateHomeCaches::class);
        Event::listen(NotificationSent::class, BroadcastNewNotification::class);

        View::composer('layouts.app', function ($view): void {
            $user = Auth::user();
            if ($user instanceof User && $user->isOrganizer()) {
                $view->with('partnerSidebarCounts', PartnerSidebarCounts::forUser($user));

                return;
            }

            $view->with('partnerSidebarCounts', null);
        });

        Gate::policy(Hackaton::class, HackatonPolicy::class);
        Gate::policy(Team::class, TeamPolicy::class);
        Gate::policy(TeamApplication::class, TeamApplicationPolicy::class);
        Gate::policy(HackatonApplication::class, HackatonApplicationPolicy::class);
        Gate::policy(HackatonCase::class, HackatonCasePolicy::class);
        Gate::policy(HackatonCaseSubmission::class, HackatonCaseSubmissionPolicy::class);
        Gate::policy(HackatonAnnouncement::class, HackatonAnnouncementPolicy::class);
        Gate::policy(HackatonCertificate::class, HackatonCertificatePolicy::class);
        Gate::policy(User::class, UserPolicy::class);
        Gate::define('access-admin', fn ($user): bool => $user->isAdmin());
        Gate::define('viewPulse', fn ($user): bool => $user->isAdmin());
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        if (config('app.force_https')) {
            URL::forceScheme('https');
        }

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

    protected function configureOAuthRedirectUris(): void
    {
        $this->app->booted(function (): void {
            Config::set('services.yandex.redirect', OAuthRedirectUris::yandexCallback());
            Config::set('services.vkontakte.redirect', OAuthRedirectUris::vkCallback());
        });
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

        RateLimiter::for('judge-management', fn (Request $request) => Limit::perMinute(10)->by($request->user()?->id ?: $request->ip()));

        RateLimiter::for('notifications', fn (Request $request) => Limit::perMinute(30)->by($request->user()?->id ?: $request->ip()));
    }

    protected function configureHealthChecks(): void
    {
        Event::listen(DiagnosingHealth::class, function (): void {
            if (config('cache.default') === 'redis' || config('session.driver') === 'redis') {
                Redis::connection()->ping();
            }

            // Queue workers are monitored separately (Horizon); liveness should not depend on them.

            if (app()->isProduction() && config('broadcasting.default') === 'reverb') {
                $reverb = config('reverb.apps.apps.0', []);

                foreach (['id', 'key', 'secret'] as $key) {
                    if (blank($reverb[$key] ?? null)) {
                        throw new \RuntimeException("Health check failed: Reverb app {$key} is not configured.");
                    }
                }

                if (blank(config('reverb.servers.reverb.host'))) {
                    throw new \RuntimeException('Health check failed: REVERB_HOST is not configured.');
                }
            }

            if (app()->isProduction() && config('queue.default') === 'redis' && app()->bound(MasterSupervisorRepository::class)) {
                $masters = app(MasterSupervisorRepository::class)->all();
                if ($masters === []) {
                    throw new \RuntimeException('Health check failed: Horizon master supervisor is not running.');
                }
            }
        });
    }

    protected function configureCatalogCacheInvalidation(): void
    {
        $cache = Cache::supportsTags() ? Cache::tags(['catalog']) : Cache::store();
        $bumpCatalogVersion = static function (): void {
            $bumpKey = static function (string $key): void {
                if (app()->isProduction() && Cache::supportsTags()) {
                    $store = Cache::tags(['catalog']);
                    $store->put($key, ((int) $store->get($key, 0)) + 1);

                    return;
                }

                Cache::put($key, ((int) Cache::get($key, 0)) + 1);
            };

            $bumpKey('api:v1:catalog:hackatons:version');
            $bumpKey('api:v1:catalog:teams:version');
            $bumpKey('api:v1:catalog:profiles:version');
        };

        Team::saved(static function () use ($bumpCatalogVersion): void {
            $bumpCatalogVersion();
        });
        Team::deleted(static function () use ($bumpCatalogVersion): void {
            $bumpCatalogVersion();
        });
        User::saved(static function (User $user) use ($cache, $bumpCatalogVersion): void {
            if ($user->wasChanged(['nickname', 'role', 'description', 'is_profile_public'])) {
                $cache->forget("profile:public-show:{$user->id}:v2");
                $bumpCatalogVersion();
            }
        });
        User::deleted(static function (User $user) use ($cache, $bumpCatalogVersion): void {
            $cache->forget("profile:public-show:{$user->id}:v2");
            $bumpCatalogVersion();
        });
    }

    /**
     * Provide a portable, accent/case-insensitive substring search.
     *
     * PostgreSQL (production) lowercases Cyrillic natively via `lower()`, but SQLite's
     * built-in `lower()` only handles ASCII. We override it with a multibyte-aware
     * implementation so `whereLikeInsensitive` behaves identically across both drivers.
     */
    private function registerCaseInsensitiveSearch(): void
    {
        $connection = DB::connection();

        if ($connection instanceof SQLiteConnection) {
            $connection->getPdo()->sqliteCreateFunction(
                'lower',
                static fn (?string $value): ?string => $value === null ? null : mb_strtolower($value),
                1,
            );
        }

        EloquentBuilder::macro('whereLikeInsensitive', function (array|string $columns, string $term): EloquentBuilder {
            /** @var EloquentBuilder $this */
            $needle = '%'.mb_strtolower(trim($term)).'%';
            $columns = (array) $columns;

            return $this->where(function (EloquentBuilder $query) use ($columns, $needle): void {
                foreach (array_values($columns) as $index => $column) {
                    $expression = 'lower('.$column.') like ?';
                    $index === 0
                        ? $query->whereRaw($expression, [$needle])
                        : $query->orWhereRaw($expression, [$needle]);
                }
            });
        });
    }
}
