<?php

declare(strict_types=1);

use App\Enums\HackatonStatus;
use App\Models\Hackaton;
use App\Models\User;

it('shows public hackaton catalog with active hackatons in real browser', function () {
    $organizer = User::factory()->partner()->create();
    Hackaton::factory()->for($organizer)->create([
        'is_public' => true,
        'status' => HackatonStatus::REGISTRATION_OPEN,
        'title' => 'BrowserCatalogHackUnique',
    ]);

    visit('/hackatons')
        ->assertSee('Каталог хакатонов')
        ->assertSee('BrowserCatalogHackUnique')
        ->assertNoJavaScriptErrors();
});

it('renders public hackaton show page in real browser', function () {
    $organizer = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create([
        'is_public' => true,
        'status' => HackatonStatus::REGISTRATION_OPEN,
        'title' => 'BrowserShowHackUnique',
    ]);

    visit('/hackatons/'.$hackaton->id)
        ->assertSee('BrowserShowHackUnique')
        ->assertNoJavaScriptErrors();
});
