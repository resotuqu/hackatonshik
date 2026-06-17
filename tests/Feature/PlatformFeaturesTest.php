<?php

use App\Actions\Hackaton\ResolveParticipantUsersForHackatonCertificates;
use App\Actions\Hackaton\SuggestTeamsForUser;
use App\Enums\HackatonStatus;
use App\Jobs\ProcessHackatonFinishedAutomations;
use App\Models\Hackaton;
use App\Models\HackatonCase;
use App\Models\HackatonCaseScore;
use App\Models\HackatonCaseSubmission;
use App\Models\Role;
use App\Models\Skill;
use App\Models\Team;
use App\Models\TeamRole;
use App\Models\User;
use App\Notifications\HackatonWatchStartReminder;
use App\Notifications\HackatonWatchStatusChanged;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Notification;

test('user can watch and unwatch a hackaton', function () {
    $user = User::factory()->create();
    $hackaton = Hackaton::factory()->create(['is_public' => true]);

    $this->actingAs($user)
        ->post(route('hackaton.watches.store', $hackaton))
        ->assertRedirect();

    expect($user->watchedHackatons()->where('hackatons.id', $hackaton->id)->exists())->toBeTrue();

    $this->actingAs($user)
        ->delete(route('hackaton.watches.destroy', $hackaton))
        ->assertRedirect();

    expect($user->watchedHackatons()->where('hackatons.id', $hackaton->id)->exists())->toBeFalse();
});

test('watchers receive notification when hackaton status changes', function () {
    Notification::fake();

    $user = User::factory()->create();
    $hackaton = Hackaton::factory()->create([
        'is_public' => true,
        'status' => HackatonStatus::PUBLISHED,
        'start_at' => now()->addDays(10),
        'end_at' => now()->addDays(12),
        'registration_deadline_at' => now()->addDays(5),
    ]);

    $user->watchedHackatons()->attach($hackaton->id);

    $hackaton->update(['status' => HackatonStatus::REGISTRATION_OPEN]);

    Notification::assertSentTo($user, HackatonWatchStatusChanged::class);
});

test('sync statuses sends start reminders to watchers', function () {
    Notification::fake();

    $user = User::factory()->create();
    $hackaton = Hackaton::factory()->create([
        'is_public' => true,
        'start_at' => now()->addDays(7)->startOfDay()->addHours(12),
        'end_at' => now()->addDays(9),
    ]);

    $user->watchedHackatons()->attach($hackaton->id);

    Artisan::call('hackatons:sync-statuses');

    Notification::assertSentTo($user, HackatonWatchStartReminder::class);
});

test('public results page is visible when published', function () {
    $organizer = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create([
        'is_public' => true,
        'is_results_public' => true,
        'status' => HackatonStatus::FINISHED,
        'start_at' => now()->subDays(5),
        'end_at' => now()->subDay(),
    ]);

    $team = Team::factory()->create(['hackaton_id' => $hackaton->id, 'title' => 'Winners']);
    $case = HackatonCase::factory()->create(['hackaton_id' => $hackaton->id, 'is_published' => true]);
    $submission = HackatonCaseSubmission::factory()->create([
        'hackaton_case_id' => $case->id,
        'team_id' => $team->id,
    ]);
    HackatonCaseScore::factory()->create([
        'hackaton_case_submission_id' => $submission->id,
        'score' => 90,
        'max_score' => 100,
        'is_final' => true,
    ]);

    $this->get(route('hackatons.results', $hackaton))
        ->assertOk()
        ->assertSee('Winners')
        ->assertSee('Итоги хакатона');
});

test('public results page is hidden when not published', function () {
    $hackaton = Hackaton::factory()->create([
        'is_public' => true,
        'is_results_public' => false,
        'status' => HackatonStatus::FINISHED,
    ]);

    $this->get(route('hackatons.results', $hackaton))
        ->assertForbidden();
});

test('finished automations publish public results when configured', function () {
    $organizer = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create([
        'status' => HackatonStatus::FINISHED,
        'auto_publish_results_announcement' => true,
        'is_results_public' => false,
    ]);

    (new ProcessHackatonFinishedAutomations($hackaton->id))->handle(
        app(ResolveParticipantUsersForHackatonCertificates::class)
    );

    expect($hackaton->fresh()->is_results_public)->toBeTrue();
});

test('suggest teams for user matches skills on open roles', function () {
    $skill = Skill::factory()->create(['name' => 'PHP']);
    $otherSkill = Skill::factory()->create(['name' => 'Design']);

    $user = User::factory()->create([
        'open_to_teams' => true,
    ]);
    $user->skills()->attach($skill->id);

    $hackaton = Hackaton::factory()->create([
        'is_public' => true,
        'status' => HackatonStatus::REGISTRATION_OPEN,
    ]);

    $matchingTeam = Team::factory()->create([
        'hackaton_id' => $hackaton->id,
        'is_public' => true,
        'title' => 'PHP Crew',
    ]);
    $openRole = TeamRole::factory()->create([
        'team_id' => $matchingTeam->id,
        'user_id' => null,
        'role_id' => Role::factory()->create()->id,
    ]);
    $openRole->skills()->attach($skill->id);

    $otherTeam = Team::factory()->create([
        'hackaton_id' => $hackaton->id,
        'is_public' => true,
        'title' => 'Design Only',
    ]);
    $otherOpenRole = TeamRole::factory()->create([
        'team_id' => $otherTeam->id,
        'user_id' => null,
        'role_id' => Role::factory()->create()->id,
    ]);
    $otherOpenRole->skills()->attach($otherSkill->id);

    $suggestions = app(SuggestTeamsForUser::class)->handle($user);

    expect($suggestions)->toHaveCount(1)
        ->and($suggestions->first()['team']->title)->toBe('PHP Crew')
        ->and($suggestions->first()['matched_skills'])->toContain('PHP');
});

test('profile watches page lists watched hackatons', function () {
    $user = User::factory()->create();
    $hackaton = Hackaton::factory()->create(['title' => 'Watched Hack', 'is_public' => true]);
    $user->watchedHackatons()->attach($hackaton->id);

    $this->actingAs($user)
        ->get(route('profile.watches'))
        ->assertOk()
        ->assertSee('Watched Hack');
});
