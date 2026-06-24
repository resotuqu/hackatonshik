<?php

declare(strict_types=1);

use App\Models\Hackaton;
use App\Models\HackatonCertificate;
use App\Models\Team;
use App\Models\TeamRole;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

use function Pest\Laravel\actingAs;

test('organizer can issue a certificate to single user', function () {
    Storage::fake('local');

    $organizer = User::factory()->partner()->create();
    $recipient = User::factory()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create();
    Team::factory()->for($hackaton)->create(['user_id' => $recipient->id]);

    actingAs($organizer)
        ->from(route('hackatons.show', $hackaton))
        ->post(route('hackatons.certificates.store', $hackaton), [
            'user_id' => $recipient->id,
            'title' => 'Победитель хакатона',
            'file' => UploadedFile::fake()->create('cert.pdf', 100, 'application/pdf'),
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    expect(HackatonCertificate::query()
        ->where('hackaton_id', $hackaton->id)
        ->where('user_id', $recipient->id)
        ->where('title', 'Победитель хакатона')
        ->exists())->toBeTrue();
});

test('organizer can issue certificates to multiple users at once', function () {
    Storage::fake('local');

    $organizer = User::factory()->partner()->create();
    $a = User::factory()->create();
    $b = User::factory()->create();
    $c = User::factory()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create();
    $teamA = Team::factory()->for($hackaton)->create(['user_id' => $a->id]);
    TeamRole::factory()->create(['team_id' => $teamA->id, 'user_id' => $b->id]);
    Team::factory()->for($hackaton)->create(['user_id' => $c->id]);

    actingAs($organizer)
        ->from(route('hackatons.show', $hackaton))
        ->post(route('hackatons.certificates.store', $hackaton), [
            'user_id' => $a->id,
            'user_ids' => [$a->id, $b->id, $c->id],
            'title' => 'Bulk Cert',
            'file' => UploadedFile::fake()->create('cert.pdf', 100, 'application/pdf'),
        ])
        ->assertRedirect();

    expect(HackatonCertificate::query()
        ->where('hackaton_id', $hackaton->id)
        ->where('title', 'Bulk Cert')
        ->count())->toBe(3);
});

test('certificate creation is idempotent on hackaton+user+title triple', function () {
    Storage::fake('local');

    $organizer = User::factory()->partner()->create();
    $recipient = User::factory()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create();
    Team::factory()->for($hackaton)->create(['user_id' => $recipient->id]);

    HackatonCertificate::factory()->create([
        'hackaton_id' => $hackaton->id,
        'user_id' => $recipient->id,
        'title' => 'Duplicate Title',
    ]);

    actingAs($organizer)
        ->from(route('hackatons.show', $hackaton))
        ->post(route('hackatons.certificates.store', $hackaton), [
            'user_id' => $recipient->id,
            'title' => 'Duplicate Title',
            'file' => UploadedFile::fake()->create('cert.pdf', 100, 'application/pdf'),
        ])
        ->assertRedirect();

    expect(HackatonCertificate::query()
        ->where('hackaton_id', $hackaton->id)
        ->where('user_id', $recipient->id)
        ->where('title', 'Duplicate Title')
        ->count())->toBe(1);
});

test('organizer cannot issue certificate to non participant', function () {
    Storage::fake('local');

    $organizer = User::factory()->partner()->create();
    $outsider = User::factory()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create();

    actingAs($organizer)
        ->from(route('hackatons.show', $hackaton))
        ->post(route('hackatons.certificates.store', $hackaton), [
            'user_id' => $outsider->id,
            'title' => 'No way',
            'file' => UploadedFile::fake()->create('cert.pdf', 100, 'application/pdf'),
        ])
        ->assertSessionHasErrors('user_ids');
});

test('bulk certificates store separate files per recipient', function () {
    Storage::fake('local');

    $organizer = User::factory()->partner()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create();
    $team = Team::factory()->for($hackaton)->create();
    $a = User::factory()->create();
    $b = User::factory()->create();
    $team->update(['user_id' => $a->id]);
    TeamRole::factory()->create(['team_id' => $team->id, 'user_id' => $b->id]);

    actingAs($organizer)
        ->from(route('hackatons.show', $hackaton))
        ->post(route('hackatons.certificates.store', $hackaton), [
            'user_id' => $a->id,
            'user_ids' => [$a->id, $b->id],
            'title' => 'Separate Files',
            'file' => UploadedFile::fake()->create('cert.pdf', 100, 'application/pdf'),
        ])
        ->assertRedirect();

    $paths = HackatonCertificate::query()
        ->where('hackaton_id', $hackaton->id)
        ->where('title', 'Separate Files')
        ->pluck('file_path');

    expect($paths)->toHaveCount(2)
        ->and($paths->unique())->toHaveCount(2);
});

test('non organizer cannot issue certificate', function () {
    Storage::fake('local');

    $organizer = User::factory()->partner()->create();
    $outsider = User::factory()->partner()->create();
    $recipient = User::factory()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create();

    actingAs($outsider)
        ->from(route('hackatons.show', $hackaton))
        ->post(route('hackatons.certificates.store', $hackaton), [
            'user_id' => $recipient->id,
            'title' => 'No way',
            'file' => UploadedFile::fake()->create('cert.pdf', 100, 'application/pdf'),
        ])
        ->assertForbidden();
});

test('recipient can download own certificate', function () {
    $disk = Storage::fake('local');

    $organizer = User::factory()->partner()->create();
    $recipient = User::factory()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create();
    $disk->put('hackaton_certificates/recipient.pdf', 'binary-pdf');
    $certificate = HackatonCertificate::factory()->create([
        'hackaton_id' => $hackaton->id,
        'user_id' => $recipient->id,
        'file_path' => 'hackaton_certificates/recipient.pdf',
    ]);

    actingAs($recipient)
        ->get(route('certificates.download', $certificate))
        ->assertSuccessful();
});

test('outsider cannot download a certificate', function () {
    $disk = Storage::fake('local');

    $organizer = User::factory()->partner()->create();
    $recipient = User::factory()->create();
    $outsider = User::factory()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create();
    $disk->put('hackaton_certificates/outsider.pdf', 'binary-pdf');
    $certificate = HackatonCertificate::factory()->create([
        'hackaton_id' => $hackaton->id,
        'user_id' => $recipient->id,
        'file_path' => 'hackaton_certificates/outsider.pdf',
    ]);

    actingAs($outsider)
        ->get(route('certificates.download', $certificate))
        ->assertForbidden();
});

test('organizer can delete certificate and underlying file', function () {
    $disk = Storage::fake('local');

    $organizer = User::factory()->partner()->create();
    $recipient = User::factory()->create();
    $hackaton = Hackaton::factory()->for($organizer)->create();
    $disk->put('hackaton_certificates/to-delete.pdf', 'binary-pdf');
    $certificate = HackatonCertificate::factory()->create([
        'hackaton_id' => $hackaton->id,
        'user_id' => $recipient->id,
        'file_path' => 'hackaton_certificates/to-delete.pdf',
    ]);

    actingAs($organizer)
        ->from(route('hackatons.show', $hackaton))
        ->delete(route('hackatons.certificates.destroy', [$hackaton, $certificate]))
        ->assertRedirect()
        ->assertSessionHas('success');

    expect(HackatonCertificate::query()->whereKey($certificate->id)->exists())->toBeFalse();
    $disk->assertMissing('hackaton_certificates/to-delete.pdf');
});

test('destroy returns 404 if certificate does not belong to hackaton', function () {
    Storage::fake('local');

    $organizer = User::factory()->partner()->create();
    $other = User::factory()->partner()->create();
    $recipient = User::factory()->create();
    $hackatonA = Hackaton::factory()->for($organizer)->create();
    $hackatonB = Hackaton::factory()->for($other)->create();
    $certificate = HackatonCertificate::factory()->create([
        'hackaton_id' => $hackatonB->id,
        'user_id' => $recipient->id,
    ]);

    actingAs($organizer)
        ->from(route('hackatons.show', $hackatonA))
        ->delete(route('hackatons.certificates.destroy', [$hackatonA, $certificate]))
        ->assertNotFound();
});
