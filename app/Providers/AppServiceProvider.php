<?php

namespace App\Providers;

use App\Mail\VerifyEmailAddressMail;
use App\Models\HackatonAnnouncement;
use App\Models\HackatonApplication;
use App\Models\HackatonCase;
use App\Models\HackatonCaseSubmission;
use App\Models\HackatonCertificate;
use App\Models\TeamApplication;
use App\Models\User;
use App\Policies\HackatonAnnouncementPolicy;
use App\Policies\HackatonApplicationPolicy;
use App\Policies\HackatonCasePolicy;
use App\Policies\HackatonCaseSubmissionPolicy;
use App\Policies\HackatonCertificatePolicy;
use App\Policies\TeamApplicationPolicy;
use Carbon\CarbonImmutable;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
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
        Event::listen(function (SocialiteWasCalled $event) {
            $event->extendSocialite('yandex', Provider::class);
            $event->extendSocialite('vkontakte', VkontakteProvider::class);
        });

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
}
