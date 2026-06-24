<?php

use App\Models\AvatarPresetPack;
use App\Models\User;

test('admin avatar presets page is forbidden for non-admin', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('filament.admin.resources.avatar-presets.index'))
        ->assertForbidden();
});

test('admin can access avatar presets filament resource', function () {
    $admin = User::factory()->admin()->create();
    $pack = AvatarPresetPack::factory()->create(['slug' => 'filament-pack']);

    $this->actingAs($admin)
        ->get(route('filament.admin.resources.avatar-presets.edit', $pack))
        ->assertOk()
        ->assertSee('Аватарки');
});

test('moderator cannot access avatar presets filament resource', function () {
    $moderator = User::factory()->moderator()->create();

    $this->actingAs($moderator)
        ->get(route('filament.admin.resources.avatar-presets.index'))
        ->assertForbidden();
});
