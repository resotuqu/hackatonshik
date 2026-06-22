<?php

use App\Actions\Hackaton\ResolveParticipantUsersForHackatonCertificates;
use App\Enums\ApplicationStatus;
use App\Enums\HackatonStatus;
use App\Jobs\ProcessHackatonFinishedAutomations;
use App\Livewire\Pages\Admin\Users as AdminUsers;
use App\Livewire\Pages\Hackatons\Create;
use App\Livewire\Pages\Profile\Hackatons\Participants;
use App\Models\Hackaton;
use App\Models\HackatonApplication;
use App\Models\HackatonCase;
use App\Models\HackatonCaseScore;
use App\Models\HackatonCaseSubmission;
use App\Models\HackatonDocument;
use App\Models\HackatonTemplate;
use App\Models\NewsPost;
use App\Models\Team;
use App\Models\TeamRole;
use App\Models\User;
use App\Notifications\DocumentUploadReminder;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;

test('organizer can view participants page with team data', function () {
    $organizer = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create();
    $team = Team::factory()->create(['hackaton_id' => $hackaton->id]);
    $participant = User::factory()->create();
    TeamRole::factory()->create(['team_id' => $team->id, 'user_id' => $participant->id]);

    $this->actingAs($organizer)
        ->get(route('organizer.participants', $hackaton))
        ->assertOk()
        ->assertSee($team->title)
        ->assertSee($participant->fio);
});

test('outsider cannot view participants page', function () {
    $organizer = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create();
    $outsider = User::factory()->partner()->create();

    $this->actingAs($outsider)
        ->get(route('organizer.participants', $hackaton))
        ->assertForbidden();
});

test('participants page filters incomplete document teams', function () {
    $organizer = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create();
    $doc = HackatonDocument::factory()->create([
        'hackaton_id' => $hackaton->id,
        'filling_by_team_member' => true,
    ]);

    $completeTeam = Team::factory()->withoutCaptainRole()->create(['hackaton_id' => $hackaton->id, 'title' => 'Complete Team']);
    $completeUser = User::factory()->create();
    TeamRole::factory()->create(['team_id' => $completeTeam->id, 'user_id' => $completeUser->id]);
    $completeUser->userDocuments()->create([
        'hackaton_document_id' => $doc->id,
        'file_url' => 'docs/complete.pdf',
    ]);

    $incompleteTeam = Team::factory()->withoutCaptainRole()->create(['hackaton_id' => $hackaton->id, 'title' => 'Incomplete Team']);
    TeamRole::factory()->create(['team_id' => $incompleteTeam->id, 'user_id' => User::factory()->create()->id]);

    Livewire::actingAs($organizer)
        ->test(Participants::class, ['hackaton' => $hackaton])
        ->set('documentsFilter', 'incomplete')
        ->assertSee('Incomplete Team')
        ->assertDontSee('Complete Team');
});

test('organizer can send document upload reminders', function () {
    Notification::fake();

    $organizer = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create();
    HackatonDocument::factory()->create([
        'hackaton_id' => $hackaton->id,
        'filling_by_team_member' => true,
    ]);
    $team = Team::factory()->create(['hackaton_id' => $hackaton->id]);
    $participant = User::factory()->create();
    TeamRole::factory()->create(['team_id' => $team->id, 'user_id' => $participant->id]);

    Livewire::actingAs($organizer)
        ->test(Participants::class, ['hackaton' => $hackaton])
        ->call('sendDocumentReminders')
        ->assertHasNoErrors();

    Notification::assertSentTo($participant, DocumentUploadReminder::class);
});

test('judge hackathon show displays unrated submissions warning', function () {
    $organizer = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create();
    $judge = User::factory()->create(['role' => 'judge']);
    $hackaton->judges()->attach($judge->id, ['assigned_by' => $organizer->id, 'assigned_at' => now()]);

    $case = HackatonCase::factory()->create(['hackaton_id' => $hackaton->id]);
    $team = Team::factory()->create(['hackaton_id' => $hackaton->id]);
    HackatonCaseSubmission::factory()->create([
        'hackaton_case_id' => $case->id,
        'team_id' => $team->id,
    ]);

    $this->actingAs($judge)
        ->get(route('judge.hackatons.show', $hackaton))
        ->assertOk()
        ->assertSee('Осталось 1 сдач без финальной оценки');
});

test('judge can export own scores csv', function () {
    $organizer = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create();
    $judge = User::factory()->create(['role' => 'judge']);
    $hackaton->judges()->attach($judge->id, ['assigned_by' => $organizer->id, 'assigned_at' => now()]);

    $case = HackatonCase::factory()->create(['hackaton_id' => $hackaton->id]);
    $submission = HackatonCaseSubmission::factory()->create([
        'hackaton_case_id' => $case->id,
        'team_id' => Team::factory()->create(['hackaton_id' => $hackaton->id])->id,
    ]);

    HackatonCaseScore::factory()->create([
        'hackaton_case_submission_id' => $submission->id,
        'reviewed_by' => $judge->id,
        'is_final' => true,
        'score' => 8,
        'max_score' => 10,
    ]);

    $response = $this->actingAs($judge)
        ->get(route('judge.hackatons.scores.export', $hackaton));

    $response->assertOk();
    expect($response->headers->get('content-type'))->toContain('text/csv');
});

test('admin can manage news posts', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('filament.admin.resources.news.create'))
        ->assertOk();

    NewsPost::query()->create([
        'title' => 'Тестовая новость',
        'slug' => 'test-news',
        'excerpt' => 'Краткое описание',
        'body' => 'Текст новости',
        'category' => 'Обновления',
        'is_published' => true,
        'published_at' => now(),
    ]);

    $this->assertDatabaseHas('news_posts', [
        'title' => 'Тестовая новость',
        'slug' => 'test-news',
    ]);
});

test('admin can change user role and suspend account', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create(['role' => 'user']);

    Livewire::actingAs($admin)
        ->test(AdminUsers::class)
        ->call('startEditRole', $user->id)
        ->set('editRole', 'judge')
        ->call('saveRole')
        ->call('toggleSuspension', $user->id);

    $user->refresh();
    expect($user->role->value)->toBe('judge');
    expect($user->isSuspended())->toBeTrue();
});

test('suspended user is logged out on next request', function () {
    $user = User::factory()->create(['suspended_at' => now()]);

    $this->actingAs($user)
        ->get(route('profile'))
        ->assertRedirect(route('login'));

    $this->assertGuest();
});

test('finished hackathon dispatches automation job', function () {
    Queue::fake();

    $hackaton = Hackaton::factory()->create([
        'status' => HackatonStatus::IN_PROGRESS,
        'start_at' => now()->subDays(3),
        'end_at' => now()->subDay(),
        'is_public' => true,
    ]);

    $hackaton->syncStatusByTimeline();

    Queue::assertPushed(ProcessHackatonFinishedAutomations::class, fn ($job) => $job->hackatonId === $hackaton->id);
});

test('finished automation publishes results announcement', function () {
    $organizer = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create([
        'status' => HackatonStatus::FINISHED,
        'auto_publish_results_announcement' => true,
    ]);
    $team = Team::factory()->create(['hackaton_id' => $hackaton->id]);
    $participant = User::factory()->create();
    TeamRole::factory()->create(['team_id' => $team->id, 'user_id' => $participant->id]);

    (new ProcessHackatonFinishedAutomations($hackaton->id))->handle(app(ResolveParticipantUsersForHackatonCertificates::class));

    $this->assertDatabaseHas('hackaton_announcements', [
        'hackaton_id' => $hackaton->id,
        'title' => 'Итоги хакатона',
    ]);
    expect($hackaton->fresh()->finished_automations_ran_at)->not->toBeNull();
});

test('hackathon create wizard can apply template', function () {
    HackatonTemplate::factory()->create([
        'title' => 'Test Template',
        'slug' => 'test-template',
        'description' => 'Template description',
        'default_documents' => [
            ['name' => 'Doc 1', 'description' => 'Desc', 'filling_by_team_member' => true],
        ],
        'is_active' => true,
    ]);

    $organizer = User::factory()->partner()->create();
    $template = HackatonTemplate::query()->first();

    Livewire::actingAs($organizer)
        ->test(Create::class)
        ->call('applyTemplate', $template->id)
        ->assertSet('title', 'Test Template')
        ->assertSet('description', 'Template description');
});

test('participant hub shows checklist section', function () {
    $organizer = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create();
    $participant = User::factory()->create();
    $team = Team::factory()->create(['hackaton_id' => $hackaton->id, 'user_id' => $participant->id]);
    HackatonApplication::factory()->create([
        'hackaton_id' => $hackaton->id,
        'team_id' => $team->id,
        'status' => ApplicationStatus::ACCEPTED,
    ]);

    $this->actingAs($participant)
        ->get(route('participant.hackatons.hub', $hackaton))
        ->assertOk()
        ->assertSee('Что сделать сейчас');
});

test('organizer dashboard shows analytics section', function () {
    $organizer = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create();
    HackatonApplication::factory()->create([
        'hackaton_id' => $hackaton->id,
        'status' => ApplicationStatus::ACCEPTED,
    ]);

    $this->actingAs($organizer)
        ->get(route('organizer.dashboard'))
        ->assertOk()
        ->assertSee('Аналитика заявок');
});
