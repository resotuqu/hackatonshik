<?php

declare(strict_types=1);

use App\Models\User;

it('lets a verified user open the create team page in a real browser', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    visit('/teams/create')
        ->assertSee('Создание команды');
});

it('shows public team catalog for guests', function () {
    visit('/teams')
        ->assertSee('Каталог команд')
        ->assertNoJavaScriptErrors();
});
