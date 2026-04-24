<?php

namespace App\Providers;

use App\Models\HackatonAnnouncement;
use App\Models\HackatonApplication;
use App\Models\HackatonCase;
use App\Models\HackatonCaseSubmission;
use App\Models\HackatonCertificate;
use App\Models\TeamApplication;
use App\Policies\HackatonAnnouncementPolicy;
use App\Policies\HackatonApplicationPolicy;
use App\Policies\HackatonCasePolicy;
use App\Policies\HackatonCaseSubmissionPolicy;
use App\Policies\HackatonCertificatePolicy;
use App\Policies\TeamApplicationPolicy;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use SocialiteProviders\Manager\SocialiteWasCalled;
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
        $this->configureDefaults();
        Event::listen(function (SocialiteWasCalled $event) {
            $event->extendSocialite('yandex', Provider::class);
        });

        Gate::policy(TeamApplication::class, TeamApplicationPolicy::class);
        Gate::policy(HackatonApplication::class, HackatonApplicationPolicy::class);
        Gate::policy(HackatonCase::class, HackatonCasePolicy::class);
        Gate::policy(HackatonCaseSubmission::class, HackatonCaseSubmissionPolicy::class);
        Gate::policy(HackatonAnnouncement::class, HackatonAnnouncementPolicy::class);
        Gate::policy(HackatonCertificate::class, HackatonCertificatePolicy::class);
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
