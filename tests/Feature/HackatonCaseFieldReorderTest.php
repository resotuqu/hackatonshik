<?php

declare(strict_types=1);

use App\Models\Hackaton;
use App\Models\HackatonCase;
use App\Models\HackatonCaseField;
use App\Models\User;

use function Pest\Laravel\actingAs;

test('organizer can reorder case fields', function () {
    $organizer = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create(['is_public' => true]);
    $case = HackatonCase::factory()->for($hackaton)->create();

    $fieldA = HackatonCaseField::factory()->create([
        'hackaton_case_id' => $case->id,
        'sort_order' => 0,
        'key' => 'field_a_'.uniqid(),
    ]);
    $fieldB = HackatonCaseField::factory()->create([
        'hackaton_case_id' => $case->id,
        'sort_order' => 1,
        'key' => 'field_b_'.uniqid(),
    ]);

    actingAs($organizer)
        ->patch(route('hackatons.cases.fields.reorder', [$hackaton, $case]), [
            'field_ids' => [$fieldB->id, $fieldA->id],
        ])
        ->assertSessionHas('success');

    expect($fieldA->fresh()->sort_order)->toBe(1);
    expect($fieldB->fresh()->sort_order)->toBe(0);
});
