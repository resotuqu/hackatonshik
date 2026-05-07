<?php

use App\Enums\ApplicationStatus;
use App\Models\Hackaton;
use App\Models\HackatonAnnouncement;
use App\Models\HackatonAnnouncementImage;
use App\Models\HackatonApplication;
use App\Models\HackatonCase;
use App\Models\HackatonCaseField;
use App\Models\HackatonCaseScore;
use App\Models\HackatonCaseSubmission;
use App\Models\HackatonCertificate;
use App\Models\HackatonImage;
use App\Models\Team;
use App\Models\TeamRole;
use App\Models\User;
use App\Notifications\HackatonAnnouncementPublished;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

test('hackaton show page renders without participant collection type errors', function () {
    $organizer = User::factory()->partner()->create(['email_verified_at' => now(), 'phone_verified_at' => now()]);
    $hackaton = Hackaton::factory()->for($organizer)->create();

    $response = $this->get(route('hackatons.show', $hackaton));

    $response->assertOk();
    $response->assertSee($hackaton->title, false);
});

test('organizer can create a case for own hackaton', function () {
    $organizer = User::factory()->partner()->create(['email_verified_at' => now(), 'phone_verified_at' => now()]);
    $hackaton = Hackaton::factory()->for($organizer)->create();

    $response = $this
        ->actingAs($organizer)
        ->post(route('hackatons.cases.store', $hackaton), [
            'title' => 'UI/UX кейс',
            'description' => 'Опишите решение и приложите артефакты.',
            'is_published' => true,
        ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('hackaton_cases', [
        'hackaton_id' => $hackaton->id,
        'title' => 'UI/UX кейс',
        'is_published' => 1,
    ]);
});

test('non organizer cannot create case for hackaton', function () {
    $organizer = User::factory()->partner()->create(['email_verified_at' => now(), 'phone_verified_at' => now()]);
    $anotherUser = User::factory()->create(['email_verified_at' => now(), 'phone_verified_at' => now()]);
    $hackaton = Hackaton::factory()->for($organizer)->create();

    $response = $this
        ->actingAs($anotherUser)
        ->post(route('hackatons.cases.store', $hackaton), [
            'title' => 'Нельзя создать',
        ]);

    $response->assertForbidden();
});

test('required case fields are validated on team submission', function () {
    $organizer = User::factory()->partner()->create(['email_verified_at' => now(), 'phone_verified_at' => now()]);
    $participant = User::factory()->create(['email_verified_at' => now(), 'phone_verified_at' => now()]);
    $hackaton = Hackaton::factory()->for($organizer)->create();
    $team = Team::factory()->for($participant)->for($hackaton)->create();

    HackatonApplication::factory()->create([
        'hackaton_id' => $hackaton->id,
        'team_id' => $team->id,
        'status' => ApplicationStatus::ACCEPTED,
    ]);

    $case = HackatonCase::factory()->for($hackaton)->create(['is_published' => true]);
    $requiredField = HackatonCaseField::factory()->create([
        'hackaton_case_id' => $case->id,
        'type' => HackatonCaseField::TYPE_TEXT,
        'is_required' => true,
    ]);

    $response = $this
        ->actingAs($participant)
        ->post(route('hackatons.cases.submissions.store', [$hackaton, $case]), [
            'scope' => 'team',
            'team_id' => $team->id,
            'answers' => [
                $requiredField->id => '',
            ],
        ]);

    $response->assertSessionHasErrors("answers.{$requiredField->id}");
});

test('user cannot download certificate of another participant', function () {
    Storage::fake('local');

    $organizer = User::factory()->partner()->create(['email_verified_at' => now(), 'phone_verified_at' => now()]);
    $owner = User::factory()->create(['email_verified_at' => now(), 'phone_verified_at' => now()]);
    $intruder = User::factory()->create(['email_verified_at' => now(), 'phone_verified_at' => now()]);
    $hackaton = Hackaton::factory()->for($organizer)->create();

    $path = UploadedFile::fake()->create('certificate.pdf', 100, 'application/pdf')
        ->store('hackaton_certificates', 'local');

    $certificate = HackatonCertificate::factory()->for($hackaton)->create([
        'user_id' => $owner->id,
        'uploaded_by' => $organizer->id,
        'file_path' => $path,
    ]);

    $response = $this
        ->actingAs($intruder)
        ->get(route('certificates.download', $certificate));

    $response->assertForbidden();
});

test('announcement publication notifies hackaton participants', function () {
    Notification::fake();

    $organizer = User::factory()->partner()->create(['email_verified_at' => now(), 'phone_verified_at' => now()]);
    $participant = User::factory()->create(['email_verified_at' => now(), 'phone_verified_at' => now()]);
    $hackaton = Hackaton::factory()->for($organizer)->create();

    Team::factory()->for($participant)->for($hackaton)->create();

    $response = $this
        ->actingAs($organizer)
        ->post(route('hackatons.announcements.store', $hackaton), [
            'title' => 'Чекпоинт №1',
            'body' => 'До 18:00 добавьте ссылки на figma и gitverse.',
        ]);

    $response->assertRedirect();

    $announcement = HackatonAnnouncement::query()->first();
    expect($announcement)->not->toBeNull();

    Notification::assertSentTo(
        [$participant],
        HackatonAnnouncementPublished::class
    );
});

test('organizer can upload announcement image gallery', function () {
    Storage::fake('public');

    $organizer = User::factory()->partner()->create(['email_verified_at' => now(), 'phone_verified_at' => now()]);
    $hackaton = Hackaton::factory()->for($organizer)->create();

    $response = $this
        ->actingAs($organizer)
        ->post(route('hackatons.announcements.store', $hackaton), [
            'title' => 'Визуальный апдейт',
            'body' => 'Добавили галерею изображений.',
            'images' => [
                UploadedFile::fake()->image('a1.png'),
                UploadedFile::fake()->image('a2.png'),
            ],
        ]);

    $response->assertRedirect();

    $announcement = HackatonAnnouncement::query()->latest()->first();
    expect($announcement)->not->toBeNull();
    expect(HackatonAnnouncementImage::query()->where('hackaton_announcement_id', $announcement->id)->count())->toBe(2);
});

test('hackaton show page renders gallery carousel', function () {
    $organizer = User::factory()->partner()->create(['email_verified_at' => now(), 'phone_verified_at' => now()]);
    $hackaton = Hackaton::factory()->for($organizer)->create();
    HackatonImage::query()->create([
        'hackaton_id' => $hackaton->id,
        'path' => 'hackaton_gallery/sample.png',
        'sort_order' => 0,
    ]);

    $response = $this->get(route('hackatons.show', $hackaton));

    $response->assertOk();
    $response->assertSee('data-image-carousel', false);
    $response->assertSee('hackaton_gallery/sample.png');
});

test('organizer can bulk accept pending applications', function () {
    $organizer = User::factory()->partner()->create(['email_verified_at' => now(), 'phone_verified_at' => now()]);
    $hackaton = Hackaton::factory()->for($organizer)->create();

    $teamA = Team::factory()->for(User::factory()->create())->for($hackaton)->create();
    $teamB = Team::factory()->for(User::factory()->create())->for($hackaton)->create();

    $applicationA = HackatonApplication::factory()->create([
        'hackaton_id' => $hackaton->id,
        'team_id' => $teamA->id,
    ]);
    $applicationB = HackatonApplication::factory()->create([
        'hackaton_id' => $hackaton->id,
        'team_id' => $teamB->id,
    ]);

    $response = $this
        ->actingAs($organizer)
        ->patch(route('hackaton.applications.bulk-update', $hackaton), [
            'application_ids' => [$applicationA->id, $applicationB->id],
            'status' => 'accepted',
        ]);

    $response->assertRedirect();
    expect($applicationA->fresh()->status->value)->toBe('accepted');
    expect($applicationB->fresh()->status->value)->toBe('accepted');
});

test('case submission is blocked after deadline', function () {
    $organizer = User::factory()->partner()->create(['email_verified_at' => now(), 'phone_verified_at' => now()]);
    $participant = User::factory()->create(['email_verified_at' => now(), 'phone_verified_at' => now()]);
    $hackaton = Hackaton::factory()->for($organizer)->create();
    $team = Team::factory()->for($participant)->for($hackaton)->create();

    HackatonApplication::factory()->create([
        'hackaton_id' => $hackaton->id,
        'team_id' => $team->id,
        'status' => ApplicationStatus::ACCEPTED,
    ]);

    TeamRole::factory()->for($team)->create([
        'user_id' => $participant->id,
    ]);

    $case = HackatonCase::factory()->for($hackaton)->create([
        'is_published' => true,
        'publish_at' => now()->subDay(),
        'deadline_at' => now()->subMinute(),
    ]);
    $field = HackatonCaseField::factory()->create([
        'hackaton_case_id' => $case->id,
        'is_required' => false,
    ]);

    $response = $this
        ->actingAs($participant)
        ->post(route('hackatons.cases.submissions.store', [$hackaton, $case]), [
            'scope' => 'team',
            'team_id' => $team->id,
            'answers' => [
                $field->id => 'test',
            ],
        ]);

    $response->assertStatus(422);
});

test('draft announcement does not notify participants', function () {
    Notification::fake();

    $organizer = User::factory()->partner()->create(['email_verified_at' => now(), 'phone_verified_at' => now()]);
    $participant = User::factory()->create(['email_verified_at' => now(), 'phone_verified_at' => now()]);
    $hackaton = Hackaton::factory()->for($organizer)->create();
    Team::factory()->for($participant)->for($hackaton)->create();

    $response = $this
        ->actingAs($organizer)
        ->post(route('hackatons.announcements.store', $hackaton), [
            'title' => 'Черновик',
            'body' => 'Пока не публикуем',
            'is_draft' => true,
        ]);

    $response->assertRedirect();
    Notification::assertNothingSent();
});

test('duplicate certificate title for same user is ignored', function () {
    Storage::fake('local');
    $organizer = User::factory()->partner()->create(['email_verified_at' => now(), 'phone_verified_at' => now()]);
    $participant = User::factory()->create(['email_verified_at' => now(), 'phone_verified_at' => now()]);
    $hackaton = Hackaton::factory()->for($organizer)->create();
    Team::factory()->for($participant)->for($hackaton)->create();

    $payload = [
        'user_id' => $participant->id,
        'title' => 'Участник хакатона',
        'file' => UploadedFile::fake()->create('certificate.pdf', 50, 'application/pdf'),
    ];

    $this->actingAs($organizer)->post(route('hackatons.certificates.store', $hackaton), $payload)->assertRedirect();
    $this->actingAs($organizer)->post(route('hackatons.certificates.store', $hackaton), $payload)->assertRedirect();

    expect(HackatonCertificate::query()
        ->where('hackaton_id', $hackaton->id)
        ->where('user_id', $participant->id)
        ->where('title', 'Участник хакатона')
        ->count())->toBe(1);
});

test('organizer can score a case submission', function () {
    $organizer = User::factory()->partner()->create(['email_verified_at' => now(), 'phone_verified_at' => now()]);
    $participant = User::factory()->create(['email_verified_at' => now(), 'phone_verified_at' => now()]);
    $hackaton = Hackaton::factory()->for($organizer)->create();
    $case = HackatonCase::factory()->for($hackaton)->create();
    $submission = HackatonCaseSubmission::factory()->for($case, 'case')->create([
        'user_id' => $participant->id,
        'submitted_by_user_id' => $participant->id,
    ]);

    $response = $this
        ->actingAs($organizer)
        ->post(route('hackatons.scores.store', $hackaton), [
            'hackaton_case_submission_id' => $submission->id,
            'score' => 77,
            'max_score' => 100,
            'comment' => 'Хорошая проработка',
        ]);

    $response->assertRedirect();

    expect(HackatonCaseScore::query()->where('hackaton_case_submission_id', $submission->id)->exists())->toBeTrue();
});
