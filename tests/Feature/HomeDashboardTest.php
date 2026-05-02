<?php

declare(strict_types=1);

use App\Models\User;

test('guest sees marketing landing on home', function () {
    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertSee('Путь участника и организатора в одном месте', false);
    $response->assertDontSee('Краткая сводка', false);
});

test('authenticated participant sees dashboard summary on home', function () {
    $user = User::factory()->create(['role' => 'user']);

    $response = $this->actingAs($user)->get(route('home'));

    $response->assertOk();
    $response->assertSee('Краткая сводка', false);
    $response->assertSee('data-test="home-dashboard"', false);
});
