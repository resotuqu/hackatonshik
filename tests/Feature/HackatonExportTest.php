<?php

use App\Models\Hackaton;
use App\Models\HackatonDocument;
use App\Models\Team;
use App\Models\User;
use App\Models\UserHackatonDocument;
use Illuminate\Support\Facades\Storage;

test('organizer can export teams as csv', function () {
    $organizer = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->create(['user_id' => $organizer->id]);
    Team::factory()->count(3)->create(['hackaton_id' => $hackaton->id]);

    $response = $this->actingAs($organizer)
        ->get(route('hackatons.export.teams', $hackaton));

    $response->assertOk()
        ->assertHeader('Content-Type', 'text/csv; charset=UTF-8')
        ->assertHeader('Content-Disposition', 'attachment; filename=hackaton_'.$hackaton->id.'_teams.csv');

    $content = $response->streamedContent();
    expect($content)->toContain('team_id,team_title,owner,owner_email,members_count');
});

test('organizer can export participants as csv', function () {
    $organizer = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->create(['user_id' => $organizer->id]);
    $team = Team::factory()->create(['hackaton_id' => $hackaton->id]);

    $response = $this->actingAs($organizer)
        ->get(route('hackatons.export.participants', $hackaton));

    $response->assertOk()
        ->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

    $content = $response->streamedContent();
    expect($content)->toContain('user_id,fio,email,nickname,teams_count');
});

test('organizer can export documents as zip', function () {
    $organizer = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->create(['user_id' => $organizer->id]);
    $docType = HackatonDocument::factory()->create(['hackaton_id' => $hackaton->id]);

    Storage::fake('public');
    $filePath = 'docs/test.pdf';
    Storage::disk('public')->put($filePath, 'fake pdf content');

    UserHackatonDocument::factory()->create([
        'hackaton_document_id' => $docType->id,
        'file_url' => $filePath,
    ]);

    $response = $this->actingAs($organizer)
        ->get(route('hackatons.export.documents-zip', $hackaton));

    $response->assertOk()
        ->assertHeader('Content-Disposition', 'attachment; filename=hackaton_'.$hackaton->id.'_documents.zip');
});

test('non-organizer cannot export data', function () {
    $organizer = User::factory()->partner()->create();
    $other = User::factory()->create();
    $hackaton = Hackaton::factory()->create(['user_id' => $organizer->id]);

    $this->actingAs($other)
        ->get(route('hackatons.export.teams', $hackaton))
        ->assertForbidden();
});
