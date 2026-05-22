<?php

declare(strict_types=1);

use App\Models\Hackaton;
use App\Models\HackatonDocument;
use App\Models\User;

use function Pest\Laravel\actingAs;

test('organizer sees readiness checklist and header metrics on show page', function () {
    $organizer = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create(['is_public' => true]);
    HackatonDocument::factory()->create(['hackaton_id' => $hackaton->id]);

    actingAs($organizer)
        ->get(route('hackatons.show', $hackaton))
        ->assertOk()
        ->assertSee('Прогресс подготовки', false)
        ->assertSee('Ключевые метрики', false)
        ->assertSee('Настроены документы для участников', false);
});

test('guest does not see organizer readiness checklist on public show page', function () {
    $organizer = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create(['is_public' => true]);

    $this->get(route('hackatons.show', $hackaton))
        ->assertOk()
        ->assertDontSee('Прогресс подготовки', false);
});
