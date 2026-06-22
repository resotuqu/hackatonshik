<?php

declare(strict_types=1);

use App\Models\Hackaton;
use App\Models\User;

use function Pest\Laravel\actingAs;

test('organizer dashboard is reachable for partner', function () {
    $organizer = User::factory()->partner()->create();
    Hackaton::factory()->for($organizer)->create(['is_public' => true]);

    actingAs($organizer)
        ->get(route('organizer.dashboard'))
        ->assertOk()
        ->assertSeeLivewire('organizer.dashboard')
        ->assertSee('data-test="organizer-dashboard-summary"', false)
        ->assertSee('Организатор', false)
        ->assertDontSee('Дашборд организатора', false);
});

test('judge dashboard uses role-aligned page header', function () {
    $judge = User::factory()->judge()->create();

    actingAs($judge)
        ->get(route('judge.dashboard'))
        ->assertOk()
        ->assertSee('Судья', false)
        ->assertSee('Оценка проектов и экспертиза', false);
});

test('profile organizer route redirects to organizer dashboard', function () {
    $organizer = User::factory()->partner()->create();

    actingAs($organizer)
        ->get(route('profile.organizer'))
        ->assertRedirect(route('organizer.dashboard'));
});
