<?php

use App\Models\Hackaton;
use App\Models\HackatonCase;
use App\Models\User;

use function Pest\Laravel\actingAs;

it('allows organizers to create cases for their hackatons', function () {
    $organizer = User::factory()->create();
    $hackaton = Hackaton::factory()->create(['user_id' => $organizer->id]);

    actingAs($organizer)
        ->post(route('hackatons.cases.store', $hackaton), [
            'title' => 'Innovative Case',
            'description' => 'Solve the world problems',
            'is_published' => true,
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('hackaton_cases', [
        'hackaton_id' => $hackaton->id,
        'title' => 'Innovative Case',
    ]);
});

it('prevents non-organizers from creating cases', function () {
    $stranger = User::factory()->create();
    $hackaton = Hackaton::factory()->create(); // Owner is someone else

    actingAs($stranger)
        ->post(route('hackatons.cases.store', $hackaton), [
            'title' => 'Stolen Case',
        ])
        ->assertForbidden();
});

it('allows organizers to add fields to cases', function () {
    $organizer = User::factory()->create();
    $hackaton = Hackaton::factory()->create(['user_id' => $organizer->id]);
    $case = HackatonCase::factory()->create(['hackaton_id' => $hackaton->id]);

    actingAs($organizer)
        ->post(route('hackatons.cases.fields.store', [$hackaton, $case]), [
            'label' => 'Project Name',
            'type' => 'text',
            'is_required' => true,
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('hackaton_case_fields', [
        'hackaton_case_id' => $case->id,
        'label' => 'Project Name',
        'type' => 'text',
    ]);
});
