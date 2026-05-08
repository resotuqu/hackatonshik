<?php

declare(strict_types=1);

use App\Events\HackatonApplicationChanged;
use App\Models\Hackaton;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

use function Pest\Laravel\get;

test('home page populates featured hackatons cache', function () {
    Cache::flush();

    $organizer = User::factory()->partner()->create();
    Hackaton::factory()->for($organizer)->create([
        'is_public' => true,
        'title' => 'CachedFeaturedUnique',
    ]);

    expect(Cache::has('home-featured-hackatons-v1'))->toBeFalse();

    get(route('home'))->assertOk();

    expect(Cache::has('home-featured-hackatons-v1'))->toBeTrue();
});

test('home page populates public totals cache', function () {
    Cache::flush();

    $organizer = User::factory()->partner()->create();
    Hackaton::factory()->for($organizer)->create(['is_public' => true]);

    expect(Cache::has('home-public-totals-v3'))->toBeFalse();

    get(route('home'))->assertOk();

    expect(Cache::has('home-public-totals-v3'))->toBeTrue();
});

test('hackaton application changed event invalidates featured hackatons cache', function () {
    Cache::flush();

    $organizer = User::factory()->partner()->create();
    $captain = User::factory()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create();
    $team = Team::factory()->for($captain)->for($hackaton)->create();

    Cache::put('home-featured-hackatons-v1', 'cached');
    Cache::put('home-public-totals-v3', 'cached');

    event(new HackatonApplicationChanged(
        teamId: (int) $team->id,
        hackatonId: (int) $hackaton->id,
        organizerId: (int) $organizer->id,
        invalidateHomeFeatured: true,
    ));

    expect(Cache::has('home-featured-hackatons-v1'))->toBeFalse();
    expect(Cache::has('home-public-totals-v3'))->toBeFalse();
});

test('hackaton application changed without invalidate flag keeps featured cache', function () {
    Cache::flush();

    $organizer = User::factory()->partner()->create();
    $captain = User::factory()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create();
    $team = Team::factory()->for($captain)->for($hackaton)->create();

    Cache::put('home-featured-hackatons-v1', 'cached');

    event(new HackatonApplicationChanged(
        teamId: (int) $team->id,
        hackatonId: (int) $hackaton->id,
        organizerId: (int) $organizer->id,
        invalidateHomeFeatured: false,
    ));

    expect(Cache::has('home-featured-hackatons-v1'))->toBeTrue();
});
