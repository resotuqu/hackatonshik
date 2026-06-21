<?php

declare(strict_types=1);

use App\Livewire\GlobalSearch;
use App\Models\Hackaton;
use App\Models\Team;
use App\Models\User;
use Livewire\Livewire;

test('global search finds public hackatons case-insensitively for cyrillic', function () {
    $hackaton = Hackaton::factory()->create(['title' => 'Зухтер Квантум Челлендж', 'is_public' => true]);

    $ids = Livewire::test(GlobalSearch::class)->set('q', 'зухтер')
        ->instance()->results()['hackatons']->pluck('id');

    expect($ids)->toContain($hackaton->id);
});

test('global search ignores private entities', function () {
    $private = Hackaton::factory()->create(['title' => 'Зухтер Приватный Кейс', 'is_public' => false]);

    $ids = Livewire::test(GlobalSearch::class)->set('q', 'зухтерприватный')
        ->instance()->results()['hackatons']->pluck('id');

    expect($ids)->not->toContain($private->id);
});

test('global search matches teams', function () {
    $team = Team::factory()->create(['title' => 'Команда Зухтерон', 'is_public' => true]);

    $ids = Livewire::test(GlobalSearch::class)->set('q', 'зухтерон')
        ->instance()->results()['teams']->pluck('id');

    expect($ids)->toContain($team->id);
});

test('global search returns public users but not hidden ones', function () {
    $public = User::factory()->create(['fio' => 'Зухтеров Пётр', 'is_profile_public' => true]);
    $hidden = User::factory()->create(['fio' => 'Зухтеров Скрытый', 'is_profile_public' => false]);

    $ids = Livewire::test(GlobalSearch::class)->set('q', 'зухтеров')
        ->instance()->results()['users']->pluck('id');

    expect($ids)->toContain($public->id)
        ->and($ids)->not->toContain($hidden->id);
});

test('global search requires at least two characters', function () {
    Hackaton::factory()->create(['title' => 'Я', 'is_public' => true]);

    expect(Livewire::test(GlobalSearch::class)->set('q', 'я')->instance()->hasResults())->toBeFalse();
});
