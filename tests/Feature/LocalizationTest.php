<?php

use App\Livewire\LocaleSwitcher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Livewire\Livewire;

uses(RefreshDatabase::class);

describe('SetLocale middleware', function () {
    it('uses the default locale (ru) when no supported preference is set', function () {
        // Use zh-CN (not in SUPPORTED) so the middleware falls through to config default
        $response = $this->withHeader('Accept-Language', 'zh-CN')->get(route('home'));

        $response->assertSuccessful();
        expect(App::getLocale())->toBe('ru');
    });

    it('picks up locale from cookie', function () {
        $response = $this->withCookie('locale', 'en')->get(route('home'));

        $response->assertSuccessful();
        expect(App::getLocale())->toBe('en');
    });

    it('ignores unsupported locale in cookie and falls back to config default', function () {
        // 'fr' is not in SUPPORTED; 'zh-CN' Accept-Language also not in SUPPORTED → falls to config 'ru'
        $this->withCookie('locale', 'fr')->withHeader('Accept-Language', 'zh-CN')->get(route('home'));

        expect(App::getLocale())->toBe('ru');
    });

    it('uses authenticated user locale over cookie', function () {
        $user = User::factory()->create(['locale' => 'en']);

        $this->actingAs($user)->withCookie('locale', 'ru')->get(route('home'));

        expect(App::getLocale())->toBe('en');
    });

    it('falls back to cookie when user has no locale preference', function () {
        $user = User::factory()->create(['locale' => null]);

        $this->actingAs($user)->withCookie('locale', 'en')->get(route('home'));

        expect(App::getLocale())->toBe('en');
    });
});

describe('LocaleSwitcher component', function () {
    it('renders with current locale', function () {
        App::setLocale('en');

        Livewire::test(LocaleSwitcher::class)
            ->assertSet('current', 'en');
    });

    it('ignores unsupported locales', function () {
        Livewire::test(LocaleSwitcher::class)
            ->call('switch', 'fr')
            ->assertSet('current', 'ru');
    });

    it('persists locale to authenticated user DB column', function () {
        $user = User::factory()->create(['locale' => 'ru']);

        Livewire::actingAs($user)
            ->test(LocaleSwitcher::class)
            ->call('switch', 'en');

        expect($user->fresh()->locale)->toBe('en');
    });

    it('does not fail for guest users', function () {
        Livewire::test(LocaleSwitcher::class)
            ->call('switch', 'en')
            ->assertSet('current', 'en');
    });

    it('queues a locale cookie on switch', function () {
        Livewire::test(LocaleSwitcher::class)
            ->call('switch', 'en');

        $cookies = collect(cookie()->getQueuedCookies());
        expect($cookies->first(fn ($c) => $c->getName() === 'locale')?->getValue())->toBe('en');
    });
});

describe('Localized UI rendering', function () {
    it('sets html lang attribute from active locale', function () {
        $response = $this->withCookie('locale', 'en')->get(route('home'));

        $response->assertSuccessful();
        $response->assertSee('lang="en"', false);
    });

    it('renders global search placeholder in english when locale is en', function () {
        $user = User::factory()->create(['locale' => 'en']);

        $response = $this->actingAs($user)->get(route('home'));

        $response->assertSuccessful();
        $response->assertSee(__('ui.search.placeholder', locale: 'en'), false);
    });

    it('renders home hero copy in english when locale is en', function () {
        $response = $this->withCookie('locale', 'en')->get(route('home'));

        $response->assertSuccessful();
        $response->assertSee(__('ui.home.hero_title', locale: 'en'), false);
        $response->assertSee(__('ui.home.stats_title', locale: 'en'), false);
    });

    it('renders login form copy in english when locale is en', function () {
        $response = $this->withCookie('locale', 'en')->get(route('login'));

        $response->assertSuccessful();
        $response->assertSee(__('ui.auth.login.form_title', locale: 'en'), false);
        $response->assertSee(__('ui.auth.login.submit', locale: 'en'), false);
    });

    it('renders layout footer copy in english when locale is en', function () {
        $response = $this->withCookie('locale', 'en')->get(route('home'));

        $response->assertSuccessful();
        $response->assertSee(__('ui.layout.footer.main_pages', locale: 'en'), false);
        $response->assertSee(__('ui.layout.cookie.accept', locale: 'en'), false);
    });
});
