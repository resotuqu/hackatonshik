<?php

declare(strict_types=1);

use App\Events\HackatonApplicationChanged;
use App\Livewire\Pages\Home\Index as HomeIndex;
use App\Models\Hackaton;
use App\Models\Team;
use App\Models\User;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;

use function Pest\Laravel\get;

function homeCatalogCache(): Repository
{
    return Cache::supportsTags()
        ? Cache::tags(['home', 'catalog'])
        : Cache::store();
}

test('home page populates featured hackatons cache', function () {
    Cache::flush();

    $organizer = User::factory()->partner()->create();
    Hackaton::factory()->for($organizer)->create([
        'is_public' => true,
        'title' => 'CachedFeaturedUnique',
    ]);

    expect(homeCatalogCache()->has('home-featured-hackatons-v2'))->toBeFalse();

    Livewire::test(HomeIndex::class);

    expect(homeCatalogCache()->has('home-featured-hackatons-v2'))->toBeTrue();
});

test('home page populates public totals cache', function () {
    Cache::flush();

    $organizer = User::factory()->partner()->create();
    Hackaton::factory()->for($organizer)->create(['is_public' => true]);

    expect(homeCatalogCache()->has('home-public-totals-v4'))->toBeFalse();

    Livewire::test(HomeIndex::class);

    expect(homeCatalogCache()->has('home-public-totals-v4'))->toBeTrue();
});

test('hackaton application changed event invalidates featured hackatons cache', function () {
    Cache::flush();

    $organizer = User::factory()->partner()->create();
    $captain = User::factory()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create();
    $team = Team::factory()->for($captain)->for($hackaton)->create();

    homeCatalogCache()->put('home-featured-hackatons-v2', 'cached');
    homeCatalogCache()->put('home-public-totals-v4', 'cached');

    event(new HackatonApplicationChanged(
        teamId: (int) $team->id,
        hackatonId: (int) $hackaton->id,
        organizerId: (int) $organizer->id,
        invalidateHomeFeatured: true,
    ));

    expect(homeCatalogCache()->has('home-featured-hackatons-v2'))->toBeFalse();
    expect(homeCatalogCache()->has('home-public-totals-v4'))->toBeFalse();
});

test('hackaton application changed without invalidate flag keeps featured cache', function () {
    Cache::flush();

    Cache::put('home-featured-hackatons-v2', 'cached', 3600);

    event(new HackatonApplicationChanged(
        teamId: 999,
        hackatonId: 1,
        organizerId: 2,
        invalidateHomeFeatured: false,
    ));

    expect(Cache::has('home-featured-hackatons-v2'))->toBeTrue();
    expect(Cache::has('home-public-totals-v4'))->toBeFalse();
});

test('home route renders successfully for guests', function () {
    get(route('home'))->assertOk();
});
