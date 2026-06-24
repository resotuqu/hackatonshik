<?php

use App\Models\User;
use Spatie\Activitylog\Models\Activity;

test('authenticated user can download account data pdf', function () {
    $user = User::factory()->create([
        'pd_consent_accepted_at' => now(),
    ]);

    $response = $this->actingAs($user)->get(route('profile.export'));

    $response->assertOk()
        ->assertHeader('Content-Type', 'application/pdf');

    expect($response->headers->get('Content-Disposition'))
        ->toContain('.pdf');
});

test('export logs activity for audit trail', function () {
    $user = User::factory()->create([
        'pd_consent_accepted_at' => now(),
    ]);

    $this->actingAs($user)->get(route('profile.export'));

    $log = Activity::where('description', 'exported_account_data')
        ->where('causer_id', $user->id)
        ->where('subject_id', $user->id)
        ->first();

    expect($log)->not->toBeNull()
        ->and($log->properties->get('ip'))->not->toBeNull();
});

test('guest cannot access account data export', function () {
    $this->get(route('profile.export'))
        ->assertRedirect(route('login'));
});
