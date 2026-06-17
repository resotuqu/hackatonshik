<?php

declare(strict_types=1);

use App\Enums\ApplicationStatus;
use App\Models\Hackaton;
use App\Models\HackatonApplication;
use App\Models\HackatonCase;
use App\Models\HackatonCaseField;
use App\Models\HackatonCaseSubmission;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\QueryException;

use function Pest\Laravel\actingAs;

test('resubmitting case solution updates existing submission instead of creating duplicate', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
        'phone_verified_at' => now(),
    ]);
    $hackaton = Hackaton::factory()->create();
    $team = Team::factory()->create([
        'user_id' => $user->id,
        'hackaton_id' => $hackaton->id,
    ]);

    HackatonApplication::factory()->create([
        'hackaton_id' => $hackaton->id,
        'team_id' => $team->id,
        'status' => ApplicationStatus::ACCEPTED,
    ]);

    $case = HackatonCase::factory()->create(['hackaton_id' => $hackaton->id, 'is_published' => true]);
    $team->update(['hackaton_case_id' => $case->id]);
    $field = HackatonCaseField::factory()->create([
        'hackaton_case_id' => $case->id,
        'type' => HackatonCaseField::TYPE_TEXT,
    ]);

    $payload = [
        'scope' => 'team',
        'answers' => [
            $field->id => 'First answer',
        ],
        'team_id' => $team->id,
    ];

    actingAs($user)
        ->post(route('hackatons.cases.submissions.store', [$hackaton, $case]), $payload)
        ->assertRedirect()
        ->assertSessionHas('success');

    actingAs($user)
        ->post(route('hackatons.cases.submissions.store', [$hackaton, $case]), [
            ...$payload,
            'answers' => [$field->id => 'Updated answer'],
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    expect(HackatonCaseSubmission::query()->where('hackaton_case_id', $case->id)->where('team_id', $team->id)->count())->toBe(1);

    $this->assertDatabaseHas('hackaton_case_answers', [
        'hackaton_case_field_id' => $field->id,
        'value_text' => 'Updated answer',
    ]);
});

test('duplicate team submission violates unique constraint', function () {
    $submission = HackatonCaseSubmission::factory()->create([
        'team_id' => Team::factory()->create()->id,
        'user_id' => null,
    ]);

    expect(fn () => HackatonCaseSubmission::query()->create([
        'hackaton_case_id' => $submission->hackaton_case_id,
        'team_id' => $submission->team_id,
        'user_id' => null,
        'submitted_by_user_id' => User::factory()->create()->id,
        'submitted_at' => now(),
    ]))->toThrow(QueryException::class);
});
