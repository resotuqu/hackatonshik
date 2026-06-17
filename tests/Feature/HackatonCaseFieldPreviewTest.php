<?php

declare(strict_types=1);

use App\Models\Hackaton;
use App\Models\HackatonCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('organizer can preview advanced case fields payload', function () {
    $organizer = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create();
    $case = HackatonCase::factory()->for($hackaton)->create();

    $this->actingAs($organizer)
        ->postJson(route('hackatons.cases.fields.preview', [$hackaton, $case]), [
            'fields' => [
                ['label' => 'Команда', 'type' => 'text', 'is_required' => true],
                ['label' => 'Ссылка на демо', 'type' => 'textarea', 'conditional_on' => 'Команда'],
            ],
        ])
        ->assertOk()
        ->assertJsonPath('preview.0.required', true)
        ->assertJsonPath('preview.1.conditional_on', 'Команда');
});
