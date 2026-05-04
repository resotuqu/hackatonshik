<?php

use App\Models\AvatarPresetPack;
use App\Models\User;
use Livewire\Livewire;

test('admin avatar presets page is forbidden for non-admin', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get('/admin/avatar-presets')->assertForbidden();
});

test('admin can create avatar preset pack', function () {
    $admin = User::factory()->admin()->create();

    Livewire::actingAs($admin)
        ->test('pages::admin.avatar-presets')
        ->set('new_pack_name', 'Тестовый пак')
        ->set('new_pack_slug', 'test-pack-admin')
        ->set('new_pack_sort_order', 5)
        ->call('createPack')
        ->assertHasNoErrors();

    expect(AvatarPresetPack::query()->where('slug', 'test-pack-admin')->exists())->toBeTrue();
});
