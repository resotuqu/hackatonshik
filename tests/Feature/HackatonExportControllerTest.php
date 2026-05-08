<?php

declare(strict_types=1);

use App\Models\Hackaton;
use App\Models\HackatonDocument;
use App\Models\Team;
use App\Models\TeamRole;
use App\Models\User;
use App\Models\UserHackatonDocument;
use Illuminate\Support\Facades\Storage;

use function Pest\Laravel\actingAs;

test('organizer can export participants csv', function () {
    $organizer = User::factory()->partner()->create();
    $captain = User::factory()->create(['fio' => 'Capt Ain', 'email' => 'cap@example.com']);
    $member = User::factory()->create(['fio' => 'Mem Ber', 'email' => 'mem@example.com']);
    $hackaton = Hackaton::factory()->for($organizer)->create();
    $team = Team::factory()->for($captain)->for($hackaton)->create();
    TeamRole::factory()->for($team)->create(['user_id' => $member->id]);

    $response = actingAs($organizer)
        ->get(route('hackatons.export.participants', $hackaton));

    $response->assertSuccessful();
    $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    $body = $response->streamedContent();
    expect($body)->toContain('Capt Ain', 'cap@example.com', 'Mem Ber', 'mem@example.com');
});

test('non organizer cannot export participants csv', function () {
    $organizer = User::factory()->partner()->create();
    $outsider = User::factory()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create();

    actingAs($outsider)
        ->get(route('hackatons.export.participants', $hackaton))
        ->assertForbidden();
});

test('non organizer cannot export documents zip', function () {
    $organizer = User::factory()->partner()->create();
    $outsider = User::factory()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create();

    actingAs($outsider)
        ->get(route('hackatons.export.documents-zip', $hackaton))
        ->assertForbidden();
});

test('documents zip export warns when there are no participant documents', function () {
    Storage::fake('public');

    $organizer = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create();

    actingAs($organizer)
        ->from(route('hackatons.show', $hackaton))
        ->get(route('hackatons.export.documents-zip', $hackaton))
        ->assertRedirect()
        ->assertSessionHas('warning');
});

test('documents zip export delivers archive when files exist', function () {
    $disk = Storage::fake('public');

    $organizer = User::factory()->partner()->create();
    $participant = User::factory()->create(['fio' => 'Doc Owner']);
    $hackaton = Hackaton::factory()->for($organizer)->create();
    $document = HackatonDocument::factory()->create([
        'hackaton_id' => $hackaton->id,
        'name' => 'Согласие',
    ]);
    $disk->put('user_hackaton_documents/sample.pdf', 'pdf-content');
    UserHackatonDocument::factory()->create([
        'user_id' => $participant->id,
        'hackaton_document_id' => $document->id,
        'file_url' => 'user_hackaton_documents/sample.pdf',
    ]);

    $response = actingAs($organizer)
        ->get(route('hackatons.export.documents-zip', $hackaton));

    $response->assertSuccessful();
    $contentType = $response->headers->get('content-type');
    expect($contentType)->toContain('application');
});
